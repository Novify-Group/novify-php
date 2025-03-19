<?php

namespace App\Services;

use App\Contracts\Services\SMSServiceContract;
use App\Helpers\ImageHelper;
use App\Models\Merchant;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\WalletService;
use App\Http\Requests\Wallet\WalletCreateRequest;

class MerchantService
{
    use ApiResponse;

    protected $smsService;
    protected $walletService;

    public function __construct(
        SMSServiceContract $smsService,
        WalletService $walletService
    ) {
        $this->smsService = $smsService;
        $this->walletService = $walletService;
    }

    /**
     * Register a new merchant
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Handle image uploads
            $idPicturePath = ImageHelper::saveBase64Image($data['id_picture'], 'merchant_ids');
            $passportPhotoPath = ImageHelper::saveBase64Image($data['passport_photo'], 'merchant_passports');
            $storeLogoPath = ImageHelper::saveBase64Image($data['store_logo'] ?? null, 'store_logos');

            $merchant = Merchant::create([
                'country_id' => $data['country_id'],
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'],
                'last_name' => $data['last_name'],
                'dob' => $data['dob'],
                'id_type' => $data['id_type'],
                'id_number' => $data['id_number'],
                'id_picture_path' => $idPicturePath,
                'passport_photo_path' => $passportPhotoPath,
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'store_name' => $data['store_name'],
                'store_logo_path' => $storeLogoPath,
                'store_description' => $data['store_description'] ?? null,
                'licence_number' => $data['licence_number'] ?? null,
                'tax_id_number' => $data['tax_id_number'] ?? null,
                'is_licenced' => $data['is_licenced'] ?? false,
                'date_started' => $data['date_started'] ?? null,
                'market_area_id' => $data['market_area_id'] ?? null,
            ]);

            // Load the country relationship before creating wallet
            $merchant->load('country');

            // Create default wallet
            $this->walletService->createDefaultWallet($merchant);
            $otp = $this->generateAndSaveOtp($merchant);
            $this->sendOtpViaSMS($merchant, $otp);

            $token = auth('api')->login($merchant);

            return $this->successResponse(
                ['token' => $token],
                'Registration successful. Please verify your phone number with the OTP sent.',
                201
            );
        });
    }

    //create wallet for a merchant
    public function createWallet(WalletCreateRequest $request): array
    {
        return $this->successResponse(
            $this->walletService->createWallet($request->user(), $request->validated()),
            'Wallet created successfully'
        );
    }

    /**
     * Authenticate a merchant
     */
    public function login(string $username, string $password): array
    {
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        
        $credentials = [
            $field => $username,
            'password' => $password
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return DB::transaction(function () use ($token) {
            
            $merchant = auth('api')->user();
            $otp = $this->generateAndSaveOtp($merchant);
            $this->sendOtpViaSMS($merchant, $otp);

            if ($merchant->is_verified) {
                return $this->successResponse([
                    'requires_otp' => true,
                    'token' => $token
                ], 'OTP sent to your phone number');
            }

            return $this->successResponse([
                'requires_verification' => true,
                'token' => $token
            ], 'Please verify your account first', 403);
        });
    }

    /**
     * Verify OTP for merchant
     */
    public function verifyOtp(Merchant $merchant, string $otp): array
    {
        if ($merchant->otp !== $otp || now()->gt($merchant->otp_expires_at)) {
            return $this->errorResponse('Invalid or expired OTP', 400);
        }

        $merchant->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null
        ]);

        // Load relationships including branches
        $merchant->load(['country', 'wallets.currency', 'marketArea', 'branches']);

        return $this->successResponse([
            'merchant' => [
                'id' => $merchant->id,
                'first_name' => $merchant->first_name,
                'middle_name' => $merchant->middle_name,
                'last_name' => $merchant->last_name,
                'email' => $merchant->email,
                'phone_number' => $merchant->phone_number,
                'store_name' => $merchant->store_name,
                'store_description' => $merchant->store_description,
                'store_logo_path' => $merchant->store_logo_path,
                'is_licenced' => $merchant->is_licenced,
                'licence_number' => $merchant->licence_number,
                'tax_id_number' => $merchant->tax_id_number,
                'date_started' => $merchant->date_started,
                'is_verified' => $merchant->is_verified,
                'is_active' => $merchant->is_active,
                'country' => $merchant->country,
                'market_area' => $merchant->marketArea,
                'wallets' => $merchant->wallets->map(function ($wallet) {
                    return [
                        'id' => $wallet->id,
                        'name' => $wallet->name,
                        'wallet_number' => $wallet->wallet_number,
                        'balance' => $wallet->balance,
                        'currency_code' => $wallet->currency_code,
                        'type' => $wallet->type,
                        'is_default' => $wallet->is_default
                    ];
                }),
                'branches' => $merchant->branches->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'phone_number' => $branch->phone_number,
                        'email' => $branch->email,
                        'address' => $branch->address,
                        'city' => $branch->city,
                        'is_main_branch' => $branch->is_main_branch,
                        'is_active' => $branch->is_active
                    ];
                })
            ]
        ], 'OTP verified successfully');
    }

    /**
     * Resend OTP to merchant
     */
    public function resendOtp(Merchant $merchant): array
    {
        return DB::transaction(function () use ($merchant) {
            $otp = $this->generateAndSaveOtp($merchant);
            $this->sendOtpViaSMS($merchant, $otp);

            return $this->successResponse(null, 'OTP resent successfully');
        });
    }

    /**
     * Send OTP via SMS
     */
    private function sendOtpViaSMS(Merchant $merchant, string $otp): void
    {
        $message = "Your OTP is: {$otp}. Valid for 10 minutes.";
        $this->smsService->send($merchant->phone_number, $message);
    }

    /**
     * Generate and save OTP for merchant
     */
    private function generateAndSaveOtp(Merchant $merchant): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $merchant->update([
            'otp' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        // Send OTP via SMS
        $this->sendOtpViaSMS($merchant, $otp);

        return $otp;
    }
} 
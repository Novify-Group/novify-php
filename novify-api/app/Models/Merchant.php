<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Merchant extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'country_id',
        'first_name',
        'middle_name',
        'last_name',
        'dob',
        'id_type',
        'id_number',
        'id_picture_path',
        'passport_photo_path',
        'phone_number',
        'email',
        'password',
        'store_name',
        'store_logo_path',
        'store_description',
        'licence_number',
        'tax_id_number',
        'is_licenced',
        'date_started',
        'market_area_id',
        'otp',
        'otp_expires_at'
    ];

    protected $hidden = [
        'password',
        'otp'
    ];

    protected $casts = [
        'dob' => 'date',
        'date_started' => 'date',
        'is_licenced' => 'boolean',
        'otp_expires_at' => 'datetime',
        'is_verified' => 'boolean'
    ];
    protected $appends = ['merchant_number'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(MerchantUser::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function productMeasureUnits()
    {
        return $this->hasMany(ProductMeasureUnit::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function marketArea()
    {
        return $this->belongsTo(MarketArea::class);
    }

    public function getMerchantNumberAttribute(): string{
        return str_pad($this->id, 7, '0', STR_PAD_LEFT);
    }
} 
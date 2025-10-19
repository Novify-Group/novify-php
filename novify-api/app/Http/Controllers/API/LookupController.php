<?php

namespace App\Http\Controllers\API;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class LookupController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/lookup/countries",
     *     tags={"Lookup"},
     *     summary="Get countries list",
     *     description="Get list of active countries with their details",
     *     @OA\Response(
     *         response=200,
     *         description="Countries retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="United States"),
     *                 @OA\Property(property="code", type="string", example="US"),
     *                 @OA\Property(property="phone_code", type="string", example="+1"),
     *                 @OA\Property(property="currency_code", type="string", example="USD"),
     *                 @OA\Property(property="currency_symbol", type="string", example="$")
     *             ))
     *         )
     *     )
     * )
     */
    public function countries(): JsonResponse
    {
        return $this->execute(function () {
            $countries = Country::where('is_active', true)
                ->select('id', 'name', 'code', 'phone_code', 'currency_code', 'currency_symbol')
                ->orderBy('name')
                ->get();

            return $this->successResponse( $countries);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/lookup/currencies",
     *     tags={"Lookup"},
     *     summary="Get currencies list",
     *     description="Get list of active currencies",
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Currencies retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="US Dollar"),
     *                 @OA\Property(property="code", type="string", example="USD"),
     *                 @OA\Property(property="symbol", type="string", example="$")
     *             ))
     *         )
     *     )
     * )
     */
    public function currencies(): JsonResponse
    {
        return $this->execute(function () {
            $currencies = Currency::where('is_active', true)
                ->select('id', 'name', 'code', 'symbol')
                ->orderBy('id')
                ->get();

            return $this->successResponse($currencies);
        });
    }
} 
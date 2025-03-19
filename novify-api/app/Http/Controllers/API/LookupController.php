<?php

namespace App\Http\Controllers\API;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class LookupController extends BaseApiController
{
    /**
     * Get list of countries
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
     * Get list of currencies
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
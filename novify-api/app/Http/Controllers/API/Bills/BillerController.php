<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\Biller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillerController extends BaseApiController
{
    public function index(Request $request)
    {
        $billers = Biller::when($request->category_id, function($query, $categoryId) {
            return $query->where('bill_category_id', $categoryId);
        })->with('billerItems')->get();

        return $this->successResponse($billers);
    }

    public function show(Biller $biller)
    {
        return $this->successResponse($biller->load('billerItems'));
    }
} 
<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\BillCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillCategoryController  extends BaseApiController
{
    public function index()
    {
        $categories = BillCategory::with('billers')->get();
        return response()->json($categories);
    }

    public function show(BillCategory $category)
    {
        return response()->json($category->load('billers'));
    }
} 
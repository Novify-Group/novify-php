<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\BillCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class BillCategoryController  extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/bills/categories",
     *     tags={"Bills"},
     *     summary="Get bill categories",
     *     description="Get list of bill payment categories with their billers",
     *     @OA\Response(
     *         response=200,
     *         description="Bill categories retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Utilities"),
     *                 @OA\Property(property="description", type="string", example="Utility bill payments"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="billers", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = BillCategory::with('billers')->get();
        return response()->json($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/bills/categories/{category}",
     *     tags={"Bills"},
     *     summary="Get specific bill category",
     *     description="Get details of a specific bill category with its billers",
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Utilities"),
     *             @OA\Property(property="description", type="string", example="Utility bill payments"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="billers", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function show(BillCategory $category)
    {
        return response()->json($category->load('billers'));
    }
} 
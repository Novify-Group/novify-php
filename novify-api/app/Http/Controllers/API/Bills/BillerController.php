<?php

namespace App\Http\Controllers\API\Bills;

use App\Http\Controllers\API\BaseApiController;
use App\Models\Biller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class BillerController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/bills/billers",
     *     tags={"Bills"},
     *     summary="Get billers list",
     *     description="Get list of billers with optional category filtering",
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by bill category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Billers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Electric Company"),
     *                 @OA\Property(property="code", type="string", example="ELEC001"),
     *                 @OA\Property(property="logo", type="string", example="logo_url"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="biller_items", type="array", @OA\Items(type="object"))
     *             ))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $billers = Biller::when($request->category_id, function($query, $categoryId) {
            return $query->where('bill_category_id', $categoryId);
        })->with('billerItems')->get();

        return $this->successResponse($billers);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/bills/billers/{biller}",
     *     tags={"Bills"},
     *     summary="Get specific biller",
     *     description="Get details of a specific biller with its items",
     *     @OA\Parameter(
     *         name="biller",
     *         in="path",
     *         required=true,
     *         description="Biller ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Biller retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Electric Company"),
     *                 @OA\Property(property="code", type="string", example="ELEC001"),
     *                 @OA\Property(property="logo", type="string", example="logo_url"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="biller_items", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function show(Biller $biller)
    {
        return $this->successResponse($biller->load('billerItems'));
    }
} 
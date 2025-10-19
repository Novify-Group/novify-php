<?php

namespace App\Http\Controllers\API\Merchant;

use App\Http\Controllers\API\BaseApiController;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class BranchController extends BaseApiController
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/merchant/branches",
     *     tags={"Merchants"},
     *     summary="Create new branch",
     *     description="Create a new branch for the merchant",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone_number", "address", "city"},
     *             @OA\Property(property="name", type="string", example="Downtown Branch"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", format="email", example="downtown@store.com"),
     *             @OA\Property(property="address", type="string", example="123 Main Street"),
     *             @OA\Property(property="city", type="string", example="New York"),
     *             @OA\Property(property="is_main_branch", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Branch created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Branch created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="branch", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|unique:branches,phone_number',
                'email' => 'nullable|email|unique:branches,email',
                'address' => 'required|string',
                'city' => 'required|string',
                'is_main_branch' => 'nullable|boolean'
            ]);

            return $this->branchService->create($request->user(), $request->all());
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/branches",
     *     tags={"Merchants"},
     *     summary="List branches",
     *     description="Get list of branches for the merchant",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of branches per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branches retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="branches", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $perPage = $request->input('per_page', 20);
            return $this->branchService->list($request->user(), $perPage);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/branches/{branch}",
     *     tags={"Merchants"},
     *     summary="Get specific branch",
     *     description="Get details of a specific branch with its staff members",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         description="Branch ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="branch", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function show(Branch $branch): JsonResponse
    {
        return $this->execute(function () use ($branch) {
            return $this->successResponse(['branch' => $branch->load('users')]);
        });
    }

    /**
     * @OA\Put(
     *     path="/api/v1/merchant/branches/{branch}",
     *     tags={"Merchants"},
     *     summary="Update branch",
     *     description="Update details of a specific branch",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         description="Branch ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Branch Name"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", format="email", example="updated@store.com"),
     *             @OA\Property(property="address", type="string", example="456 New Street"),
     *             @OA\Property(property="city", type="string", example="Los Angeles"),
     *             @OA\Property(property="is_main_branch", type="boolean", example=false),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Branch updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="branch", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, Branch $branch): JsonResponse
    {
        return $this->execute(function () use ($request, $branch) {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone_number' => 'sometimes|string|unique:branches,phone_number,' . $branch->id,
                'email' => 'nullable|email|unique:branches,email,' . $branch->id,
                'address' => 'sometimes|string',
                'city' => 'sometimes|string',
                'is_main_branch' => 'sometimes|boolean',
                'is_active' => 'sometimes|boolean'
            ]);

            return $this->branchService->update($branch, $request->all());
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/merchant/branches/{branch}",
     *     tags={"Merchants"},
     *     summary="Delete branch",
     *     description="Delete a specific branch",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         description="Branch ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Branch deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy(Branch $branch): JsonResponse
    {
        return $this->execute(function () use ($branch) {
            return $this->branchService->delete($branch);
        });
    }
} 
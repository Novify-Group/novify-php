<?php

namespace App\Http\Controllers\API\Merchant;

use App\Http\Controllers\API\BaseApiController;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends BaseApiController
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

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

    public function index(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $perPage = $request->input('per_page', 20);
            return $this->branchService->list($request->user(), $perPage);
        });
    }

    public function show(Branch $branch): JsonResponse
    {
        return $this->execute(function () use ($branch) {
            return $this->successResponse(['branch' => $branch->load('users')]);
        });
    }

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

    public function destroy(Branch $branch): JsonResponse
    {
        return $this->execute(function () use ($branch) {
            return $this->branchService->delete($branch);
        });
    }
} 
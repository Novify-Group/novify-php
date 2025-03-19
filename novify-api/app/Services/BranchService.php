<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Merchant;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class BranchService
{
    use ApiResponse;

    public function create(Merchant $merchant, array $data): array
    {
        return DB::transaction(function () use ($merchant, $data) {
            // If this is the first branch or marked as main, ensure no other main branch exists
            if (($merchant->branches()->count() === 0) || ($data['is_main_branch'] ?? false)) {
                $merchant->branches()->update(['is_main_branch' => false]);
                $data['is_main_branch'] = true;
            }

            $branch = $merchant->branches()->create($data);

            return $this->successResponse(
                 $branch,
                'Branch created successfully',
                201
            );
        });
    }

    public function list(Merchant $merchant, int $perPage = 20): array
    {
        $branches = $merchant->branches()
            ->with('users')
            ->paginate($perPage);
        
        return $this->successResponse( $branches);
    }

    public function update(Branch $branch, array $data): array
    {
        return DB::transaction(function () use ($branch, $data) {
            // Handle main branch changes if necessary
            if (isset($data['is_main_branch']) && $data['is_main_branch']) {
                $branch->merchant->branches()
                    ->where('id', '!=', $branch->id)
                    ->update(['is_main_branch' => false]);
            }

            $branch->update($data);

            return $this->successResponse( $branch->fresh(), 'Branch updated successfully');
        });
    }

    public function delete(Branch $branch): array
    {
        if ($branch->is_main_branch) {
            return $this->errorResponse('Cannot delete main branch', 400);
        }

        $branch->delete();

        return $this->successResponse(null, 'Branch deleted successfully');
    }
} 
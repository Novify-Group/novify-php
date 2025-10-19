<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Merchant;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of branches
     */
    public function index()
    {
        $merchant = Auth::user();
        $branches = $merchant->branches()->with('users')->get();
        
        return view('templates.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch
     */
    public function create()
    {
        return view('templates.branches.create');
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'is_main_branch' => 'boolean',
        ]);

        $merchant = Auth::user();
        $result = $this->branchService->create($merchant, $request->all());

        if ($result['success']) {
            return redirect()->route('ui.branches.index')
                ->with('success', 'Branch created successfully');
        }

        return back()->with('error', $result['message'] ?? 'Failed to create branch');
    }

    /**
     * Display the specified branch
     */
    public function show(Branch $branch)
    {
        // Ensure the branch belongs to the authenticated merchant
        if ($branch->merchant_id !== Auth::id()) {
            abort(403);
        }

        $branch->load('users');
        return view('templates.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch
     */
    public function edit(Branch $branch)
    {
        // Ensure the branch belongs to the authenticated merchant
        if ($branch->merchant_id !== Auth::id()) {
            abort(403);
        }

        return response()->json($branch);
    }

    /**
     * Update the specified branch
     */
    public function update(Request $request, Branch $branch)
    {
        // Ensure the branch belongs to the authenticated merchant
        if ($branch->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'is_main_branch' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $result = $this->branchService->update($branch, $request->all());

        if ($result['success']) {
            return redirect()->route('ui.branches.index')
                ->with('success', 'Branch updated successfully');
        }

        return back()->with('error', $result['message'] ?? 'Failed to update branch');
    }

    /**
     * Remove the specified branch
     */
    public function destroy(Branch $branch)
    {
        // Ensure the branch belongs to the authenticated merchant
        if ($branch->merchant_id !== Auth::id()) {
            abort(403);
        }

        $result = $this->branchService->delete($branch);

        if ($result['success']) {
            return redirect()->route('ui.branches.index')
                ->with('success', 'Branch deleted successfully');
        }

        return back()->with('error', $result['message'] ?? 'Failed to delete branch');
    }
}

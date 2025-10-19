<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Biller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    /**
     * Display bill categories
     */
    public function index()
    {
        $merchant = Auth::user();
        $categories = BillCategory::all();
        $recentBills = Bill::where('merchant_id', $merchant->id)
            ->latest()
            ->take(10)
            ->get();
        
        return view('templates.bills.index', compact('categories', 'recentBills'));
    }

    /**
     * Show bill categories
     */
    public function categories()
    {
        $categories = BillCategory::with('billers')->get();
        return view('templates.bills.categories', compact('categories'));
    }

    /**
     * Show billers for a category
     */
    public function showBillers(BillCategory $category)
    {
        $billers = $category->billers()->get();
        return view('templates.bills.billers', compact('category', 'billers'));
    }

    /**
     * Show bill payment form
     */
    public function create()
    {
        $categories = BillCategory::all();
        $billers = Biller::all();
        
        return view('templates.bills.create', compact('categories', 'billers'));
    }

    /**
     * Process bill payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:bill_categories,id',
            'biller_id' => 'required|exists:billers,id',
            'account_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $merchant = Auth::user();
        
        $bill = Bill::create([
            'merchant_id' => $merchant->id,
            'category_id' => $request->category_id,
            'biller_id' => $request->biller_id,
            'account_number' => $request->account_number,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('ui.bills.index')
            ->with('success', 'Bill payment initiated successfully');
    }

    /**
     * Show bill details
     */
    public function show(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated merchant
        if ($bill->merchant_id !== Auth::id()) {
            abort(403);
        }

        return view('templates.bills.show', compact('bill'));
    }

    /**
     * Show bill edit form
     */
    public function edit(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated merchant
        if ($bill->merchant_id !== Auth::id()) {
            abort(403);
        }

        $categories = BillCategory::all();
        $billers = Biller::all();
        
        return view('templates.bills.edit', compact('bill', 'categories', 'billers'));
    }

    /**
     * Update bill
     */
    public function update(Request $request, Bill $bill)
    {
        // Ensure the bill belongs to the authenticated merchant
        if ($bill->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:bill_categories,id',
            'biller_id' => 'required|exists:billers,id',
            'account_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $bill->update($request->all());

        return redirect()->route('ui.bills.show', $bill)
            ->with('success', 'Bill updated successfully');
    }

    /**
     * Delete bill
     */
    public function destroy(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated merchant
        if ($bill->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Only allow deletion of pending bills
        if ($bill->status !== 'pending') {
            return back()->with('error', 'Only pending bills can be deleted');
        }

        $bill->delete();

        return redirect()->route('ui.bills.index')
            ->with('success', 'Bill deleted successfully');
    }
}

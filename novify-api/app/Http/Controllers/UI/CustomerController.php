<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index()
    {
        $merchant = Auth::user();
        $customers = Customer::where('merchant_id', $merchant->id)
            ->withCount('orders')
            ->latest()
            ->paginate(20);
        
        return view('templates.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('templates.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
        ]);

        $merchant = Auth::user();
        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'city' => $request->city,
        ]);

        return redirect()->route('ui.customers.index')
            ->with('success', 'Customer created successfully');
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        // Ensure the customer belongs to the authenticated merchant
        if ($customer->merchant_id !== Auth::id()) {
            abort(403);
        }

        $customer->load(['orders' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return view('templates.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        // Ensure the customer belongs to the authenticated merchant
        if ($customer->merchant_id !== Auth::id()) {
            abort(403);
        }

        return view('templates.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        // Ensure the customer belongs to the authenticated merchant
        if ($customer->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
        ]);

        $customer->update($request->all());

        return redirect()->route('ui.customers.index')
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        // Ensure the customer belongs to the authenticated merchant
        if ($customer->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete customer with existing orders');
        }

        $customer->delete();

        return redirect()->route('ui.customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}

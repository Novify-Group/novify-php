<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index()
    {
        $merchant = Auth::user();
        $orders = Order::where('merchant_id', $merchant->id)
            ->with(['customer'])
            ->latest()
            ->paginate(20);
        
        return view('templates.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $merchant = Auth::user();
        $products = Product::where('merchant_id', $merchant->id)
            ->where('is_active', true)
            ->get();
        $customers = Customer::where('merchant_id', $merchant->id)->get();
        
        return view('templates.orders.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $merchant = Auth::user();
        
        // Calculate totals
        $subtotal = 0;
        $tax_amount = 0;
        $discount_amount = 0;
        
        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $lineTotal = $product->selling_price * $item['quantity'];
            $subtotal += $lineTotal;
            
            // Calculate tax if applicable
            if ($product->is_taxable && $product->tax_percentage) {
                $tax_amount += ($lineTotal * $product->tax_percentage / 100);
            }
        }
        
        $total_amount = $subtotal + $tax_amount - $discount_amount;
        
        // Create order
        $order = Order::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $request->customer_id,
            'order_number' => 'ORD-' . str_pad(Order::where('merchant_id', $merchant->id)->count() + 1, 6, '0', STR_PAD_LEFT),
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'discount_amount' => $discount_amount,
            'total_amount' => $total_amount,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);
        
        // Create order items
        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->selling_price,
                'total_price' => $product->selling_price * $item['quantity'],
            ]);
            
            // Update stock if inventory is tracked
            if ($product->is_inventory_tracked) {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }
        
        return redirect()->route('ui.orders.index')
            ->with('success', 'Order created successfully');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated merchant
        if ($order->merchant_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['customer', 'items.product']);
        return view('templates.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        // Ensure the order belongs to the authenticated merchant
        if ($order->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Only allow editing of pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('ui.orders.show', $order)
                ->with('error', 'Only pending orders can be edited');
        }

        $merchant = Auth::user();
        $products = Product::where('merchant_id', $merchant->id)
            ->where('is_active', true)
            ->get();
        $customers = Customer::where('merchant_id', $merchant->id)->get();
        
        return view('templates.orders.edit', compact('order', 'products', 'customers'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        // Ensure the order belongs to the authenticated merchant
        if ($order->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Only allow updating of pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('ui.orders.show', $order)
                ->with('error', 'Only pending orders can be updated');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update($request->only(['status', 'notes']));

        return redirect()->route('ui.orders.show', $order)
            ->with('success', 'Order updated successfully');
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        // Ensure the order belongs to the authenticated merchant
        if ($order->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Only allow deletion of pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('ui.orders.show', $order)
                ->with('error', 'Only pending orders can be deleted');
        }

        $order->delete();

        return redirect()->route('ui.orders.index')
            ->with('success', 'Order deleted successfully');
    }
}

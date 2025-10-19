<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMeasureUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index()
    {
        $merchant = Auth::user();
        $products = Product::where('merchant_id', $merchant->id)
            ->with(['category', 'measure_unit'])
            ->latest()
            ->paginate(20);
        
        return view('templates.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $merchant = Auth::user();
        $categories = ProductCategory::where('merchant_id', $merchant->id)->get();
        $measureUnits = ProductMeasureUnit::where('merchant_id', $merchant->id)->get();
        
        return view('templates.products.create', compact('categories', 'measureUnits'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:cost_price',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_measure_unit_id' => 'required|exists:product_measure_units,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_inventory_tracked' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'is_taxable' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $merchant = Auth::user();
        $data = $request->all();
        $data['merchant_id'] = $merchant->id;

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('products', 'public');
            $data['featured_image'] = $imagePath;
        }

        $product = Product::create($data);

        return redirect()->route('ui.products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        // Ensure the product belongs to the authenticated merchant
        if ($product->merchant_id !== Auth::id()) {
            abort(403);
        }

        $product->load(['category', 'measure_unit', 'variants', 'images']);
        return view('templates.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        // Ensure the product belongs to the authenticated merchant
        if ($product->merchant_id !== Auth::id()) {
            abort(403);
        }

        $merchant = Auth::user();
        $categories = ProductCategory::where('merchant_id', $merchant->id)->get();
        $measureUnits = ProductMeasureUnit::where('merchant_id', $merchant->id)->get();
        
        return view('templates.products.edit', compact('product', 'categories', 'measureUnits'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        // Ensure the product belongs to the authenticated merchant
        if ($product->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:cost_price',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_measure_unit_id' => 'required|exists:product_measure_units,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_inventory_tracked' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'is_taxable' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = $request->all();

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($product->featured_image) {
                Storage::disk('public')->delete($product->featured_image);
            }
            
            $imagePath = $request->file('featured_image')->store('products', 'public');
            $data['featured_image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->route('ui.products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Ensure the product belongs to the authenticated merchant
        if ($product->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Delete featured image if exists
        if ($product->featured_image) {
            Storage::disk('public')->delete($product->featured_image);
        }

        $product->delete();

        return redirect()->route('ui.products.index')
            ->with('success', 'Product deleted successfully');
    }

    /**
     * Display product categories
     */
    public function categories()
    {
        $merchant = Auth::user();
        $categories = ProductCategory::where('merchant_id', $merchant->id)->get();
        
        return view('templates.products.categories', compact('categories'));
    }

    /**
     * Display product measure units
     */
    public function measureUnits()
    {
        $merchant = Auth::user();
        $measureUnits = ProductMeasureUnit::where('merchant_id', $merchant->id)->get();
        
        return view('templates.products.measure-units', compact('measureUnits'));
    }

    /**
     * Store product image
     */
    public function storeImage(Request $request, Product $product)
    {
        // Ensure the product belongs to the authenticated merchant
        if ($product->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');
        
        $product->images()->create([
            'image_path' => $imagePath,
            'order' => $product->images()->count() + 1,
        ]);

        return back()->with('success', 'Image uploaded successfully');
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, Product $product)
    {
        // Ensure the product belongs to the authenticated merchant
        if ($product->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'stock_quantity' => $request->stock_quantity,
            'min_stock_level' => $request->min_stock_level,
            'is_inventory_low' => $request->stock_quantity <= ($request->min_stock_level ?? 0),
            'is_inventory_out' => $request->stock_quantity == 0,
        ]);

        return back()->with('success', 'Stock updated successfully');
    }
}

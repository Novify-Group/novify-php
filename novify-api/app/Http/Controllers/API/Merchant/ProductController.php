<?php

namespace App\Http\Controllers\API\Merchant;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMeasureUnit;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->productService->create($request->user(), $request->validated());
        });
    }

    public function index(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $perPage = $request->input('per_page', 20);
            return $this->productService->list($request->all(), $perPage);
        });
    }

    public function show(Product $product): JsonResponse
    {
        return $this->execute(function () use ($product) {
            return $this->successResponse([
                'product' => $product->load(['category', 'variants', 'images'])
            ]);
        });
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        return $this->execute(function () use ($request, $product) {
            return $this->productService->update($product, $request->validated());
        });
    }

    public function destroy(Product $product): JsonResponse
    {
        return $this->execute(function () use ($product) {
            return $this->productService->delete($product);
        });
    }

    // Categories
    public function categories(Request $request): JsonResponse
    {
      
        return $this->execute(function () use ($request) {
            $request['is_active'] = 1;
            return $this->productService->listCategories($request->all(), $request->input('per_page', 20));
        });
    }

    public function storeCategory(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'temp_category_id' => 'required|exists:temp_categories,id',
                'name' => 'required|string|max:255',
                'image' => 'nullable|string', // base64
                'description' => 'nullable|string'
            ]);

            return $this->productService->createCategory($request->user(), $request->all());
        });
    }

    public function showCategory(ProductCategory $category): JsonResponse
    {
        return $this->execute(function () use ($category) {
            return $this->successResponse( $category->load('products'));
        });
    }

    public function updateCategory(Request $request, ProductCategory $category): JsonResponse
    {
        return $this->execute(function () use ($request, $category) {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'image' => 'nullable|string', // base64
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            return $this->productService->updateCategory($category, $request->all());
        });
    }

    public function deleteCategory(ProductCategory $category): JsonResponse
    {
        return $this->execute(function () use ($category) {
            return $this->productService->deleteCategory($category);
        });
    }

    // Measure Units
    public function measureUnits(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request['is_active'] = 1;
            return $this->productService->listMeasureUnits($request->all());
        });
    }

    public function storeMeasureUnit(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'temp_measure_unit_id' => 'required|exists:temp_measure_units,id',
                'name' => 'required|string|max:255',
                'symbol' => 'nullable|string|max:10'
            ]);

            return $this->productService->createMeasureUnit($request->user(), $request->all());
        });
    }

    public function showMeasureUnit(ProductMeasureUnit $unit): JsonResponse
    {
        return $this->execute(function () use ($unit) {
            return $this->successResponse(['measure_unit' => $unit->load('products')]);
        });
    }

    public function updateMeasureUnit(Request $request, ProductMeasureUnit $unit): JsonResponse
    {
        return $this->execute(function () use ($request, $unit) {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'symbol' => 'nullable|string|max:10',
                'is_active' => 'boolean'
            ]);

            return $this->productService->updateMeasureUnit($unit, $request->all());
        });
    }

    public function deleteMeasureUnit(ProductMeasureUnit $unit): JsonResponse
    {
        return $this->execute(function () use ($unit) {
            return $this->productService->deleteMeasureUnit($unit);
        });
    }

    // Variants
    public function variants(Product $product): JsonResponse
    {
        return $this->execute(function () use ($product) {
            return $this->successResponse(['variants' => $product->variants]);
        });
    }

    public function storeVariant(Request $request, Product $product): JsonResponse
    {
        return $this->execute(function () use ($request, $product) {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|unique:product_variants,sku',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0|gte:cost_price',
                'stock_quantity' => 'required|integer|min:0',
                'min_stock_level' => 'required|integer|min:0',
                'attributes' => 'required|array'
            ]);

            return $this->productService->createVariant($product, $request->all());
        });
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant): JsonResponse
    {
        return $this->execute(function () use ($request, $product, $variant) {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'sku' => 'sometimes|string|unique:product_variants,sku,' . $variant->id,
                'cost_price' => 'sometimes|numeric|min:0',
                'selling_price' => 'sometimes|numeric|min:0|gte:cost_price',
                'stock_quantity' => 'sometimes|integer|min:0',
                'min_stock_level' => 'sometimes|integer|min:0',
                'attributes' => 'sometimes|array',
                'is_active' => 'boolean'
            ]);

            return $this->productService->updateVariant($variant, $request->all());
        });
    }

    public function deleteVariant(Product $product, ProductVariant $variant): JsonResponse
    {
        return $this->execute(function () use ($variant) {
            return $this->productService->deleteVariant($variant);
        });
    }

    // Images
    public function storeImage(Request $request, Product $product): JsonResponse
    {
        return $this->execute(function () use ($request, $product) {
            $request->validate([
                'image_url' => 'required|string', // base64
                'variant_id' => 'nullable|exists:product_variants,id',
                'is_featured' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            return $this->productService->createImage($product, $request->all());
        });
    }

    public function updateImage(Request $request, Product $product, ProductImage $image): JsonResponse
    {
        return $this->execute(function () use ($request, $image) {
            $request->validate([
                'image_url' => 'sometimes|string', // base64
                'is_featured' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            return $this->productService->updateImage($image, $request->all());
        });
    }

    public function deleteImage(Product $product, ProductImage $image): JsonResponse
    {
        return $this->execute(function () use ($image) {
            return $this->productService->deleteImage($image);
        });
    }

    public function reorderImages(Request $request, Product $product): JsonResponse
    {
        return $this->execute(function () use ($request, $product) {
            $request->validate([
                'images' => 'required|array',
                'images.*.id' => 'required|exists:product_images,id',
                'images.*.sort_order' => 'required|integer|min:0'
            ]);

            return $this->productService->reorderImages($product, $request->images);
        });
    }

    public function getTempCategories(): JsonResponse
    {
        return $this->execute(function () {
            return $this->productService->getTempCategories();
        });
    }

    public function getTempMeasureUnits(): JsonResponse
    {
        return $this->execute(function () {
            return $this->productService->getTempMeasureUnits();
        });
    }
    

    public function chooseTempCategories(Request $request): JsonResponse 
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'temp_category_ids' => 'required|array',
                'temp_category_ids.*' => 'required|exists:temp_categories,id'
            ]);

            return $this->productService->chooseTempCategories($request->user(), $request->temp_category_ids);
        });
    }
    
    public function chooseTempMeasureUnits(Request $request): JsonResponse
    {
        $request->validate([
            'temp_measure_unit_ids' => 'required|array',
            'temp_measure_unit_ids.*' => 'required|exists:temp_measure_units,id'
        ]);

        return $this->execute(function () use ($request) {
            return $this->productService->chooseTempMeasureUnits($request->user(), $request->temp_measure_unit_ids);
        });
    }

    public function chooseTempData(Request $request): JsonResponse 
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'temp_category_ids' => 'required|array',
                'temp_category_ids.*' => 'required|exists:temp_categories,id',
                'temp_measure_unit_ids' => 'required|array',
                'temp_measure_unit_ids.*' => 'required|exists:temp_measure_units,id'
            ]);

            return $this->productService->chooseTempData($request->user(), $request->all());
        });
    }
} 
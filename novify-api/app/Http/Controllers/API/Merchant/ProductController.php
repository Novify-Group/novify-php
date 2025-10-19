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
use OpenApi\Annotations as OA;

class ProductController extends BaseApiController
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     description="Create a new product in the merchant's catalog",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "sku", "selling_price", "product_category_id", "product_measure_unit_id"},
     *             @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
     *             @OA\Property(property="sku", type="string", example="IPH15PRO-001"),
     *             @OA\Property(property="description", type="string", example="Latest iPhone model"),
     *             @OA\Property(property="selling_price", type="number", format="float", example=999.99),
     *             @OA\Property(property="cost_price", type="number", format="float", example=800.00),
     *             @OA\Property(property="product_category_id", type="integer", example=1),
     *             @OA\Property(property="product_measure_unit_id", type="integer", example=1),
     *             @OA\Property(property="is_inventory_tracked", type="boolean", example=true),
     *             @OA\Property(property="stock_quantity", type="integer", example=50),
     *             @OA\Property(property="is_taxable", type="boolean", example=true),
     *             @OA\Property(property="tax_percentage", type="number", format="float", example=15.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="product", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->productService->create($request->user(), $request->validated());
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="List products",
     *     description="Get a list of products with filtering and pagination",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of products per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by product name or SKU",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="products", type="array", @OA\Items(type="object")),
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
            return $this->productService->list($request->all(), $perPage);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Get specific product",
     *     description="Get details of a specific product with variants and images",
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="product", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        return $this->execute(function () use ($product) {
            return $this->successResponse([
                'product' => $product->load(['category', 'variants', 'images'])
            ]);
        });
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Update product",
     *     description="Update details of a specific product",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated iPhone 15 Pro"),
     *             @OA\Property(property="sku", type="string", example="IPH15PRO-001-UPDATED"),
     *             @OA\Property(property="description", type="string", example="Updated iPhone model"),
     *             @OA\Property(property="selling_price", type="number", format="float", example=1099.99),
     *             @OA\Property(property="cost_price", type="number", format="float", example=900.00),
     *             @OA\Property(property="product_category_id", type="integer", example=1),
     *             @OA\Property(property="product_measure_unit_id", type="integer", example=1),
     *             @OA\Property(property="is_inventory_tracked", type="boolean", example=true),
     *             @OA\Property(property="stock_quantity", type="integer", example=75),
     *             @OA\Property(property="is_taxable", type="boolean", example=true),
     *             @OA\Property(property="tax_percentage", type="number", format="float", example=18.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="product", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        return $this->execute(function () use ($request, $product) {
            return $this->productService->update($product, $request->validated());
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Delete product",
     *     description="Delete a specific product",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        return $this->execute(function () use ($product) {
            return $this->productService->delete($product);
        });
    }

    // Categories
    /**
     * @OA\Get(
     *     path="/api/v1/products/lists/categories",
     *     tags={"Products"},
     *     summary="Get product categories",
     *     description="Get list of active product categories",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of categories per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by category name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function categories(Request $request): JsonResponse
    {
      
        return $this->execute(function () use ($request) {
            $request['is_active'] = 1;
            return $this->productService->listCategories($request->all(), $request->input('per_page', 20));
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products/lists/categories",
     *     tags={"Products"},
     *     summary="Create product category",
     *     description="Create a new product category",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"temp_category_id", "name"},
     *             @OA\Property(property="temp_category_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="image", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="description", type="string", example="Electronic devices and accessories")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="object")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/products/lists/categories/{category}",
     *     tags={"Products"},
     *     summary="Get specific category",
     *     description="Get details of a specific product category with its products",
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="object")
     *             )
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}/variants",
     *     tags={"Products"},
     *     summary="Get product variants",
     *     description="Get all variants for a specific product",
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Variants retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="variants", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function variants(Product $product): JsonResponse
    {
        return $this->execute(function () use ($product) {
            return $this->successResponse(['variants' => $product->variants]);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products/{product}/variants",
     *     tags={"Products"},
     *     summary="Create product variant",
     *     description="Create a new variant for a specific product",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "sku", "cost_price", "selling_price", "stock_quantity", "min_stock_level", "attributes"},
     *             @OA\Property(property="name", type="string", example="iPhone 15 Pro - 256GB - Blue"),
     *             @OA\Property(property="sku", type="string", example="IPH15PRO-256-BLUE"),
     *             @OA\Property(property="cost_price", type="number", format="float", example=800.00),
     *             @OA\Property(property="selling_price", type="number", format="float", example=999.99),
     *             @OA\Property(property="stock_quantity", type="integer", example=50),
     *             @OA\Property(property="min_stock_level", type="integer", example=5),
     *             @OA\Property(property="attributes", type="object", example={"storage": "256GB", "color": "Blue"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Variant created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Variant created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="variant", type="object")
     *             )
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/api/v1/products/{product}/images",
     *     tags={"Products"},
     *     summary="Add product image",
     *     description="Add an image to a specific product",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"image_url"},
     *             @OA\Property(property="image_url", type="string", example="base64_encoded_image_data"),
     *             @OA\Property(property="variant_id", type="integer", example=1),
     *             @OA\Property(property="is_featured", type="boolean", example=false),
     *             @OA\Property(property="sort_order", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Image added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image added successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="image", type="object")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/products/temp/categories",
     *     tags={"Products"},
     *     summary="Get temporary categories",
     *     description="Get list of temporary categories for setup",
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Temporary categories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getTempCategories(): JsonResponse
    {
        return $this->execute(function () {
            return $this->productService->getTempCategories();
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/temp/measure-units",
     *     tags={"Products"},
     *     summary="Get temporary measure units",
     *     description="Get list of temporary measure units for setup",
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Temporary measure units retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getTempMeasureUnits(): JsonResponse
    {
        return $this->execute(function () {
            return $this->productService->getTempMeasureUnits();
        });
    }
    

    /**
     * @OA\Post(
     *     path="/api/v1/products/temp/categories",
     *     tags={"Products"},
     *     summary="Choose temporary categories",
     *     description="Select temporary categories for merchant setup",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"temp_category_ids"},
     *             @OA\Property(property="temp_category_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories selected successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories selected successfully")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/products/temp/setup",
     *     tags={"Products"},
     *     summary="Setup temporary data",
     *     description="Complete setup with temporary categories and measure units",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"temp_category_ids", "temp_measure_unit_ids"},
     *             @OA\Property(property="temp_category_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *             @OA\Property(property="temp_measure_unit_ids", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Setup completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Setup completed successfully")
     *         )
     *     )
     * )
     */
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
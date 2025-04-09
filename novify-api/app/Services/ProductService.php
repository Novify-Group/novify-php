<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Merchant;
use App\Models\ProductCategory;
use App\Models\ProductMeasureUnit;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Traits\ApiResponse;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\TempCategory;
use App\Models\TempMeasureUnit;


class ProductService
{
    use ApiResponse;

    public function create(Merchant $merchant, array $data): array
    {
        return DB::transaction(function () use ($merchant, $data) {
            // Handle featured image if provided
            if (isset($data['featured_image'])) {
                $data['featured_image'] = ImageHelper::saveBase64Image($data['featured_image'], 'product_images');
            }

            // Create the product
            $product = DB::transaction(function () use ($merchant, $data) {
                return $merchant->products()->create([
                    'product_category_id' => $data['category_id'],
                    'product_measure_unit_id' => $data['measure_unit_id'],
                    'name' => $data['name'],
                    'sku' => $data['sku'],
                    'description' => $data['description'] ?? null,
                    'cost_price' => $data['cost_price'],
                    'selling_price' => $data['selling_price'],
                    'stock_quantity' => $data['stock_quantity'],
                    'min_stock_level' => $data['min_stock_level'],
                    'featured_image' => $data['featured_image'] ?? null,
                    'is_featured' => $data['is_featured'] ?? false,
                    'is_discounted' => ($data['is_discounted'] || $data['discount_percentage'] || $data['discount_amount']) ?? false,
                    'discount_percentage' => $data['discount_percentage'] ?? null,
                    'discount_amount' => $data['discount_amount'] ?? null,
                    'is_discount_percentage' => $data['is_discount_percentage'] ?? false,
                    'is_taxable' => ($data['is_taxable'] || $data['tax_percentage'] || $data['tax_amount']) ?? false,
                    'tax_percentage' => $data['tax_percentage'] ?? null,
                    'tax_amount' => $data['tax_amount'] ?? null,
                    'is_tax_percentage' => $data['is_tax_percentage'] ?? false,
                    'is_inventory_tracked' => $data['is_inventory_tracked'] ?? true,
                    'expiry_date' => $data['expiry_date'] ?? null,
                ]);
            });

            // Create variants if provided
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variant) {
                    $product->variants()->create($variant);
                }
            }

            // Create images if provided
            if (!empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $imageUrl = ImageHelper::saveBase64Image($image['image_url'], 'product_images');
                    $product->images()->create([
                        'image_url' => $imageUrl,
                        'is_featured' => $image['is_featured'] ?? false,
                        'sort_order' => $image['sort_order'] ?? 0
                    ]);
                }
            }

            return $this->successResponse( $product->load(['category', 'variants', 'images']),
                'Product created successfully',
                201
            );
        });
    }

    public function list(array $request, int $perPage = 20): array
    {
        $products = $this->applyCommonFilters(Product::query(), $request)
            ->when(isset($request['category_id']), function ($query) use ($request) {
                $query->where('product_category_id', $request['category_id']);
            })
            ->when(isset($request['is_featured']), function ($query) use ($request) {
                $query->where('is_featured', $request['is_featured']);
            })
            ->when(isset($request['is_discounted']), function ($query) use ($request) {
                $query->where('is_discounted', intval($request['is_discounted']));
            })
            ->with(['category', 'variants', 'images'])
            ->paginate($perPage);

        return $this->successResponse( $products);
    }

    public function update(Product $product, array $data): array
    {
        return DB::transaction(function () use ($product, $data) {
            // Handle featured image if provided
            if (isset($data['featured_image'])) {
                $data['featured_image'] = ImageHelper::saveBase64Image($data['featured_image'], 'product_images');
            }

            // Update product
            $product = DB::transaction(function () use ($product, $data) {
                return $product->update($data);
            });

            // Handle variants
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    if (isset($variantData['id'])) {
                        $product->variants()->where('id', $variantData['id'])->update($variantData);
                    } else {
                        $product->variants()->create($variantData);
                    }
                }
            }

            // Handle variant deletions
            if (!empty($data['variants_to_delete'])) {
                $product->variants()->whereIn('id', $data['variants_to_delete'])->delete();
            }

            // Handle new images
            if (!empty($data['images'])) {
                foreach ($data['images'] as $imageData) {
                    if (isset($imageData['id'])) {
                        if (isset($imageData['image_url'])) {
                            $imageData['image_url'] = ImageHelper::saveBase64Image($imageData['image_url'], 'product_images');
                        }
                        $product->images()->where('id', $imageData['id'])->update($imageData);
                    } else {
                        $imageUrl = ImageHelper::saveBase64Image($imageData['image_url'], 'product_images');
                        $product->images()->create([
                            'image_url' => $imageUrl,
                            'is_featured' => $imageData['is_featured'] ?? false,
                            'sort_order' => $imageData['sort_order'] ?? 0
                        ]);
                    }
                }
            }

            // Handle image deletions
            if (!empty($data['images_to_delete'])) {
                $product->images()->whereIn('id', $data['images_to_delete'])->delete();
            }

            return $this->successResponse( $product->fresh(['category', 'variants', 'images']),
                'Product updated successfully');
        });
    }

    public function delete(Product $product): array
    {
        $product->delete();
        return $this->successResponse(null, 'Product deleted successfully');
    }

    // Categories
    public function listCategories( array $request, int $perPage = 20): array
    {
         $categories = $this->applyCommonFilters(ProductCategory::query(), $request)
         ->withCount("products")
         ->paginate($perPage);

        return $this->successResponse( $categories);
    }

    public function createCategory(Merchant $merchant, array $data): array
    {
        return DB::transaction(function () use ($merchant, $data) {
            if (isset($data['image'])) {
                $data['image'] = ImageHelper::saveBase64Image($data['image'], 'category_images');
            }

            $category = $merchant->productCategories()->create($data);

            return $this->successResponse(
                ['category' => $category],
                'Category created successfully',
                201
            );
        });
    }

    public function updateCategory(ProductCategory $category, array $data): array
    {
        return DB::transaction(function () use ($category, $data) {
            if (isset($data['image'])) {
                $data['image'] = ImageHelper::saveBase64Image($data['image'], 'category_images');
            }

            $category->update($data);

            return $this->successResponse(
                ['category' => $category->fresh()],
                'Category updated successfully'
            );
        });
    }

    public function deleteCategory(ProductCategory $category): array
    {
        if ($category->products()->exists()) {
            return $this->errorResponse('Cannot delete category with associated products', 400);
        }

        $category->delete();
        return $this->successResponse(null, 'Category deleted successfully');
    }

    // Measure Units
    public function listMeasureUnits( array $request): array
    {
        $units = $this->applyCommonFilters(ProductMeasureUnit::query(), $request)->get();
        return $this->successResponse($units);
    }

    public function applyCommonFilters($query, $request)
    {
        return $query->when(isset($request['is_active']), function ($query) use ($request) {
            $query->where('is_active', intval($request['is_active']));
        })
        ->when(isset($request['search']), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request['search'] . '%');
        })
        ->when(isset($request['merchant_id']), function ($query) use ($request) {
            $query->where('merchant_id', $request['merchant_id']);
        });
    }

    public function createMeasureUnit(Merchant $merchant, array $data): array
    {
        $unit = $merchant->productMeasureUnits()->create($data);

        return $this->successResponse(
            ['measure_unit' => $unit],
            'Measure unit created successfully',
            201
        );
    }

    public function updateMeasureUnit(ProductMeasureUnit $unit, array $data): array
    {
        return DB::transaction(function () use ($unit, $data) {
            $unit->update($data);

        return $this->successResponse( $unit->fresh(),
                'Measure unit updated successfully'
            );
        });
    }

    public function deleteMeasureUnit(ProductMeasureUnit $unit): array
    {
        if ($unit->products()->exists()) {
            return $this->errorResponse('Cannot delete measure unit with associated products', 400);
        }

        $unit->delete();
        return $this->successResponse(null, 'Measure unit deleted successfully');
    }

    // Variants
    public function createVariant(Product $product, array $data): array
    {
        $variant = $product->variants()->create($data);

        return $this->successResponse(
            ['variant' => $variant],
            'Product variant created successfully',
            201
        );
    }

    public function updateVariant(ProductVariant $variant, array $data): array
    {
        $variant->update($data);

        return $this->successResponse(
            ['variant' => $variant->fresh()],
            'Product variant updated successfully'
        );
    }

    public function deleteVariant(ProductVariant $variant): array
    {
        $variant->delete();
        return $this->successResponse(null, 'Product variant deleted successfully');
    }

    // Images
    public function createImage(Product $product, array $data): array
    {
        return DB::transaction(function () use ($product, $data) {
            $data['image_url'] = ImageHelper::saveBase64Image($data['image_url'], 'product_images');

            // If this is marked as featured, unmark others
            if ($data['is_featured'] ?? false) {
                $product->images()->update(['is_featured' => false]);
            }

            $image = $product->images()->create($data);

            return $this->successResponse(
                ['image' => $image],
                'Product image created successfully',
                201
            );
        });
    }

    public function updateImage(ProductImage $image, array $data): array
    {
        return DB::transaction(function () use ($image, $data) {
            if (isset($data['image_url'])) {
                $data['image_url'] = ImageHelper::saveBase64Image($data['image_url'], 'product_images');
            }

            // Handle featured image changes
            if (($data['is_featured'] ?? false) && !$image->is_featured) {
                $image->product->images()->where('id', '!=', $image->id)->update(['is_featured' => false]);
            }

            $image->update($data);

            return $this->successResponse(
                ['image' => $image->fresh()],
                'Product image updated successfully'
            );
        });
    }

    public function deleteImage(ProductImage $image): array
    {
        $image->delete();
        return $this->successResponse(null, 'Product image deleted successfully');
    }

    public function reorderImages(Product $product, array $imageData): array
    {
        DB::transaction(function () use ($imageData) {
            foreach ($imageData as $data) {
                ProductImage::where('id', $data['id'])->update(['sort_order' => $data['sort_order']]);
            }
        });

        return $this->successResponse(
            ['images' => $product->images()->orderBy('sort_order')->get()],
            'Images reordered successfully'
        );
    }

    public function getTempCategories(): array
    {
        $categories = TempCategory::where('is_active', true)->get();
        return $this->successResponse($categories);
    }

    public function getTempMeasureUnits(): array
    {
        $measureUnits = TempMeasureUnit::where('is_active', true)->get();
        return $this->successResponse($measureUnits);
    }

    public function chooseTempData(Merchant $merchant, array $tempData): array
    {
        $tempCategories   = $tempData['temp_category_ids'];
        $tempMeasureUnits = $tempData['temp_measure_unit_ids'];

        if(count($tempCategories) == 0 && count($tempMeasureUnits) == 0)
            return $this->errorResponse('No selection made', 400);

        if(count($tempCategories) > 0)
            $this->chooseTempCategories($merchant, $tempCategories);
        
        if(count($tempMeasureUnits) > 0)
            $this->chooseTempMeasureUnits($merchant, $tempMeasureUnits);

        $tempData = [
            'product_categories' => $merchant->productCategories,
            'product_measure_units' => $merchant->productMeasureUnits
        ];

        return $this->successResponse($tempData, 'Temp data chosen successfully');
    }
    

    public function chooseTempCategories(Merchant $merchant, array $tempCategoryIds): array
    {
        // Get the temp categories with their data
        $tempCategories = TempCategory::whereIn('id', $tempCategoryIds)
            ->whereNotIn('id', $merchant->productCategories->pluck('temp_category_id'))
            ->get()
            ->map(function ($tempCategory) use ($merchant) {
                return [
                    'merchant_id' => $merchant->id,
                    'temp_category_id' => $tempCategory->id,
                    'name' => $tempCategory->name,
                    'image' => $tempCategory->image,
                    'description' => $tempCategory->description,
                    'is_active' => true
                ];
            })
            ->toArray();

        $merchant->productCategories()->createMany($tempCategories);
        return $this->successResponse(null, 'Categories chosen successfully');
    }
    
    public function chooseTempMeasureUnits(Merchant $merchant, array $tempMeasureUnitIds): array
    {
        // Get the temp measure units with their data
        $tempMeasureUnits = TempMeasureUnit::whereIn('id', $tempMeasureUnitIds)
            ->whereNotIn('id', $merchant->productMeasureUnits->pluck('temp_measure_unit_id'))
            ->get()
            ->map(function ($tempMeasureUnit) use ($merchant) {
                return [
                    'merchant_id' => $merchant->id,
                    'temp_measure_unit_id' => $tempMeasureUnit->id,
                    'name' => $tempMeasureUnit->name,
                    'symbol' => $tempMeasureUnit->symbol,
                    'description' => $tempMeasureUnit->description,
                    'is_active' => true
                ];
            })
            ->toArray();

        $merchant->productMeasureUnits()->createMany($tempMeasureUnits);
        return $this->successResponse(null, 'Measure units chosen successfully');
    }
    
} 
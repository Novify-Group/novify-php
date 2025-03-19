<?php

namespace Database\Seeders;

use App\Models\TempCategory;
use Illuminate\Database\Seeder;

class TempCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Building and Construction
            [
                'name' => 'Building & Construction',
                'description' => 'Construction materials and tools',
                'children' => [
                    ['name' => 'Cement & Concrete', 'description' => 'Cement, concrete and related products'],
                    ['name' => 'Timber & Wood', 'description' => 'Wood products and timber materials'],
                    ['name' => 'Plumbing', 'description' => 'Pipes, fittings and plumbing materials'],
                    ['name' => 'Electrical', 'description' => 'Electrical wiring and components'],
                    ['name' => 'Paint & Finishes', 'description' => 'Paints, varnishes and finishes'],
                    ['name' => 'Hardware', 'description' => 'Tools and construction hardware'],
                ]
            ],
            // Dairy Products
            [
                'name' => 'Dairy Products',
                'description' => 'Milk and dairy products',
                'children' => [
                    ['name' => 'Fresh Milk', 'description' => 'Fresh and pasteurized milk'],
                    ['name' => 'Yogurt', 'description' => 'Yogurt and fermented milk products'],
                    ['name' => 'Cheese', 'description' => 'Various types of cheese'],
                    ['name' => 'Butter & Ghee', 'description' => 'Butter, ghee and spreads'],
                    ['name' => 'Ice Cream', 'description' => 'Ice cream and frozen dairy desserts'],
                ]
            ],
            // Farm Inputs
            [
                'name' => 'Farm Inputs',
                'description' => 'Agricultural supplies and equipment',
                'children' => [
                    ['name' => 'Seeds', 'description' => 'Agricultural and garden seeds'],
                    ['name' => 'Fertilizers', 'description' => 'Organic and chemical fertilizers'],
                    ['name' => 'Pesticides', 'description' => 'Pest control products'],
                    ['name' => 'Animal Feed', 'description' => 'Livestock and poultry feed'],
                    ['name' => 'Farm Tools', 'description' => 'Agricultural tools and equipment'],
                    ['name' => 'Irrigation', 'description' => 'Irrigation systems and equipment'],
                ]
            ],
            // Bakery and Confectionery
            [
                'name' => 'Bakery & Confectionery',
                'description' => 'Baked goods and sweets',
                'children' => [
                    ['name' => 'Bread', 'description' => 'Fresh bread and rolls'],
                    ['name' => 'Cakes', 'description' => 'Cakes and pastries'],
                    ['name' => 'Cookies', 'description' => 'Cookies and biscuits'],
                    ['name' => 'Chocolates', 'description' => 'Chocolate products'],
                    ['name' => 'Candies', 'description' => 'Sweets and candies'],
                ]
            ],
            // Meats
            [
                'name' => 'Meats',
                'description' => 'Fresh and processed meats',
                'children' => [
                    ['name' => 'Beef', 'description' => 'Fresh and frozen beef products'],
                    ['name' => 'Poultry', 'description' => 'Chicken and other poultry'],
                    ['name' => 'Pork', 'description' => 'Fresh and processed pork'],
                    ['name' => 'Fish', 'description' => 'Fresh and frozen fish'],
                    ['name' => 'Processed Meats', 'description' => 'Sausages and cured meats'],
                ]
            ],
            // Electronics (existing)
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'children' => [
                    ['name' => 'Smartphones', 'description' => 'Mobile phones and accessories'],
                    ['name' => 'Computers', 'description' => 'Laptops, desktops and accessories'],
                    ['name' => 'Audio', 'description' => 'Speakers, headphones and audio equipment'],
                    ['name' => 'TVs', 'description' => 'Television sets and accessories'],
                    ['name' => 'Gaming', 'description' => 'Gaming consoles and accessories'],
                ]
            ],
            // Fashion (existing with expansion)
            [
                'name' => 'Fashion',
                'description' => 'Clothing, shoes and accessories',
                'children' => [
                    ['name' => 'Men\'s Wear', 'description' => 'Clothing for men'],
                    ['name' => 'Women\'s Wear', 'description' => 'Clothing for women'],
                    ['name' => 'Children\'s Wear', 'description' => 'Clothing for children'],
                    ['name' => 'Shoes', 'description' => 'Footwear for all'],
                    ['name' => 'Accessories', 'description' => 'Fashion accessories'],
                ]
            ],
            // Groceries and Household
            [
                'name' => 'Groceries & Household',
                'description' => 'Food and household items',
                'children' => [
                    ['name' => 'Cereals', 'description' => 'Rice, maize and other grains'],
                    ['name' => 'Cooking Oil', 'description' => 'Edible oils and fats'],
                    ['name' => 'Spices', 'description' => 'Cooking spices and seasonings'],
                    ['name' => 'Cleaning', 'description' => 'Household cleaning products'],
                    ['name' => 'Personal Care', 'description' => 'Personal hygiene products'],
                ]
            ],
            // Beverages
            [
                'name' => 'Beverages',
                'description' => 'Drinks and liquid refreshments',
                'children' => [
                    ['name' => 'Soft Drinks', 'description' => 'Carbonated beverages'],
                    ['name' => 'Juices', 'description' => 'Fruit and vegetable juices'],
                    ['name' => 'Water', 'description' => 'Bottled water'],
                    ['name' => 'Tea & Coffee', 'description' => 'Hot beverages'],
                    ['name' => 'Energy Drinks', 'description' => 'Energy and sports drinks'],
                ]
            ]
        ];

        foreach ($categories as $category) {
            $parent = TempCategory::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'is_active' => true,
            ]);

            foreach ($category['children'] as $child) {
                TempCategory::create([
                    'name' => $child['name'],
                    'description' => $child['description'],
                    'parent_id' => $parent->id,
                    'is_active' => true,
                ]);
            }
        }
    }
} 
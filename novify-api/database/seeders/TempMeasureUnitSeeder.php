<?php

namespace Database\Seeders;

use App\Models\TempMeasureUnit;
use Illuminate\Database\Seeder;

class TempMeasureUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // Weight
            ['name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Metric unit of mass'],
            ['name' => 'Gram', 'symbol' => 'g', 'description' => 'Metric unit of mass'],
            ['name' => 'Pound', 'symbol' => 'lb', 'description' => 'Imperial unit of mass'],
            ['name' => 'Ounce', 'symbol' => 'oz', 'description' => 'Imperial unit of mass'],
            
            // Volume
            ['name' => 'Liter', 'symbol' => 'L', 'description' => 'Metric unit of volume'],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'description' => 'Metric unit of volume'],
            ['name' => 'Gallon', 'symbol' => 'gal', 'description' => 'Imperial unit of volume'],
            
            // Length
            ['name' => 'Meter', 'symbol' => 'm', 'description' => 'Metric unit of length'],
            ['name' => 'Centimeter', 'symbol' => 'cm', 'description' => 'Metric unit of length'],
            ['name' => 'Inch', 'symbol' => 'in', 'description' => 'Imperial unit of length'],
            
            // Quantity
            ['name' => 'Piece', 'symbol' => 'pc', 'description' => 'Count of individual items'],
            ['name' => 'Dozen', 'symbol' => 'dz', 'description' => 'Group of twelve items'],
            ['name' => 'Box', 'symbol' => 'box', 'description' => 'Standard packaging unit'],
            ['name' => 'Pack', 'symbol' => 'pk', 'description' => 'Group of items'],
            
            // Area
            ['name' => 'Square Meter', 'symbol' => 'm²', 'description' => 'Metric unit of area'],
            ['name' => 'Square Foot', 'symbol' => 'ft²', 'description' => 'Imperial unit of area'],
        ];

        foreach ($units as $index => $unit) {
            TempMeasureUnit::create([
                'name' => $unit['name'],
                'symbol' => $unit['symbol'],
                'description' => $unit['description'],
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
} 
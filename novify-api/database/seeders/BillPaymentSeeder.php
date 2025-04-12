<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillCategory;
use App\Models\Biller;
use App\Models\BillerItem;

class BillPaymentSeeder extends Seeder
{
    public function run()
    {
        // Create Bill Categories
        $categories = [
            [
                'name' => 'Housing & Utilities',
                'description' => 'Housing, rent, and utility payments',
                'status' => 'active'
            ],
            [
                'name' => 'Shopping',
                'description' => 'Retail and marketplace payments',
                'status' => 'active'
            ],
            [
                'name' => 'Internet & TV',
                'description' => 'Internet and cable TV services',
                'status' => 'active'
            ]
        ];

        foreach ($categories as $category) {
            BillCategory::create($category);
        }

        // Create Biller - My Market
        $myMarket = Biller::create([
            'bill_category_id' => 2, // Shopping category
            'name' => 'My Market',
            'code' => 'MYMARKET',
            'logo' => 'mymarket-logo.png',
            'status' => 'active',
            'description' => 'My Market - Your one-stop shopping destination'
        ]);

        // Create Biller Items for My Market
        BillerItem::create([
            'biller_id' => $myMarket->id,
            'name' => 'Rent Payment',
            'code' => 'RENT-PAY',
            'min_amount' => 100.00,
            'max_amount' => 100000.00,
            'is_amount_fixed' => false,
            'status' => 'active',
            'description' => 'Rental payment for My Market properties'
        ]);

        // Add more sample billers
        $utilityBiller = Biller::create([
            'bill_category_id' => 1, // Housing & Utilities category
            'name' => 'City Utilities',
            'code' => 'CITYUTIL',
            'logo' => 'cityutil-logo.png',
            'status' => 'active',
            'description' => 'City Utilities and Services'
        ]);

        // Create Biller Items for City Utilities
        BillerItem::create([
            'biller_id' => $utilityBiller->id,
            'name' => 'Water Bill',
            'code' => 'WATER-BILL',
            'min_amount' => 10.00,
            'max_amount' => 1000.00,
            'is_amount_fixed' => false,
            'status' => 'active',
            'description' => 'Water utility bill payment'
        ]);

        BillerItem::create([
            'biller_id' => $utilityBiller->id,
            'name' => 'Electricity Bill',
            'code' => 'ELEC-BILL',
            'min_amount' => 20.00,
            'max_amount' => 2000.00,
            'is_amount_fixed' => false,
            'status' => 'active',
            'description' => 'Electricity bill payment'
        ]);

        // Internet Provider
        $internetBiller = Biller::create([
            'bill_category_id' => 3, // Internet & TV category
            'name' => 'FastNet ISP',
            'code' => 'FASTNET',
            'logo' => 'fastnet-logo.png',
            'status' => 'active',
            'description' => 'High-speed internet services'
        ]);

        // Create Biller Items for FastNet
        BillerItem::create([
            'biller_id' => $internetBiller->id,
            'name' => 'Internet Monthly Plan',
            'code' => 'NET-MONTHLY',
            'amount' => 49.99,
            'is_amount_fixed' => true,
            'status' => 'active',
            'description' => 'Monthly internet subscription'
        ]);
    }
} 
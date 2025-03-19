<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            [
                'name' => 'Uganda Shilling',
                'code' => 'UGX',
                'symbol' => 'USh',
                'is_active' => true
            ],
            [
                'name' => 'Kenya Shilling',
                'code' => 'KES',
                'symbol' => 'KSh',
                'is_active' => true
            ],
            [
                'name' => 'Tanzania Shilling',
                'code' => 'TZS',
                'symbol' => 'TSh',
                'is_active' => true
            ],
            [
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'is_active' => true
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'is_active' => true
            ],
            [
                'name' => 'Rwanda Franc',
                'code' => 'RWF',
                'symbol' => 'RF',
                'is_active' => true
            ],
            [
                'name' => 'British Pound',
                'code' => 'GBP',
                'symbol' => '£',
                'is_active' => true
            ],
            [
                'name' => 'South African Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
                'is_active' => true
            ]
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
} 
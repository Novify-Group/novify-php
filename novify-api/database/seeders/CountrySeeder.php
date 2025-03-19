<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            [
                'name' => 'Kenya',
                'code' => 'KEN',
                'phone_code' => '254',
                'currency_code' => 'KES',
                'currency_symbol' => 'KSh',
                'is_active' => false
            ],
            [
                'name' => 'Uganda',
                'code' => 'UGA',
                'phone_code' => '256',
                'currency_code' => 'UGX',
                'currency_symbol' => 'USh',
                'is_active' => true // Only Uganda is active
            ],
            [
                'name' => 'Tanzania',
                'code' => 'TZA',
                'phone_code' => '255',
                'currency_code' => 'TZS',
                'currency_symbol' => 'TSh',
                'is_active' => false
            ],
            [
                'name' => 'Rwanda',
                'code' => 'RWA',
                'phone_code' => '250',
                'currency_code' => 'RWF',
                'currency_symbol' => 'RF',
                'is_active' => false
            ],
            [
                'name' => 'Burundi',
                'code' => 'BDI',
                'phone_code' => '257',
                'currency_code' => 'BIF',
                'currency_symbol' => 'FBu',
                'is_active' => false
            ],
            [
                'name' => 'South Sudan',
                'code' => 'SSD',
                'phone_code' => '211',
                'currency_code' => 'SSP',
                'currency_symbol' => 'SSP',
                'is_active' => false
            ],
            [
                'name' => 'Ethiopia',
                'code' => 'ETH',
                'phone_code' => '251',
                'currency_code' => 'ETB',
                'currency_symbol' => 'Br',
                'is_active' => false
            ],
            [
                'name' => 'Somalia',
                'code' => 'SOM',
                'phone_code' => '252',
                'currency_code' => 'SOS',
                'currency_symbol' => 'Sh.So.',
                'is_active' => false
            ],
            [
                'name' => 'Djibouti',
                'code' => 'DJI',
                'phone_code' => '253',
                'currency_code' => 'DJF',
                'currency_symbol' => 'Fdj',
                'is_active' => false
            ],
            [
                'name' => 'Eritrea',
                'code' => 'ERI',
                'phone_code' => '291',
                'currency_code' => 'ERN',
                'currency_symbol' => 'Nfk',
                'is_active' => false
            ],
            [
                'name' => 'Sudan',
                'code' => 'SDN',
                'phone_code' => '249',
                'currency_code' => 'SDG',
                'currency_symbol' => 'SDG',
                'is_active' => false
            ],
            [
                'name' => 'Nigeria',
                'code' => 'NGA',
                'phone_code' => '234',
                'currency_code' => 'NGN',
                'currency_symbol' => '₦',
                'is_active' => false
            ],
            [
                'name' => 'Ghana',
                'code' => 'GHA',
                'phone_code' => '233',
                'currency_code' => 'GHS',
                'currency_symbol' => 'GH₵',
                'is_active' => false
            ],
            [
                'name' => 'South Africa',
                'code' => 'ZAF',
                'phone_code' => '27',
                'currency_code' => 'ZAR',
                'currency_symbol' => 'R',
                'is_active' => false
            ],
            [
                'name' => 'Egypt',
                'code' => 'EGY',
                'phone_code' => '20',
                'currency_code' => 'EGP',
                'currency_symbol' => 'E£',
                'is_active' => false
            ],
            [
                'name' => 'Morocco',
                'code' => 'MAR',
                'phone_code' => '212',
                'currency_code' => 'MAD',
                'currency_symbol' => 'MAD',
                'is_active' => false
            ],
            [
                'name' => 'Zambia',
                'code' => 'ZMB',
                'phone_code' => '260',
                'currency_code' => 'ZMW',
                'currency_symbol' => 'ZK',
                'is_active' => false
            ],
            [
                'name' => 'Zimbabwe',
                'code' => 'ZWE',
                'phone_code' => '263',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'is_active' => false
            ],
            [
                'name' => 'Malawi',
                'code' => 'MWI',
                'phone_code' => '265',
                'currency_code' => 'MWK',
                'currency_symbol' => 'MK',
                'is_active' => false
            ],
            [
                'name' => 'Mozambique',
                'code' => 'MOZ',
                'phone_code' => '258',
                'currency_code' => 'MZN',
                'currency_symbol' => 'MT',
                'is_active' => false
            ]
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
} 
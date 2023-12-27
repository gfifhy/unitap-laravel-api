<?php

namespace Database\Seeders;

use App\Models\CMSLanding;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{

    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            [
                'type' => 'upperLanding',
                'value' => json_encode([
                    'imgurl' => '/storage/658ab800e09d6_Kona164630.png',
                    'title' => 'UniTap',
                    'subtitle' => 'Transaction United',
                    'description' => 'Centralized, seamless and secure commerce payment system.',
                    'btn_txt' => 'About',
                    'btn_lnk' => '/about',
                    'disabled' => 0,
                ]),
            ],
            [
                'type' => 'middleLanding',
                'value' => json_encode([
                    'title' => 'With just a tap',
                    'subtitle' => 'Harnessing Secure NFC Technology',
                    'disabled' => 0,
                ]),
            ],
            [
                'type' => 'middleLanding_card',
                'value' => json_encode([
                    'title' => 'Enhanced Security',
                    'subtitle' => 'Robust layers of security',
                    'imgurl' => '/storage/658ad8de98630_1674978629184343.jpg',
                ]),
                'option' => 0,
            ],
            [
                'type' => 'middleLanding_card',
                'value' => json_encode([
                    'title' => 'Convenience',
                    'subtitle' => 'Simplified transactions',
                    'imgurl' => '/storage/658ad8dea7732_assagasgah.png',
                ]),
                'option' => 1,
            ],
            [
                'type' => 'middleLanding_card',
                'value' => json_encode([
                    'title' => 'Future-Proof',
                    'subtitle' => 'Forefront of digital payment',
                    'imgurl' => '/storage/658ad8deb02ae_848915486677794898.png',
                ]),
                'option' => 2,
            ],
            [
                'type' => 'lowerLanding',
                'value' => json_encode([
                    'imgurl' => '/storage/658be83cc51ba_mp,840x830,matte,f8f8f8,t-pad,750x1000,f8f8f8.jpg',
                    'title' => 'Discoverable Deals',
                    'subtitle' => 'Inclusivity in choices made more possible',
                    'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec vel nisi et eros commodo bibendum sed quis enim.',
                    'btn_txt' => 'Learn More',
                    'btn_lnk' => '/about',
                    'disabled' => 1,
                ]),
                'is_disabled' => 1,
            ]
        ];
        foreach ($values as $item) {
            CMSLanding::create($item);
        }

    }

}

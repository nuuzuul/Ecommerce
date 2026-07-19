<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $buyerRole = Role::firstOrCreate(['name' => 'pembeli']);

        User::updateOrCreate(
            ['email' => 'admin@kanrejawataa.test'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin Kanrejawataa',
                'phone' => '081234567890',
                'address' => 'Makassar, Sulawesi Selatan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'pembeli@kanrejawataa.test'],
            [
                'role_id' => $buyerRole->id,
                'name' => 'Pembeli Demo',
                'phone' => '081298765432',
                'address' => 'Jl. Perintis Kemerdekaan, Makassar',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $kering = Category::updateOrCreate(
            ['slug' => 'kue-kering'],
            [
                'name' => 'Kue Kering',
                'description' => 'Kue kering untuk keluarga, hampers, dan berbagai momen spesial.',
                'uses_variants' => true,
                'is_active' => true,
            ]
        );

        $tradisional = Category::updateOrCreate(
            ['slug' => 'kue-tradisional-makassar'],
            [
                'name' => 'Kue Tradisional Makassar',
                'description' => 'Aneka jajanan dan kue khas Makassar dengan cita rasa autentik.',
                'uses_variants' => false,
                'is_active' => true,
            ]
        );

        $products = [
            [
                'category' => $kering,
                'name' => 'Nastar Premium',
                'slug' => 'nastar-premium',
                'description' => 'Nastar lembut dengan isian selai nanas homemade dan aroma butter yang harum.',
                'featured' => true,
                'variants' => [
                    ['label' => '500 gram', 'weight_grams' => 500, 'price' => 85000, 'stock' => 24],
                    ['label' => '1 kg', 'weight_grams' => 1000, 'price' => 160000, 'stock' => 12],
                ],
            ],
            [
                'category' => $kering,
                'name' => 'Kastengel Keju',
                'slug' => 'kastengel-keju',
                'description' => 'Kastengel renyah dengan rasa keju gurih dan taburan keju di bagian atas.',
                'featured' => true,
                'variants' => [
                    ['label' => '500 gram', 'weight_grams' => 500, 'price' => 95000, 'stock' => 20],
                    ['label' => '1 kg', 'weight_grams' => 1000, 'price' => 180000, 'stock' => 10],
                ],
            ],
            [
                'category' => $kering,
                'name' => 'Putri Salju',
                'slug' => 'putri-salju',
                'description' => 'Kue putri salju lembut dengan kacang dan balutan gula halus yang manis.',
                'featured' => false,
                'variants' => [
                    ['label' => '500 gram', 'weight_grams' => 500, 'price' => 80000, 'stock' => 18],
                    ['label' => '1 kg', 'weight_grams' => 1000, 'price' => 150000, 'stock' => 8],
                ],
            ],
            [
                'category' => $kering,
                'name' => 'Lidah Kucing',
                'slug' => 'lidah-kucing',
                'description' => 'Kue tipis dan renyah dengan rasa butter yang ringan, cocok sebagai teman minum teh.',
                'featured' => false,
                'variants' => [
                    ['label' => '500 gram', 'weight_grams' => 500, 'price' => 78000, 'stock' => 16],
                    ['label' => '1 kg', 'weight_grams' => 1000, 'price' => 145000, 'stock' => 7],
                ],
            ],
            [
                'category' => $tradisional,
                'name' => 'Barongko',
                'slug' => 'barongko',
                'description' => 'Kue pisang khas Bugis-Makassar yang lembut, dibungkus daun pisang, dan disajikan dingin.',
                'featured' => true,
                'variants' => [
                    ['label' => null, 'weight_grams' => null, 'price' => 12000, 'stock' => 40],
                ],
            ],
            [
                'category' => $tradisional,
                'name' => 'Cucuru Bayao',
                'slug' => 'cucuru-bayao',
                'description' => 'Kue tradisional berbahan kuning telur dengan cita rasa manis dan tekstur lembut.',
                'featured' => true,
                'variants' => [
                    ['label' => null, 'weight_grams' => null, 'price' => 10000, 'stock' => 35],
                ],
            ],
            [
                'category' => $tradisional,
                'name' => 'Sikaporo',
                'slug' => 'sikaporo',
                'description' => 'Kue lapis khas Makassar dengan warna menarik, tekstur halus, dan rasa santan yang khas.',
                'featured' => false,
                'variants' => [
                    ['label' => null, 'weight_grams' => null, 'price' => 15000, 'stock' => 22],
                ],
            ],
            [
                'category' => $tradisional,
                'name' => 'Katirisala',
                'slug' => 'katirisala',
                'description' => 'Perpaduan ketan hitam dan lapisan gula merah yang legit serta harum.',
                'featured' => false,
                'variants' => [
                    ['label' => null, 'weight_grams' => null, 'price' => 14000, 'stock' => 25],
                ],
            ],
        ];

        foreach ($products as $item) {
            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $item['category']->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_featured' => $item['featured'],
                    'is_active' => true,
                ]
            );

            $product->variants()->delete();
            $product->variants()->createMany($item['variants']);
        }
    }
}

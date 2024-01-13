<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'id' => '00000000-0000-0000-0000-000000000000',
            'product_name' => 'peso',
            'user_id' => '00000000-0000-0000-0000-000000000000',
            'stock' => '-1',
            'price' => 1,
            'image' => ''
        ]);
    }
}

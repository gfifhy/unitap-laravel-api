<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert([
            ['id' => Str::uuid(), 'user_id' => '3042099f-0fd1-473b-b5d4-e0da9cea613b', 'store_name' => 'Unitap']
        ]);
    }
}

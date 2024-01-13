<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wallets')->insert([
            ['id' => '2588800c-c181-4c32-80ce-b04d5156b958', 'user_id' => '974c1f8d-dece-4a60-9453-e34e4781b429', 'balance' => 0, 'isDisabled' => 0],
            ['id' => '9a8e0c76-46ef-4333-badc-18f668c94514', 'user_id' => '3042099f-0fd1-473b-b5d4-e0da9cea613b', 'balance' => 0, 'isDisabled' => 0],
        ]);
    }
}

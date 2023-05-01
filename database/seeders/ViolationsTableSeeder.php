<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ViolationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = ['1c29ef17-3482-4920-a710-b59551a54be8','9a9cf325-c7a1-43f0-860b-29a257c59f36','0e59ebb6-fbdb-4037-a44c-0f6734a62c83','c544645a-28ae-4432-8cbc-c5c510cdd25f','5b6184a0-ab7a-4d5e-98e0-5a0bc377619d','05fa631d-f00f-4f76-9dd4-66182ef26051', '5f8f588d-577f-43d8-be90-ad1c18106e79

', 'eece836f-fb5a-46cc-accb-81d72f92e50a'];
        $violations = ['Haircut', 'Piercing', 'Hair Color', 'Charging', 'Sitting on a table', 'Fighting', 'Vandalism', 'Cheating'];
                for ($i=0; $i<count($violations); $i++){
                    DB::table('violations')->insert([
                        ['id' => $id[$i], 'violation_name' => $violations[$i]],
                    ]);
                }
    }
}

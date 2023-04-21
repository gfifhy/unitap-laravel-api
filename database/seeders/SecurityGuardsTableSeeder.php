<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityGuardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('security_guards')->insert([
            ['id' => Str::uuid(), 'user_id' => 'd75da919-746b-4c21-b642-b318cc98dd05', 'location_id' => '4b090ffc-41f8-498d-973a-5944f4fdeaad']
        ]);
    }
}

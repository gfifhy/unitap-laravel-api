<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            ['id' => '1615b6bb-7792-495a-998d-e522abb4a29f','role_id' => '5762ddd2-dad9-4729-b77a-7b06ea14eb3e' , 'email' => 'umakadmin.1972@umak.edu.ph','first_name' => 'James Francis', 'last_name' => 'Ga', 'password'=> bcrypt('UmakAdmin@#1972!')],
            ['id' => '974c1f8d-dece-4a60-9453-e34e4781b429','role_id' => '69717d90-078a-40b8-afd0-0f20a654b78e' , 'email' => 'clydelenonsantuele@umak.edu.ph','first_name' => 'Clyde  Lenon', 'last_name' => 'Santuele', 'password'=> bcrypt('Default@123!')],
            ['id' => 'd75da919-746b-4c21-b642-b318cc98dd05','role_id' => '6bd2e804-0a95-439c-a53a-36af2e3a472c' , 'email' => 'justinelouisecarunungan@umak.edu.ph','first_name' => 'Justine Louise', 'last_name' => 'Carunungan', 'password'=> bcrypt('Default@123!')],
            ['id' => '3042099f-0fd1-473b-b5d4-e0da9cea613b','role_id' => '9908a9ad-c1fe-4394-bdb6-e0a271d591c6' , 'email' => 'unitapstore@umak.edu.ph','first_name' => 'Timothy Walter', 'last_name' => 'Cuizon', 'password'=> bcrypt('Default@123!')],
            ['id' => '9ba51b10-7ff0-4a05-b019-1f0ebb6316a8','role_id' => 'a9f6d210-d4d8-44fd-8f48-c9f9669366e9' , 'email' => 'lorenzjeddalvarez@umak.edu.ph','first_name' => 'Lorenz Jedd', 'last_name' => 'Alvarez', 'password'=> bcrypt('Default@123!')],
        ]);
    }
}

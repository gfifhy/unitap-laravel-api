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
        $role = DB::table('roles')->where('slug', '=', 'admin')->first();
        DB::table('users')->insert([
            ['id' => Str::uuid(),'role_id' => $role->id , 'email' => 'umakadmin.1972@umak.edu.ph', 'password'=> bcrypt('UmakAdmin@#1972!')]
        ]);
    }
}

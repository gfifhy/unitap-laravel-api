<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Admin', 'Student', 'Security Guard', 'Store', 'Guidance Staff'];
        for ($i=0; $i<count($roles); $i++){
            DB::table('roles')->insert([
                ['id' => Str::uuid(), 'name' => $roles[$i], 'slug' => str::slug($roles[$i])],
            ]);
        }
    }
}

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
        $id = ['5762ddd2-dad9-4729-b77a-7b06ea14eb3e', '69717d90-078a-40b8-afd0-0f20a654b78e', '6bd2e804-0a95-439c-a53a-36af2e3a472c', '9908a9ad-c1fe-4394-bdb6-e0a271d591c6', 'a9f6d210-d4d8-44fd-8f48-c9f9669366e9'];
        for ($i=0; $i<count($roles); $i++){
            DB::table('roles')->insert([
                ['id' => $id[$i], 'name' => $roles[$i], 'slug' => str::slug($roles[$i])],
            ]);
        }
    }
}

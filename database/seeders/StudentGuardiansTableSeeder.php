<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentGuardiansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_guardians')->insert([
            ['id' => 'd25d089a-a682-466e-a631-73f6ee1d4c27', 'first_name' => 'John', 'last_name' => 'Doe', 'contact' => '09234536424'],
        ]);
    }
}

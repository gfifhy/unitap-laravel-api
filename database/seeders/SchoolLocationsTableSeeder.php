<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $location = ['Main Entrance', 'Oval Entrance', 'Rear Entrance', 'Building 1', 'Building 2', 'Building 3', 'Admin Building', 'HPSB'];
        $id = ['4b090ffc-41f8-498d-973a-5944f4fdeaad','2c33bb95-9779-4db2-90db-4267fb835aa6','6cfd1f85-1a80-4fbc-bd83-812fbefbe330','fa23d632-865f-4627-b4e8-374c35ec6412','9c340e4b-e4d4-4fca-9014-584e07a3982e','9e16180c-bd91-41e9-a796-a0c4ef0b021f','231eeaaa-28a5-409e-b1f4-e5c2f27b93fc','a6ebcc40-e62d-4d08-9018-b67a53e3d5d8'];
        for($i=0; $i < count($location); $i++){
            DB::table('school_locations')->insert([
                ['id' => $id[$i], 'location' => $location[$i], 'location_slug' => Str::slug($location[$i])]
            ]);
        }
    }
}

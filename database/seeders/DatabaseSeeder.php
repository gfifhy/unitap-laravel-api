<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(RolesTableSeeder::class);
        $this->call(SecurityGuardsTableSeeder::class);
        $this->call(StoresTableSeeder::class);
        $this->call(StudentGuardiansTableSeeder::class);
        $this->call(StudentsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ViolationsTableSeeder::class);
        $this->call(WalletsTableSeeder::class);
    }
}

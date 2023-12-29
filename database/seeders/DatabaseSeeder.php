<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUser = User::Create([
                'name' => 'amdinUser',
                'phone' => 'test@example.com',
                'password' => 'AdminPasword',
        ]);

        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'pharmacist']);

        $adminUser->assignRole($adminRole);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

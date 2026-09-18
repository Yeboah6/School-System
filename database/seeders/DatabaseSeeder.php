<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['name' => 'Hillcrest Academy'],
            [
                'email' => 'admin@hillcrest.edu.gh',
                'phone' => '+233200000000',
                'currency' => 'GHS',
                'timezone' => 'Africa/Accra',
                'status' => 'active',
            ]
        );

        $role = Role::firstOrCreate(
            ['slug' => 'super-administrator'],
            [
                'name' => 'Super Administrator',
                'description' => 'Full administrative access to the school system.',
                'is_system' => true,
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'admin@hillcrest.edu.gh'],
            [
                'school_id' => $school->id,
                'name' => 'Hillcrest Administrator',
                'password' => Hash::make('School@123'),
                'is_active' => true,
            ]
        );

        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}

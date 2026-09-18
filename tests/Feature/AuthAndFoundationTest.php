<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a user to log in and reach the dashboard', function () {
    $school = School::create([
        'name' => 'Hillcrest Academy',
        'email' => 'admin@hillcrest.edu.gh',
        'phone' => '+233200000000',
        'currency' => 'GHS',
        'timezone' => 'Africa/Accra',
    ]);

    $role = Role::firstOrCreate([
        'slug' => 'super-administrator',
    ], [
        'name' => 'Super Administrator',
        'description' => 'System administrator',
        'is_system' => true,
    ]);

    $user = User::factory()->create([
        'school_id' => $school->id,
        'email' => 'admin@hillcrest.edu.gh',
        'password' => bcrypt('password'),
    ]);

    $user->roles()->attach($role->id);

    $response = $this->post('/login', [
        'email' => 'admin@hillcrest.edu.gh',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('redirects guests to the login page when they try to access the dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

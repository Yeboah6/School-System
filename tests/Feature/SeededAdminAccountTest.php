<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds a ready-to-use super administrator account', function () {
    $this->artisan('db:seed')->assertSuccessful();

    $this->assertDatabaseHas('roles', ['slug' => 'super-administrator']);
    $this->assertDatabaseHas('users', ['email' => 'admin@hillcrest.edu.gh']);

    $user = User::where('email', 'admin@hillcrest.edu.gh')->first();

    expect($user)->not->toBeNull()
        ->and($user->roles->contains('slug', 'super-administrator'))->toBeTrue()
        ->and(Hash::check('School@123', $user->password))->toBeTrue();
});

<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function phaseEightUser(string $roleName): array
{
    $school = School::create(['name' => $roleName.' School']);
    $role = Role::where('name', $roleName)->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('shows the parent portal only for a linked parent account', function () {
    [$user, $school] = phaseEightUser('Parent');
    $parent = $school->parents()->create([
        'user_id' => $user->id,
        'first_name' => 'Ama',
        'last_name' => 'Mensah',
        'email' => $user->email,
        'status' => 'active',
    ]);
    $student = $school->students()->create(['first_name' => 'Kojo', 'last_name' => 'Mensah', 'status' => 'active']);
    $parent->students()->attach($student->id, ['relationship' => 'Mother', 'is_primary' => true]);

    $this->actingAs($user)->get('/parent-portal')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Portals/Parent')->has('students', 1));
});

it('shows the teacher portal only for a linked teacher account', function () {
    [$user, $school] = phaseEightUser('Teacher');
    $staff = $school->staff()->create([
        'user_id' => $user->id,
        'first_name' => 'Kofi',
        'last_name' => 'Owusu',
        'role' => 'Teacher',
        'email' => $user->email,
        'status' => 'active',
    ]);

    $this->actingAs($user)->get('/teacher-portal')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Portals/Teacher')->where('teacher.id', $staff->id));
});

it('delivers an announcement notification to linked parent accounts', function () {
    [$admin, $school] = phaseEightUser('School Administrator');
    [$parentUser] = phaseEightUser('Parent');
    $parentUser->update(['school_id' => $school->id]);
    $school->parents()->create(['user_id' => $parentUser->id, 'first_name' => 'Esi', 'last_name' => 'Doe', 'status' => 'active']);

    $this->actingAs($admin)->post('/announcements', [
        'title' => 'Fee Reminder',
        'message' => 'Please review outstanding fees.',
        'audience' => 'parents',
        'published_at' => '2026-10-05 09:00:00',
        'status' => 'published',
    ])->assertRedirect();

    $this->assertDatabaseHas('notifications', [
        'notifiable_type' => User::class,
        'notifiable_id' => $parentUser->id,
    ]);
});

it('provisions a teacher portal account when staff is created', function () {
    [$admin, $school] = phaseEightUser('School Administrator');

    $this->actingAs($admin)->post('/staff', [
        'first_name' => 'Nana',
        'last_name' => 'Mensah',
        'role' => 'Teacher',
    ])->assertRedirect('/staff')->assertSessionHas('portal_credentials');

    $staff = $school->staff()->where('first_name', 'Nana')->firstOrFail();
    $this->assertNotNull($staff->user_id);
    $this->assertDatabaseHas('roles', ['name' => 'Teacher']);
    $this->assertDatabaseHas('role_user', ['user_id' => $staff->user_id]);
});

it('provisions a parent portal account during student enrollment', function () {
    [$admin, $school] = phaseEightUser('School Administrator');

    $this->actingAs($admin)->post('/students', [
        'first_name' => 'Esi',
        'last_name' => 'Mensah',
        'parent_name' => 'Ama Mensah',
        'parent_phone' => '+233200000123',
    ])->assertRedirect('/students')->assertSessionHas('portal_credentials');

    $parent = $school->parents()->where('phone', '+233200000123')->firstOrFail();
    $this->assertNotNull($parent->user_id);
    $this->assertDatabaseHas('role_user', ['user_id' => $parent->user_id]);
});

it('redirects provisioned parent credentials to the parent portal', function () {
    [$admin, $school] = phaseEightUser('School Administrator');

    $response = $this->actingAs($admin)->post('/students', [
        'first_name' => 'Kofi',
        'last_name' => 'Mensah',
        'parent_name' => 'Efe Mensah',
        'parent_email' => 'efe@example.test',
    ]);

    $credentials = app('session.store')->get('portal_credentials');
    $portalUser = User::where('email', $credentials['email'])->firstOrFail();
    expect($portalUser->hasRole('Parent'))->toBeTrue();

    $this->post('/logout');
    $this->post('/login', [
        'email' => $credentials['email'],
        'password' => $credentials['password'],
    ])->assertRedirect('/parent-portal');
});
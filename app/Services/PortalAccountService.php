<?php

namespace App\Services;

use App\Models\ParentGuardian;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PortalAccountService
{
    public function provisionParent(ParentGuardian $parent): ?array
    {
        return $this->provision(
            $parent->user_id,
            $parent->school_id,
            trim($parent->first_name.' '.$parent->last_name),
            $parent->email,
            'Parent',
            'parent',
            $parent,
        );
    }

    public function provisionTeacher(Staff $staff): ?array
    {
        $roleName = strcasecmp(trim($staff->role), 'accountant') === 0 ? 'Accountant' : 'Teacher';

        return $this->provision(
            $staff->user_id,
            $staff->school_id,
            trim($staff->first_name.' '.$staff->last_name),
            $staff->email,
            $roleName,
            strtolower($roleName),
            $staff,
        );
    }

    private function provision(?int $existingUserId, int $schoolId, string $name, ?string $email, string $roleName, string $identifierPrefix, ParentGuardian|Staff $profile): ?array
    {
        if ($existingUserId) {
            return null;
        }

        $loginEmail = $email ?: $identifierPrefix.'.'.$profile->id.'@portal.local';
        if (User::where('email', $loginEmail)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email address is already used by another login account.',
            ]);
        }

        $password = Str::random(12);
        $user = User::create([
            'school_id' => $schoolId,
            'name' => $name,
            'email' => $loginEmail,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        $role = Role::where('name', $roleName)->firstOrFail();
        $user->roles()->attach($role->id);
        $profile->forceFill(['user_id' => $user->id])->save();

        return [
            'role' => $roleName,
            'name' => $name,
            'email' => $loginEmail,
            'password' => $password,
        ];
    }
}
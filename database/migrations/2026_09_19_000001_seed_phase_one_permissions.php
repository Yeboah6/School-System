<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'view-system-dashboard' => 'View system dashboard',
            'manage-school-setup' => 'Manage school setup',
            'view-students' => 'View students',
            'create-students' => 'Create students',
            'manage-student-records' => 'Manage student records',
            'view-staff' => 'View staff',
            'manage-staff' => 'Manage staff',
        ];

        foreach ($permissions as $slug => $name) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $name . '.',
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_keys($permissions))
            ->pluck('id', 'slug');

        $rolePermissions = [
            'super-administrator' => array_keys($permissions),
            'school-administrator' => array_keys($permissions),
            'principal' => ['view-system-dashboard', 'manage-school-setup', 'view-students', 'manage-student-records', 'view-staff'],
            'vice-principal' => ['view-system-dashboard', 'manage-school-setup', 'view-students', 'manage-student-records', 'view-staff'],
            'head-teacher' => ['view-system-dashboard', 'view-students', 'manage-student-records', 'view-staff'],
            'teacher' => ['view-students', 'manage-student-records'],
            'receptionist' => ['view-students', 'create-students', 'view-staff'],
        ];

        foreach ($rolePermissions as $roleSlug => $slugs) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');

            if (! $roleId) {
                continue;
            }

            foreach ($slugs as $slug) {
                $permissionId = $permissionIds->get($slug);

                if ($permissionId) {
                    DB::table('permission_role')->updateOrInsert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $slugs = [
            'view-system-dashboard',
            'manage-school-setup',
            'view-students',
            'create-students',
            'manage-student-records',
            'view-staff',
            'manage-staff',
        ];

        $permissionIds = DB::table('permissions')->whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
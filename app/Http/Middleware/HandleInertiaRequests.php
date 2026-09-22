<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            $user->loadMissing(['school', 'roles.permissions']);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => (bool) $user->is_active,
                    'school' => $user->school?->only(['id', 'name', 'currency', 'timezone']),
                    'roles' => $user->roles->pluck('name')->values(),
                    'permissions' => $user->roles->flatMap->permissions->pluck('slug')->unique()->values(),
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'portal_credentials' => $request->session()->get('portal_credentials'),
            ],
        ];
    }
}

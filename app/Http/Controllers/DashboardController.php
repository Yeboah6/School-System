<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\School;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(Gate::check('view-system-dashboard'), 403);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(
            ['name' => 'School System'],
            [
                'email' => 'admin@hillcrest.edu.gh',
                'phone' => '+233200000000',
                'currency' => 'GHS',
                'timezone' => 'Africa/Accra',
                'status' => 'active',
            ]
        );

        return Inertia::render('Dashboard/Index', [
            'user' => $request->user()->only(['id', 'name', 'email']),
            'stats' => [
                ['label' => 'Total Students', 'value' => $school->students()->count()],
                ['label' => 'Teachers', 'value' => $school->staff()->count()],
                ['label' => 'Today Attendance', 'value' => '0%'],
                ['label' => 'Outstanding Fees', 'value' => 'GHS 0.00'],
            ],
        ]);
    }
}

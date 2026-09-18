<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(Gate::check('view-system-dashboard'), 403);

        return Inertia::render('Dashboard/Index', [
            'user' => $request->user()->only(['id', 'name', 'email']),
            'stats' => [
                ['label' => 'Total Students', 'value' => 0],
                ['label' => 'Teachers', 'value' => 0],
                ['label' => 'Today Attendance', 'value' => '0%'],
                ['label' => 'Outstanding Fees', 'value' => 'GHS 0.00'],
            ],
        ]);
    }
}

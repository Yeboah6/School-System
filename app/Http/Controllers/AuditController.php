<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $logs = AuditLog::with('user')->where('school_id', $request->user()->school_id)->latest()->paginate(50)->withQueryString();
        return Inertia::render('Audit/Index', ['logs' => $logs->through(fn (AuditLog $log) => ['id' => $log->id, 'action' => $log->action, 'description' => $log->description, 'user' => $log->user?->name, 'ip_address' => $log->ip_address, 'created_at' => $log->created_at?->format('Y-m-d H:i')])]);
    }
}
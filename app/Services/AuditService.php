<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditService
{
    public function record(Request $request, string $action, ?string $description = null, mixed $auditable = null, array $metadata = []): void
    {
        $user = $request->user();
        AuditLog::create([
            'school_id' => $user?->school_id,
            'user_id' => $user?->id,
            'action' => $action,
            'auditable_type' => is_object($auditable) ? $auditable::class : null,
            'auditable_id' => is_object($auditable) ? $auditable->getKey() : null,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $request->ip(),
        ]);
    }
}
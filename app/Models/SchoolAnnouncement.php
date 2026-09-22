<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'message',
        'audience',
        'published_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
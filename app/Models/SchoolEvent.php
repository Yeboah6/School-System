<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'event_type',
        'event_date',
        'location',
        'description',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}

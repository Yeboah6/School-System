<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'role',
        'department_name',
        'email',
        'phone',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}

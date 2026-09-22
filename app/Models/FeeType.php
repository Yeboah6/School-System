<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'name', 'code', 'status'];

    public function school() { return $this->belongsTo(School::class); }
    public function structures() { return $this->hasMany(FeeStructure::class); }
}
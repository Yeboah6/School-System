<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'name', 'is_default'];
    protected $casts = ['is_default' => 'boolean'];

    public function school() { return $this->belongsTo(School::class); }
    public function items() { return $this->hasMany(GradingScaleItem::class)->orderByDesc('minimum_mark'); }
    public function assessments() { return $this->hasMany(Assessment::class); }
}
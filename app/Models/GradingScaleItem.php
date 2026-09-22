<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['grading_scale_id', 'grade', 'minimum_mark', 'maximum_mark', 'grade_point', 'remark'];
    protected $casts = ['minimum_mark' => 'decimal:2', 'maximum_mark' => 'decimal:2', 'grade_point' => 'decimal:2'];

    public function gradingScale() { return $this->belongsTo(GradingScale::class); }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('school_branches')->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('attendance_date');
            $table->string('session')->default('daily');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['school_id', 'class_id', 'attendance_date', 'session'], 'attendance_session_unique');
            $table->index(['school_id', 'attendance_date', 'branch_id'], 'attendance_session_lookup');
        });

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['attendance_session_id', 'student_id'], 'attendance_student_unique');
            $table->index(['student_id', 'status'], 'attendance_student_status_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('attendance_sessions');
    }
};
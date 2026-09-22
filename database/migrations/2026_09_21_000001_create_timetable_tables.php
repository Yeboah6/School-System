<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['school_id', 'class_id', 'day_of_week', 'start_time'], 'timetable_slot_class_day_time_unique');
            $table->unique(['school_id', 'staff_id', 'day_of_week', 'start_time'], 'timetable_slot_staff_day_time_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};

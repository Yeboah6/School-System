<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('end_of_term');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['school_id', 'academic_year_id', 'term_id'], 'exam_period_idx');
        });

        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['school_id', 'name'], 'grading_scale_school_name_unique');
        });

        Schema::create('grading_scale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained()->cascadeOnDelete();
            $table->string('grade');
            $table->decimal('minimum_mark', 5, 2);
            $table->decimal('maximum_mark', 5, 2);
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->index(['grading_scale_id', 'minimum_mark', 'maximum_mark'], 'grading_item_range_idx');
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('grading_scale_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('maximum_marks', 8, 2)->default(100);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['examination_id', 'subject_id', 'class_id'], 'assessment_exam_subject_class_unique');
        });

        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('marks', 8, 2);
            $table->decimal('maximum_marks', 8, 2);
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 5, 2)->nullable();
            $table->text('teacher_comment')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['assessment_id', 'student_id'], 'student_result_assessment_unique');
            $table->index(['school_id', 'student_id', 'status'], 'student_result_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_results');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('grading_scale_items');
        Schema::dropIfExists('grading_scales');
        Schema::dropIfExists('examinations');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->nullable()->after('id');
            $table->string('nationality')->nullable()->after('gender');
            $table->string('email')->nullable()->after('nationality');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->date('admission_date')->nullable()->after('date_of_birth');
            $table->string('previous_school')->nullable()->after('admission_date');
            $table->string('student_type')->nullable()->after('previous_school');
            $table->index(['school_id', 'status', 'admission_no']);
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->string('emergency_contact_name')->nullable()->after('emergency_contact');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->after('id');
            $table->string('gender')->nullable()->after('last_name');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->text('address')->nullable()->after('phone');
            $table->string('position')->nullable()->after('role');
            $table->string('qualification')->nullable()->after('position');
            $table->string('employment_type')->nullable()->after('qualification');
            $table->date('joining_date')->nullable()->after('employment_type');
            $table->index(['school_id', 'branch_id', 'status']);
        });

        Schema::table('student_notes', function (Blueprint $table) {
            $table->string('priority')->default('normal')->after('category');
            $table->string('visibility')->default('staff_only')->after('priority');
            $table->date('follow_up_date')->nullable()->after('visibility');
            $table->string('follow_up_status')->default('not_required')->after('follow_up_date');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->index(['student_id', 'category', 'priority', 'follow_up_status'], 'student_notes_filter_idx');
        });
    }

    public function down(): void
    {
        Schema::table('student_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('updated_by');
            $table->dropIndex('student_notes_filter_idx');
            $table->dropColumn(['priority', 'visibility', 'follow_up_date', 'follow_up_status']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'branch_id', 'status']);
            $table->dropColumn(['employee_id', 'gender', 'date_of_birth', 'address', 'position', 'qualification', 'employment_type', 'joining_date']);
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact_name', 'emergency_contact_relationship']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'status', 'admission_no']);
            $table->dropColumn(['student_id', 'nationality', 'email', 'phone', 'address', 'admission_date', 'previous_school', 'student_type']);
        });
    }
};
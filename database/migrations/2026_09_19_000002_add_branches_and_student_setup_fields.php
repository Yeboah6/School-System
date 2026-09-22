<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 30);
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['school_id', 'code']);
            $table->unique(['school_id', 'name']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('school_id')->constrained('school_branches')->nullOnDelete();
            $table->index(['school_id', 'branch_id', 'status']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('school_id')->constrained('school_branches')->nullOnDelete();
            $table->index(['school_id', 'branch_id', 'status']);
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->string('occupation')->nullable()->after('address');
            $table->string('emergency_contact')->nullable()->after('occupation');
            $table->foreignId('user_id')->nullable()->after('school_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('school_id')->constrained('school_branches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('staff', fn (Blueprint $table) => $table->dropConstrainedForeignId('branch_id'));
        Schema::table('parents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['occupation', 'emergency_contact']);
        });
        Schema::table('students', fn (Blueprint $table) => $table->dropConstrainedForeignId('branch_id'));
        Schema::table('classes', fn (Blueprint $table) => $table->dropConstrainedForeignId('branch_id'));
        Schema::dropIfExists('school_branches');
    }
};
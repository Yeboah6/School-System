<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('event_type')->default('academic');
            $table->date('event_date');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();

            $table->unique(['school_id', 'title', 'event_date'], 'school_event_title_date_unique');
            $table->index(['school_id', 'event_date'], 'school_event_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_events');
    }
};

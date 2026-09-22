<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('audience')->default('all');
            $table->dateTime('published_at');
            $table->dateTime('expires_at')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->unique(['school_id', 'title', 'published_at'], 'school_announcement_title_publish_unique');
            $table->index(['school_id', 'status', 'published_at'], 'school_announcement_status_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_announcements');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_category_id')
                ->constrained('project_categories')
                ->restrictOnDelete();

            $table->string('slug')->unique();

            $table->string('title_en');
            $table->string('title_fa');

            $table->text('short_description_en')->nullable();
            $table->text('short_description_fa')->nullable();

            $table->string('location_en')->nullable();
            $table->string('location_fa')->nullable();

            $table->unsignedSmallInteger('year')->nullable();

            $table->string('cover_image_path');
            $table->string('mobile_cover_image_path')->nullable();

            $table->string('status', 30)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['project_category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

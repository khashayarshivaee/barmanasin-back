<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_heroes', function (Blueprint $table): void {
            $table->id();

            // Main content
            $table->string('eyebrow_en')->nullable();
            $table->string('eyebrow_fa')->nullable();

            $table->string('title_en')->nullable();
            $table->string('title_fa')->nullable();

            $table->text('description_en')->nullable();
            $table->text('description_fa')->nullable();

            // Text displayed when the visual expands on scroll
            $table->text('fullscreen_caption_en')->nullable();
            $table->text('fullscreen_caption_fa')->nullable();

            // CTA
            $table->string('cta_title_en')->nullable();
            $table->string('cta_title_fa')->nullable();
            $table->string('cta_path')->nullable();

            // Media
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();

            $table->string('image_alt_en')->nullable();
            $table->string('image_alt_fa')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_heroes');
    }
};

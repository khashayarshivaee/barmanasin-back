<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_image_showcase_slides', function (Blueprint $table) {
            $table->id();

            $table->foreignId('home_image_showcase_section_id')
                ->constrained('home_image_showcase_sections')
                ->cascadeOnDelete();

            $table->string('image_path');

            $table->string('title_en');
            $table->string('title_fa')->nullable();

            $table->text('description_en')->nullable();
            $table->text('description_fa')->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'home_image_showcase_section_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_image_showcase_slides');
    }
};

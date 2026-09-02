<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_engineering_approach_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->constrained('home_engineering_approach_sections')
                ->cascadeOnDelete();


            $table->string('title_en');

            $table->string('title_fa');


            $table->text('description_en')
                ->nullable();

            $table->text('description_fa')
                ->nullable();


            $table->unsignedInteger('sort_order')
                ->default(0);


            $table->boolean('is_active')
                ->default(true);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_engineering_approach_steps');
    }
};

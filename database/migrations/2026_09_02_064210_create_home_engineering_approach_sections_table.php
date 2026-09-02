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
        Schema::create('home_engineering_approach_sections', function (Blueprint $table) {
            $table->id();

            $table->string('eyebrow_en')
                ->nullable();

            $table->string('eyebrow_fa')
                ->nullable();


            $table->string('title_en');

            $table->string('title_fa');


            $table->text('description_en')
                ->nullable();

            $table->text('description_fa')
                ->nullable();


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
        Schema::dropIfExists('home_engineering_approach_sections');
    }
};

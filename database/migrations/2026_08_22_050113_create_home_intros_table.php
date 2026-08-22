<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_intros', function (Blueprint $table): void {
            $table->id();

            $table->string('eyebrow_en')->nullable();
            $table->string('eyebrow_fa')->nullable();

            $table->text('title_en')->nullable();
            $table->text('title_fa')->nullable();

            $table->text('description_en')->nullable();
            $table->text('description_fa')->nullable();

            $table->string('cta_title_en')->nullable();
            $table->string('cta_title_fa')->nullable();
            $table->string('cta_path')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_intros');
    }
};

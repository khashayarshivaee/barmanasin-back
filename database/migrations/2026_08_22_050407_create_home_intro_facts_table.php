<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_intro_facts', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('home_intro_id')
                ->constrained('home_intros')
                ->cascadeOnDelete();

            $table->string('value');

            $table->string('label_en')->nullable();
            $table->string('label_fa')->nullable();

            $table->string('suffix_en')->nullable();
            $table->string('suffix_fa')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'home_intro_id',
                'is_active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_intro_facts');
    }
};

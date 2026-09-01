<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capability_focus_points', function (Blueprint $table) {
            $table->id();

            $table->foreignId('capability_id')
                ->constrained('capabilities')
                ->cascadeOnDelete();

            $table->string('title_en');
            $table->string('title_fa');

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'capability_id',
                'is_active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capability_focus_points');
    }
};

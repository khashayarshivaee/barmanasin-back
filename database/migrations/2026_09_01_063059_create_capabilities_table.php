<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capabilities', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();

            $table->string('title_en');
            $table->string('title_fa');

            $table->text('short_description_en')->nullable();
            $table->text('short_description_fa')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->string('status', 30)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'status',
                'is_active',
                'published_at',
            ]);

            $table->index([
                'is_active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capabilities');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_mega_menu_sections', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('header_menu_item_id')
                ->constrained('header_menu_items')
                ->cascadeOnDelete();

            $table->string('title_en')->nullable();
            $table->string('title_fa')->nullable();

            $table
                ->unsignedInteger('sort_order')
                ->default(0);

            $table
                ->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'header_menu_item_id',
                'is_active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_mega_menu_sections');
    }
};

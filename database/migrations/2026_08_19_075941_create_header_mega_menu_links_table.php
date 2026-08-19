<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_mega_menu_links', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('header_mega_menu_section_id')
                ->constrained('header_mega_menu_sections')
                ->cascadeOnDelete();

            $table->string('title_en');
            $table->string('title_fa');

            $table->string('description_en')->nullable();
            $table->string('description_fa')->nullable();

            $table->string('path');

            $table
                ->string('link_type', 20)
                ->default('internal');

            $table
                ->boolean('open_in_new_tab')
                ->default(false);

            $table->string('icon')->nullable();

            $table
                ->unsignedInteger('sort_order')
                ->default(0);

            $table
                ->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index(
                [
                    'header_mega_menu_section_id',
                    'is_active',
                    'sort_order',
                ],
                'mega_links_section_active_order_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_mega_menu_links');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_footer_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_footer_id')
                ->constrained('site_footers')
                ->cascadeOnDelete();

            $table->string('group');

            $table->string('title_en');

            $table->string('title_fa');

            $table->string('url');

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'site_footer_id',
                'group',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_footer_links');
    }
};

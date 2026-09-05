<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_footers', function (Blueprint $table) {
            $table->id();

            $table->string('logo_path')
                ->nullable();

            $table->text('description_en')
                ->nullable();

            $table->text('description_fa')
                ->nullable();

            $table->text('address_en')
                ->nullable();

            $table->text('address_fa')
                ->nullable();

            $table->string('phone')
                ->nullable();

            $table->string('fax')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('copyright_en')
                ->nullable();

            $table->string('copyright_fa')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_footers');
    }
};

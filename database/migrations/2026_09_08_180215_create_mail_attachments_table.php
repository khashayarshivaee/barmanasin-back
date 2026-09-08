<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_attachments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('original_name');

            $table->string('stored_name')
                ->nullable();

            $table->string('path');

            $table->string('disk')
                ->default('local');

            $table->string('mime_type')
                ->nullable();

            $table->unsignedBigInteger('size');

            $table->string('status')
                ->default('ready');

            $table->string('checksum')
                ->nullable();

            $table->boolean('is_used')
                ->default(false);

            $table->timestamp('used_at')
                ->nullable();

            $table->timestamp('expires_at')
                ->nullable();

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('mail_attachments');
    }
};

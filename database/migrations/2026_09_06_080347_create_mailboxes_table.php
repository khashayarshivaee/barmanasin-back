<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailboxes', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
             |--------------------------------------------------------------------------
             | Address
             |--------------------------------------------------------------------------
             */

            $table->string('address')
                ->unique();

            $table->string('local_part');

            $table->string('domain')
                ->default('barmanasin.com');


            /*
             |--------------------------------------------------------------------------
             | Mail Provider
             |--------------------------------------------------------------------------
             */

            $table->string('provider')
                ->default('stalwart');


            /*
             |--------------------------------------------------------------------------
             | External Mail Server ID
             |--------------------------------------------------------------------------
             */

            $table->string('external_id')
                ->nullable()
                ->unique();


            /*
             |--------------------------------------------------------------------------
             | Storage
             |--------------------------------------------------------------------------
             */

            $table->unsignedInteger('quota_mb')
                ->default(5120);

            $table->unsignedInteger('used_storage_mb')
                ->default(0);


            /*
             |--------------------------------------------------------------------------
             | Status
             |--------------------------------------------------------------------------
             */

            $table->enum('status', [
                'pending',
                'active',
                'suspended',
                'disabled',
            ])
                ->default('pending');


            $table->timestamps();


            $table->index([
                'domain',
                'local_part',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('mailboxes');
    }
};

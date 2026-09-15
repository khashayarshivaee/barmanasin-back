<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mailbox_watch_states', function (Blueprint $table) {

            $table->id();

            $table->foreignId('mailbox_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * Unique identifier of the detected message.
             *
             * For Maildir this will initially be the filename
             * inside Maildir/new.
             */
            $table->string('message_key');

            $table->timestamp('detected_at');


            $table->timestamps();


            $table->unique([
                'mailbox_id',
                'message_key',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailbox_watch_states');
    }
};

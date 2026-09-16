<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_pinned_messages', function (Blueprint $table) {

            $table->id();


            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->string('mailbox');


            $table->string('message_uid');


            $table->unsignedInteger('position')
                ->default(0);


            $table->timestamps();



            $table->unique([
                'user_id',
                'mailbox',
                'message_uid',
            ]);


            $table->index([
                'user_id',
                'position',
            ]);

        });
    }



    public function down(): void
    {
        Schema::dropIfExists(
            'mail_pinned_messages'
        );
    }
};

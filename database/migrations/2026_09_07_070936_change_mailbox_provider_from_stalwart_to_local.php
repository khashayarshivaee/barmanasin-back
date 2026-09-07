<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('mailboxes')
            ->where('provider', 'stalwart')
            ->update([
                'provider' => 'local',
            ]);

        Schema::table('mailboxes', function (Blueprint $table) {
            $table->string('provider')
                ->default('local')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('mailboxes')
            ->where('provider', 'local')
            ->update([
                'provider' => 'stalwart',
            ]);

        Schema::table('mailboxes', function (Blueprint $table) {
            $table->string('provider')
                ->default('stalwart')
                ->change();
        });
    }
};

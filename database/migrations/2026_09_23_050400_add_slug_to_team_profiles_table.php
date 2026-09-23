<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'team_profiles',
            function (Blueprint $table): void {

                $table
                    ->string('slug')
                    ->nullable()
                    ->unique()
                    ->after('user_id');

            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'team_profiles',
            function (Blueprint $table): void {

                $table->dropUnique([
                    'slug',
                ]);

                $table->dropColumn(
                    'slug',
                );

            }
        );
    }
};

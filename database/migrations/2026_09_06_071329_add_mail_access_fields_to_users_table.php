<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)
                ->nullable()
                ->after('password')
                ->index();

            $table->boolean('is_active')
                ->default(true)
                ->after('role');

            $table->boolean('mailbox_enabled')
                ->default(false)
                ->after('is_active');

            $table->string('mailbox_address')
                ->nullable()
                ->unique()
                ->after('mailbox_enabled');

            $table->string('mailbox_external_id')
                ->nullable()
                ->unique()
                ->after('mailbox_address');

            $table->unsignedInteger('mailbox_quota_mb')
                ->default(5120)
                ->after('mailbox_external_id');

            $table->boolean('must_change_password')
                ->default(false)
                ->after('mailbox_quota_mb');

            $table->timestamp('activated_at')
                ->nullable()
                ->after('must_change_password');

            $table->timestamp('suspended_at')
                ->nullable()
                ->after('activated_at');
        });

        /*
         * Preserve the existing administrator account
         * and convert it to the new role-based access model.
         */
        DB::table('users')
            ->where('email', 'khashayarshivaee@gmail.com')
            ->update([
                'role' => 'super_admin',
                'is_active' => true,
                'mailbox_enabled' => false,
                'must_change_password' => false,
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['mailbox_address']);
            $table->dropUnique(['mailbox_external_id']);

            $table->dropColumn([
                'role',
                'is_active',
                'mailbox_enabled',
                'mailbox_address',
                'mailbox_external_id',
                'mailbox_quota_mb',
                'must_change_password',
                'activated_at',
                'suspended_at',
            ]);
        });
    }
};

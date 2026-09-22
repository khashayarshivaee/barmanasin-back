<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_profiles', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Public Identity
            |--------------------------------------------------------------------------
            */

            $table->string('name_en')
                ->nullable();

            $table->string('name_fa')
                ->nullable();

            $table->string('job_title_en')
                ->nullable();

            $table->string('job_title_fa')
                ->nullable();

            $table->string('department_en')
                ->nullable();

            $table->string('department_fa')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Biography
            |--------------------------------------------------------------------------
            */

            $table->text('bio_en')
                ->nullable();

            $table->text('bio_fa')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            $table->string('photo_path')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Public Contact
            |--------------------------------------------------------------------------
            */

            $table->string('linkedin_url')
                ->nullable();

            $table->string('public_email')
                ->nullable();

            $table->string('public_phone')
                ->nullable();

            $table->boolean('show_email')
                ->default(false);

            $table->boolean('show_phone')
                ->default(false);


            /*
            |--------------------------------------------------------------------------
            | Website Visibility
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_public')
                ->default(false);


            /*
            |--------------------------------------------------------------------------
            | Approval Workflow
            |--------------------------------------------------------------------------
            |
            | draft
            | pending
            | approved
            | rejected
            |
            */

            $table->string('approval_status', 20)
                ->default('draft')
                ->index();

            $table->timestamp('submitted_at')
                ->nullable();

            $table->timestamp('approved_at')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Website Ordering
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->index();


            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Useful Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'is_public',
                'approval_status',
                'sort_order',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('team_profiles');
    }
};

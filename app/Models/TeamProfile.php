<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',

    'name_en',
    'name_fa',

    'job_title_en',
    'job_title_fa',

    'department_en',
    'department_fa',

    'bio_en',
    'bio_fa',

    'photo_path',

    'linkedin_url',

    'public_email',
    'public_phone',

    'show_email',
    'show_phone',

    'is_public',

    'approval_status',

    'submitted_at',
])]
class TeamProfile extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';


    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by',
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->approval_status === self::STATUS_DRAFT;
    }


    public function isPending(): bool
    {
        return $this->approval_status === self::STATUS_PENDING;
    }


    public function isApproved(): bool
    {
        return $this->approval_status === self::STATUS_APPROVED;
    }


    public function isRejected(): bool
    {
        return $this->approval_status === self::STATUS_REJECTED;
    }


    public function isPublishable(): bool
    {
        return $this->is_public
            && $this->isApproved();
    }


    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    public function photoUrl(): ?string
    {
        if (blank($this->photo_path)) {
            return $this->user?->avatarUrl();
        }

        return url(
            Storage::disk('public')->url(
                $this->photo_path,
            ),
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'show_email' => 'boolean',

            'show_phone' => 'boolean',

            'is_public' => 'boolean',

            'sort_order' => 'integer',

            'submitted_at' => 'datetime',

            'approved_at' => 'datetime',
        ];
    }
}

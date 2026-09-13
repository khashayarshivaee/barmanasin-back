<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'is_active',
    'mailbox_enabled',
    'mailbox_address',
    'mailbox_quota_mb',
    'must_change_password',
    'activated_at',
    'suspended_at',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_MAIL_USER = 'mail_user';


    /*
    |--------------------------------------------------------------------------
    | Filament Access
    |--------------------------------------------------------------------------
    */

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        return $this->isActive()
            && $this->isSuperAdmin();
    }


    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->resolvedRole() === self::ROLE_SUPER_ADMIN;
    }


    public function isMailUser(): bool
    {
        return $this->resolvedRole() === self::ROLE_MAIL_USER;
    }


    /*
    |--------------------------------------------------------------------------
    | Account Status
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        if (! array_key_exists('is_active', $this->attributes)) {
            return true;
        }

        return (bool) $this->is_active
            && $this->suspended_at === null;
    }


    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }


    public function isActivated(): bool
    {
        return $this->activated_at !== null;
    }


    /*
    |--------------------------------------------------------------------------
    | Mailbox
    |--------------------------------------------------------------------------
    */

    public function mailbox(): HasOne
    {
        return $this->hasOne(Mailbox::class);
    }


    public function hasMailbox(): bool
    {
        return (bool) $this->mailbox_enabled
            && $this->mailbox !== null;
    }


    public function mailboxIsReady(): bool
    {
        return $this->hasMailbox()
            && $this->mailbox->status === 'active'
            && $this->isActive()
            && $this->isActivated()
            && ! $this->must_change_password;
    }


    /*
    |--------------------------------------------------------------------------
    | Transitional Role Resolution
    |--------------------------------------------------------------------------
    */

    private function resolvedRole(): ?string
    {
        if (
            array_key_exists('role', $this->attributes)
            && filled($this->role)
        ) {
            return $this->role;
        }

        /*
         * Temporary bootstrap fallback.
         *
         * Keeps the existing administrator account accessible before
         * the new migration has been executed.
         */
        if ($this->email === 'khashayarshivaee@gmail.com') {
            return self::ROLE_SUPER_ADMIN;
        }

        return null;
    }

    public function avatarUrl(): ?string
    {
        if (blank($this->avatar_path)) {
            return null;
        }

        return url(
            Storage::disk('public')->url($this->avatar_path)
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
            'email_verified_at' => 'datetime',

            'password' => 'hashed',

            'is_active' => 'boolean',

            'mailbox_enabled' => 'boolean',

            'must_change_password' => 'boolean',

            'mailbox_quota_mb' => 'integer',

            'activated_at' => 'datetime',

            'suspended_at' => 'datetime',
        ];
    }
}

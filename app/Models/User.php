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

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'is_active',
    'mailbox_enabled',
    'mailbox_address',
    'mailbox_external_id',
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
    |
    | Mail users are intentionally NOT allowed into the panel yet.
    |
    | We will enable them only after the dedicated mail workspace and
    | resource-level permissions are implemented.
    |
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

    public function hasMailbox(): bool
    {
        return (bool) $this->mailbox_enabled
            && filled($this->mailbox_address);
    }


    public function mailboxIsReady(): bool
    {
        return $this->hasMailbox()
            && filled($this->mailbox_external_id)
            && $this->isActive();
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

    public function mailbox(): HasOne
    {
        return $this->hasOne(Mailbox::class);
    }
}

<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserAccessToken;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UserAccessTokenService
{
    /*
    |--------------------------------------------------------------------------
    | Issue Tokens
    |--------------------------------------------------------------------------
    */

    public function issueActivationToken(
        User $user,
        ?User $createdBy = null
    ): array {
        return $this->issue(
            user: $user,
            purpose: UserAccessToken::PURPOSE_ACTIVATION,
            createdBy: $createdBy,
            expiresAt: CarbonImmutable::now()->addHours(24),
        );
    }


    public function issuePasswordResetToken(
        User $user,
        ?User $createdBy = null
    ): array {
        return $this->issue(
            user: $user,
            purpose: UserAccessToken::PURPOSE_PASSWORD_RESET,
            createdBy: $createdBy,
            expiresAt: CarbonImmutable::now()->addHour(),
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve
    |--------------------------------------------------------------------------
    */

    public function resolveValidToken(
        string $plainToken,
        string $purpose
    ): ?UserAccessToken {
        $token = UserAccessToken::query()
            ->where(
                'token_hash',
                $this->hashToken($plainToken)
            )
            ->where('purpose', $purpose)
            ->first();

        if (! $token) {
            return null;
        }

        if (! $token->isValid()) {
            return null;
        }

        return $token;
    }


    /*
    |--------------------------------------------------------------------------
    | Consume
    |--------------------------------------------------------------------------
    */

    public function consumeToken(
        string $plainToken,
        string $purpose
    ): UserAccessToken {
        return DB::transaction(function () use (
            $plainToken,
            $purpose
        ): UserAccessToken {
            $token = UserAccessToken::query()
                ->where(
                    'token_hash',
                    $this->hashToken($plainToken)
                )
                ->where('purpose', $purpose)
                ->lockForUpdate()
                ->first();

            if (! $token || ! $token->isValid()) {
                throw new RuntimeException(
                    'This access link is invalid or has expired.'
                );
            }

            $token->forceFill([
                'used_at' => now(),
            ])->save();

            return $token->fresh();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Revoke
    |--------------------------------------------------------------------------
    */

    public function revokeOutstandingTokens(
        User $user,
        string $purpose
    ): void {
        UserAccessToken::query()
            ->where('user_id', $user->getKey())
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->whereNull('revoked_at')
            ->update([
                'revoked_at' => now(),
                'updated_at' => now(),
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Internal
    |--------------------------------------------------------------------------
    */

    private function issue(
        User $user,
        string $purpose,
        ?User $createdBy,
        CarbonImmutable $expiresAt
    ): array {
        return DB::transaction(function () use (
            $user,
            $purpose,
            $createdBy,
            $expiresAt
        ): array {
            /*
             * Only one outstanding token of the same purpose
             * may remain valid for each user.
             */
            $this->revokeOutstandingTokens(
                user: $user,
                purpose: $purpose
            );

            /*
             * 256 bits of cryptographically secure randomness.
             *
             * The plain token is returned once and is NEVER
             * persisted to the database.
             */
            $plainToken = bin2hex(
                random_bytes(32)
            );

            $accessToken = UserAccessToken::query()->create([
                'user_id' => $user->getKey(),

                'created_by' => $createdBy?->getKey(),

                'purpose' => $purpose,

                'token_hash' => $this->hashToken(
                    $plainToken
                ),

                'expires_at' => $expiresAt,
            ]);

            return [
                'token' => $plainToken,
                'access_token' => $accessToken,
                'expires_at' => $expiresAt,
            ];
        });
    }


    private function hashToken(string $plainToken): string
    {
        return hash(
            'sha256',
            $plainToken
        );
    }
}

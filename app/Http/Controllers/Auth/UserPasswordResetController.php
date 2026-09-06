<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAccessToken;
use App\Services\Auth\UserAccessTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserPasswordResetController extends Controller
{
    public function __construct(
        private readonly UserAccessTokenService $tokenService
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Show Reset Form
    |--------------------------------------------------------------------------
    */

    public function show(string $token)
    {
        $accessToken = $this->tokenService->resolveValidToken(
            plainToken: $token,
            purpose: UserAccessToken::PURPOSE_PASSWORD_RESET
        );

        if (! $accessToken) {
            return response()->view(
                'auth.reset-user-access',
                [
                    'state' => 'invalid',
                    'user' => null,
                    'token' => null,
                ],
                410
            );
        }

        /** @var User|null $user */
        $user = $accessToken->user;

        if (
            ! $user
            || ! $user->isActive()
            || ! $user->isActivated()
        ) {
            return response()->view(
                'auth.reset-user-access',
                [
                    'state' => 'unavailable',
                    'user' => $user,
                    'token' => null,
                ],
                403
            );
        }

        return view('auth.reset-user-access', [
            'state' => 'ready',
            'user' => $user,
            'token' => $token,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    public function reset(
        Request $request,
        string $token
    ) {
        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',

                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = DB::transaction(function () use (
            $token,
            $validated
        ): User {
            $accessToken = $this->tokenService->consumeToken(
                plainToken: $token,
                purpose: UserAccessToken::PURPOSE_PASSWORD_RESET
            );

            /** @var User $user */
            $user = User::query()
                ->lockForUpdate()
                ->findOrFail(
                    $accessToken->user_id
                );

            if (
                ! $user->isActive()
                || ! $user->isActivated()
            ) {
                abort(
                    403,
                    'This account is currently unavailable.'
                );
            }

            $user->forceFill([
                'password' => $validated['password'],

                /*
                 * Rotating remember_token invalidates persistent
                 * "remember me" authentication for the old password.
                 */
                'remember_token' => Str::random(60),

                'must_change_password' => false,
            ])->save();

            return $user;
        });

        return view('auth.reset-user-access', [
            'state' => 'success',
            'user' => $user,
            'token' => null,
        ]);
    }
}

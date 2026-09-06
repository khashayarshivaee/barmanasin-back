<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAccessToken;
use App\Services\Auth\UserAccessTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserActivationController extends Controller
{
    public function __construct(
        private readonly UserAccessTokenService $tokenService
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Show Activation Form
    |--------------------------------------------------------------------------
    */

    public function show(string $token)
    {
        $accessToken = $this->tokenService->resolveValidToken(
            plainToken: $token,
            purpose: UserAccessToken::PURPOSE_ACTIVATION
        );

        if (! $accessToken) {
            return response()->view(
                'auth.activate-user',
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

        if (! $user || ! $user->isActive()) {
            return response()->view(
                'auth.activate-user',
                [
                    'state' => 'unavailable',
                    'user' => $user,
                    'token' => null,
                ],
                403
            );
        }

        return view('auth.activate-user', [
            'state' => 'ready',
            'user' => $user,
            'token' => $token,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Activate Account
    |--------------------------------------------------------------------------
    */

    public function activate(
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
                purpose: UserAccessToken::PURPOSE_ACTIVATION
            );

            /** @var User $user */
            $user = User::query()
                ->lockForUpdate()
                ->findOrFail(
                    $accessToken->user_id
                );

            if (! $user->isActive()) {
                abort(
                    403,
                    'This account is currently unavailable.'
                );
            }

            $user->forceFill([
                'password' => $validated['password'],

                'must_change_password' => false,

                'activated_at' => now(),
            ])->save();

            return $user;
        });

        return view('auth.activate-user', [
            'state' => 'success',
            'user' => $user,
            'token' => null,
        ]);
    }
}

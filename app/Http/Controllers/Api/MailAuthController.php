<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MailAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        $user = User::query()
            ->where(
                'email',
                mb_strtolower($credentials['email']),
            )
            ->first();


        if (
            ! $user
            || ! Hash::check(
                $credentials['password'],
                $user->password,
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }


        if (
            ! $user->isMailUser()
            || ! $user->mailboxIsReady()
        ) {
            return response()->json([
                'message' =>
                    'This mail account is not available.',
            ], 403);
        }


        $token = $user
            ->createToken('mail-front')
            ->plainTextToken;


        return response()->json([
            'message' =>
                'Authenticated successfully.',

            'user' => [
                'id' =>
                    $user->id,

                'name' =>
                    $user->name,

                'email' =>
                    $user->email,

                'mailbox_address' =>
                    $user->mailbox_address,

                'mailbox_quota_mb' =>
                    $user->mailbox_quota_mb,

                'avatar_url' =>
                    $user->avatarUrl(),
            ],

            'token' =>
                $token,
        ]);
    }



    public function me(Request $request): JsonResponse
    {
        $user = $request->user();


        return response()->json([
            'user' => [
                'id' =>
                    $user->id,

                'name' =>
                    $user->name,

                'email' =>
                    $user->email,

                'mailbox_address' =>
                    $user->mailbox_address,

                'mailbox_quota_mb' =>
                    $user->mailbox_quota_mb,

                'avatar_url' =>
                    $user->avatarUrl(),
            ],
        ]);
    }



    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ?->currentAccessToken()
            ?->delete();


        if (auth()->guard('web')->check()) {
            auth()->guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }


        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}

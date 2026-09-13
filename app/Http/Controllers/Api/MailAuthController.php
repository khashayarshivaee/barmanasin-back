<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MailAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt([
            'email' => mb_strtolower($credentials['email']),
            'password' => $credentials['password'],
        ])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (
            ! $user
            || ! $user->isMailUser()
            || ! $user->mailboxIsReady()
        ) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'This mail account is not available.',
            ], 403);
        }

        return response()->json([
            'message' => 'Authenticated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mailbox_address' => $user->mailbox_address,
                'mailbox_quota_mb' => $user->mailbox_quota_mb,
                'avatar_url' => $user->avatarUrl(),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mailbox_address' => $user->mailbox_address,
                'mailbox_quota_mb' => $user->mailbox_quota_mb,
                'avatar_url' => $user->avatarUrl(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}

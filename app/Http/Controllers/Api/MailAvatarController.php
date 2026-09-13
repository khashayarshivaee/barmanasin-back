<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MailAvatarController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $user = $this->mailUser($request);

        $request->validate([
            'avatar' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=4096,max_height=4096',
            ],
        ]);

        $path = $request->file('avatar')->store(
            'mail-avatars/'.$user->id,
            'public',
        );

        if (! is_string($path) || $path === '') {
            throw new RuntimeException(
                'Unable to store the profile photo.'
            );
        }

        try {
            $oldPath = $this->replaceAvatar(
                $user,
                $path,
            );
        } catch (Throwable $exception) {
            // Do not leave an unused upload if the database update fails.
            $this->deleteStoredAvatar($user, $path);

            throw $exception;
        }

        $this->deleteStoredAvatar($user, $oldPath);

        return response()->json([
            'message' => 'Profile photo updated successfully.',
            'avatar_url' => $user->avatarUrl(),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $this->mailUser($request);

        $oldPath = $this->replaceAvatar(
            $user,
            null,
        );

        $this->deleteStoredAvatar($user, $oldPath);

        return response()->json([
            'message' => 'Profile photo removed successfully.',
            'avatar_url' => null,
        ]);
    }

    private function mailUser(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        abort_unless(
            $user->isMailUser() && $user->mailboxIsReady(),
            403,
            'This mail account is not available.',
        );

        return $user;
    }

    private function replaceAvatar(
        User $user,
        ?string $newPath,
    ): ?string {
        $oldPath = DB::transaction(function () use ($user, $newPath) {
            // Serialize simultaneous changes to the same account.
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $previousPath = $lockedUser->avatar_path;

            $lockedUser->avatar_path = $newPath;
            $lockedUser->save();

            return $previousPath;
        });

        $user->avatar_path = $newPath;

        return $oldPath;
    }

    private function deleteStoredAvatar(
        User $user,
        ?string $path,
    ): void {
        if (
            blank($path)
            || ! str_starts_with(
                $path,
                'mail-avatars/'.$user->id.'/',
            )
        ) {
            return;
        }

        try {
            if (! Storage::disk('public')->delete($path)) {
                report(new RuntimeException(
                    'Unable to remove an unused profile photo.'
                ));
            }
        } catch (Throwable $exception) {
            // A cleanup failure must not undo a successful profile update.
            report($exception);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MailAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:25600',
            ],
        ]);


        $file = $request->file('file');


        $originalName =
            $file->getClientOriginalName();


        $storedName =
            Str::uuid()->toString()
            . '.'
            . $file->getClientOriginalExtension();


        $disk = 'local';


        $directory =
            'mail-attachments/'
            . auth()->id();



        $path =
            $file->storeAs(
                $directory,
                $storedName,
                $disk,
            );


        $attachment =
            MailAttachment::create([

                'user_id' =>
                    auth()->id(),

                'original_name' =>
                    $originalName,

                'stored_name' =>
                    $storedName,

                'path' =>
                    $path,

                'disk' =>
                    $disk,

                'mime_type' =>
                    $file->getMimeType(),

                'size' =>
                    $file->getSize(),

                'status' =>
                    'ready',

                'checksum' =>
                    hash_file(
                        'sha256',
                        $file->getRealPath(),
                    ),

                'expires_at' =>
                    now()->addDay(),

            ]);


        return response()->json([

            'attachment' => [

                'id' =>
                    $attachment->id,

                'name' =>
                    $attachment->original_name,

                'size' =>
                    $attachment->size,

                'mime_type' =>
                    $attachment->mime_type,

                'status' =>
                    $attachment->status,

            ],

        ]);

    }
}

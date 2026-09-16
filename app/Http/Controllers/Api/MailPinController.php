<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailPinService;
use Illuminate\Http\Request;


class MailPinController extends Controller
{

    public function __construct(
        private readonly MailPinService $pinService,
    ) {
    }



    public function index(
        Request $request
    ) {

        return response()->json([

            'pinned' =>
                $this->pinService
                    ->list(
                        $request->user()
                    ),

        ]);

    }





    public function store(
        Request $request,
        string $uid,
    ) {

        $data =
            $request->validate([

                'mailbox' =>
                    [
                        'required',
                        'string',
                    ],

            ]);



        $pinned =
            $this->pinService
                ->pin(

                    $request->user(),

                    $data['mailbox'],

                    $uid,

                );



        return response()->json([

            'pinned' =>
                $pinned,

        ]);

    }





    public function destroy(
        Request $request,
        string $uid,
    ) {


        $data =
            $request->validate([

                'mailbox' =>
                    [
                        'required',
                        'string',
                    ],

            ]);



        $this->pinService
            ->unpin(

                $request->user(),

                $data['mailbox'],

                $uid,

            );



        return response()->json([

            'message' =>
                'Message unpinned.',

        ]);

    }


}

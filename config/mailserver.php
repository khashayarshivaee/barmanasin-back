<?php

return [

    'provider' => env(
        'MAIL_PROVIDER',
        'stalwart'
    ),


    'stalwart' => [

        'url' => env(
            'STALWART_URL'
        ),

        'token' => env(
            'STALWART_TOKEN'
        ),

        'domain' => env(
            'STALWART_DOMAIN',
            'barmanasin.com'
        ),

    ],

];

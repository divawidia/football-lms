<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'midtrans' => [
        'serverKey' => env('MIDTRANS_SERVER_KEY'),
        'clientKey' => env('MIDTRANS_CLIENT_KEY'),
        'isProduction' => env('MIDTRANS_IS_PRODUCTION'),
        'isSanitized' => env('MIDTRANS_IS_SANITIZED'),
        'is3ds' => env('MIDTRANDS_IS_3DS'),
    ],
    'oh_dear' => [
        'pulse' => [
            'api_key' => env('OH_DEAR_API_TOKEN'),
            'site_id' => env('OH_DEAR_SITE_ID'),
        ],
    ],

];

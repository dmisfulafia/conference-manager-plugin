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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'depbwqh2i'),
        'api_key' => env('CLOUDINARY_API_KEY', '481196651998552'),
        'api_secret' => env('CLOUDINARY_API_SECRET', 'fcpUUvTNOKae3xJFY5sxEKc0OW0'),
    ],

    'credo' => [
        'api_url' => env('CREDO_API_URL', 'https://api.credocentral.com'),
        'public_key' => env('CREDO_PUBLIC_KEY', '1PUB3580LYTFvv7aSCJMEBH3ESdaYKff75eFhr'),
        'secret_key' => env('CREDO_SECRET_KEY', '1PRI3486heaq54W2yGYGBmWj0GMt1x6yndC0wj'),
        'payment_code' => env('CREDO_PAYMENT_CODE', '003486YST1X8'),
    ],

];

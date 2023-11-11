<?php

$scopes = 'read_products,write_products';

return [
    /*
    |--------------------------------------------------------------------------
    | Shopify
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for shopify authentication,
    | api version managing, webhook registration and all other shopify relevant work
    |
    */

    'app_name' => env('SHOPIFY_APP_NAME'),

    'client_id' => env('SHOPIFY_APP_CLIENT_ID'),

    'client_secret' => env('SHOPIFY_APP_SECRET'),

    'redirect' => env('SHOPIFY_APP_REDIRECT'),

    'api_version' => env('SHOPIFY_API_VERSION', '2023-01'),

    'scopes' => explode(',', env('SHOPIFY_APP_SCOPES', $scopes)),

    'max_retries' => env('SHOPIFY_API_MAX_RETRIES', 5),

    'webhook_url' => env('SHOPIFY_WEBHOOK', env('APP_URL', 'http://localhost')),

    'webhooks' => [
        // first index will be shopify webhook topic and second index will be Laravel Job
        // example given bellow
        // ['products/update', \App\Jobs\ProductUpdateJob::class],
    ]
];

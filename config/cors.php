<?php

$frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
$allowedOrigins = array_values(array_filter([
    'http://localhost:5173',
    'http://127.0.0.1:5173',
    'http://localhost:3000',
    $frontendUrl,
]));

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Cross-Origin Resource Sharing (CORS) for your application.
    | Allows frontend apps on Vercel or localhost:5173 to interact with this API.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [
        // Allow Vercel preview deployment URLs (e.g., https://movie-ticket-*-username.vercel.app)
        '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    'supports_credentials' => true,

];


<?php

return [

    'http_bearer' => env('SHART_HTTP_BEARER', 'Bearer'),
    'http_header' => env('SHART_HTTP_HEADER', 'Authorization'),
    'http_qtoken' => env('SHART_HTTP_QTOKEN', 'token'),

    'auth_private' => storage_path(env('SHART_AUTH_PRIVATE', 'app/private.key')),
    'auth_public' => storage_path(env('SHART_AUTH_PUBLIC', 'app/public.key')),
    'auth_phrase' => env('SHART_AUTH_PHRASE', 'docotel123456'),

];

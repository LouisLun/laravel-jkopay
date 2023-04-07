<?php

return [
    'storeID' => env('JKOS_STORE_ID'),
    'apiKey' => env('JKOS_API_KEY'),
    'secretKey' => env('JKOS_SECRET_KEY'),

    // sandbox mode
    'isSandbox' => env('JKOS_IS_SANDBOX', false),
];
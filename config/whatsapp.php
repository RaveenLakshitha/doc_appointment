<?php

return [
    'from_phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'access_token'         => env('WHATSAPP_ACCESS_TOKEN'),
    'api_version'          => env('WHATSAPP_API_VERSION', 'v19.0'), // or latest like v20.0 if available
    'verify_token'         => env('WHATSAPP_VERIFY_TOKEN', 'your_random_secret_string_here'),
];
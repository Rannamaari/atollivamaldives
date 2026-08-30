<?php

return [
    'recaptcha' => [
        'enabled' => (bool) env('RECAPTCHA_ENABLED', env('APP_ENV') === 'production'),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
    ],
    'analytics' => [
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'google_tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID'),
        'search_console_verification' => env('GOOGLE_SEARCH_CONSOLE_VERIFICATION'),
    ],
];

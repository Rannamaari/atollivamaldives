<?php

$publicDiskUrl = env('FILESYSTEM_PUBLIC_URL');

if (! $publicDiskUrl) {
    $appUrl = rtrim((string) env('APP_URL', ''), '/');
    $publicDiskUrl = $appUrl !== '' ? "{$appUrl}/storage" : '/storage';
}

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => $publicDiskUrl,
            'visibility' => 'public',
            'throw' => false,
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];

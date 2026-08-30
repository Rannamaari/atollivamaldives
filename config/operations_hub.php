<?php

return [
    'follow_up' => [
        'supplier_rate_request_business_days' => 3,
        'supplier_first_follow_up_business_days' => 5,
        'agency_intro_business_days' => 4,
    ],
    'expiry_windows' => [
        'rate_days' => 30,
        'agreement_days' => 60,
        'document_days' => [90, 60, 30, 7],
    ],
    'company' => [
        'name' => env('OPERATIONS_ATOLLIVA_NAME', 'Atolliva Maldives'),
        'email' => env('OPERATIONS_ATOLLIVA_EMAIL', env('MICRO_TRAVEL_CONTACT_EMAIL', 'hello@atollivamaldives.com')),
        'website' => env('OPERATIONS_ATOLLIVA_WEBSITE', env('APP_URL', 'https://atollivamaldives.com')),
        'address_lines' => [
            env('OPERATIONS_ATOLLIVA_ADDRESS_LINE_1', 'M. Ithaamuiyge 1'),
            env('OPERATIONS_ATOLLIVA_ADDRESS_LINE_2', 'Alima Magu'),
            env('OPERATIONS_ATOLLIVA_ADDRESS_LINE_3', 'Male'),
            env('OPERATIONS_ATOLLIVA_ADDRESS_LINE_4', 'Republic of Maldives'),
        ],
        'phone' => env('OPERATIONS_ATOLLIVA_PHONE', '+960 9996210'),
        'secondary_phone' => env('OPERATIONS_ATOLLIVA_SECONDARY_PHONE', '+960 7779493'),
        'license_number' => env('OPERATIONS_ATOLLIVA_LICENSE_NUMBER', 'To be confirmed'),
        'sender_designation' => env('OPERATIONS_ATOLLIVA_SENDER_DESIGNATION', 'Partnerships & Contracting'),
    ],
];

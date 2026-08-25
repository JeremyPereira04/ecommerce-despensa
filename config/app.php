<?php

declare(strict_types=1);

return [
    'environment' => getenv('APP_ENV') ?: 'production',
    'timezone' => getenv('APP_TIMEZONE') ?: 'America/Asuncion',
    'stock_low_limit' => max(1, (int) (getenv('STOCK_LOW_LIMIT') ?: 5)),
    'contact' => [
        'phone_display' => '+595 994 265 663',
        'phone_href' => 'tel:+595994265663',
        'whatsapp_url' => 'https://wa.me/595994265663',
        'email' => 'rolonyere@gmail.com',
        'location' => 'MG37+89G, San Lorenzo 111428',
        'maps_url' => 'https://www.google.com/maps/search/?api=1&query=MG37%2B89G%2C%20San%20Lorenzo%20111428',
    ],
    'social' => [
        'facebook' => '',
        'instagram' => '',
        'tiktok' => '',
        'whatsapp' => 'https://wa.me/595994265663',
    ],
];

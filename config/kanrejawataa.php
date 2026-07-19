<?php

return [
    'delivery_fee' => (int) env('KANREJAWATAA_DELIVERY_FEE', 15000),
    'bank_name' => env('KANREJAWATAA_BANK_NAME', 'Bank BCA'),
    'bank_account' => env('KANREJAWATAA_BANK_ACCOUNT', '1234567890'),
    'bank_holder' => env('KANREJAWATAA_BANK_HOLDER', 'Kanrejawataa'),
    'qris_image' => env('KANREJAWATAA_QRIS_IMAGE'),
    'pickup_address' => env('KANREJAWATAA_PICKUP_ADDRESS', 'Makassar, Sulawesi Selatan'),
];

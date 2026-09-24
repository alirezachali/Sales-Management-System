<?php

return [
    'url' => rtrim((string) env('ONLINE_SHOP_URL', ''), '/'),
    'token' => env('ONLINE_SHOP_TOKEN', ''),
    'city' => env('ONLINE_SHOP_CITY', ''),
];

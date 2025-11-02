<?php

return [
    'allowed_hosts' => array_filter(array_map('trim', explode(',', env('TRACK_ALLOWED_HOSTS', '*')))),
    'store_raw_ip' => false,
    'hash_ip' => true,
    'ip_hash_pepper' => env('IP_HASH_PEPPER'),
];

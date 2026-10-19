<?php

return [
    // ponytail: single shared key from env. Upgrade to api_keys table (per-client, revocable) when >1 client or rotation needed.
    'key' => env('API_KEY'),
];

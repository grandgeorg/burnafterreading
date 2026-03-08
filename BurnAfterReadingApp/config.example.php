<?php

return [
    'langs' => [
        'en', 'de'
    ],
    'fallback_lang' => 'en',
    'app_name' => 'Burn After Reading',
    'debug' => false,
    'error_log' => __DIR__ . DIRECTORY_SEPARATOR .
        'logs' . DIRECTORY_SEPARATOR . 'error.log',
    'mail' => [
        'enable' => true,
        'suggest_notify' => true,
        'host' => 'smtp.example.com',
        'secure' => true,
        'port' => 587,
        'username' => 'user@example.com',
        'password' => 'your-password',
        'from' => 'user@example.com',
        'to' => 'user@example.com'
    ]
];
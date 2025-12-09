<?php

return [
    /**
     * Set to false to use Snap API request in production
     * For production environment, change it to false
     */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /**
     * Sandbox Server Key.
     */
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /**
     * Sandbox Client Key
     */
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /**
     * Set true to enable http GET
     * @deprecated
     */
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', false),

    /**
     * Set true to enable request param validation
     * @deprecated
     */
    'is_append_notation' => env('MIDTRANS_IS_APPEND_NOTATION', false),

    /**
     * Proxy configuration
     */
    'proxy' => [
        'host' => env('MIDTRANS_PROXY_HOST'),
        'port' => env('MIDTRANS_PROXY_PORT'),
        'user' => env('MIDTRANS_PROXY_USER'),
        'pass' => env('MIDTRANS_PROXY_PASS'),
    ],

    /**
     * Fallback for configuration
     * @deprecated
     */
    'curl_options' => [],

    /**
     * Connection Timeout
     */
    'connection_timeout' => env('MIDTRANS_CONNECTION_TIMEOUT', 30),

    /**
     * Override Notification URL
     */
    'override_notification_url' => env('MIDTRANS_OVERRIDE_NOTIFICATION_URL'),

    /**
     * Demo mode - use mock tokens for development
     */
    'demo_mode' => env('MIDTRANS_DEMO_MODE', false),
];


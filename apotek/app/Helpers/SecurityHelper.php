<?php

/**
 * Security Helpers for Input Sanitization and Protection
 */

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input to prevent XSS attacks
     *
     * @param mixed $input
     * @return mixed
     */
    function sanitize_input($input)
    {
        if (is_array($input)) {
            return array_map(fn($item) => sanitize_input($item), $input);
        }

        if ($input === null) {
            return null;
        }

        return htmlspecialchars((string) $input, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('strip_tags_safe')) {
    /**
     * Strip tags safely while preserving certain safe HTML
     *
     * @param string $input
     * @param array $allowedTags
     * @return string
     */
    function strip_tags_safe(?string $input, array $allowedTags = []): string
    {
        if ($input === null) {
            return '';
        }

        $tags = implode('>', $allowedTags);
        $tags = !empty($tags) ? '<' . $tags . '>' : '';

        return strip_tags($input, $tags);
    }
}

if (!function_exists('validate_url')) {
    /**
     * Validate and sanitize URL
     *
     * @param string $url
     * @return string|false
     */
    function validate_url(?string $url)
    {
        if ($url === null) {
            return false;
        }

        // Only allow http and https protocols
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'])) {
            return false;
        }

        return $url;
    }
}

if (!function_exists('validate_email')) {
    /**
     * Validate email address
     *
     * @param string $email
     * @return string|false
     */
    function validate_email(?string $email)
    {
        if ($email === null) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

if (!function_exists('truncate_safe')) {
    /**
     * Safely truncate text without breaking HTML entities
     *
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    function truncate_safe(?string $text, int $length = 100, string $suffix = '...'): string
    {
        if ($text === null) {
            return '';
        }

        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('get_ip_address')) {
    /**
     * Get client IP address safely
     *
     * @return string
     */
    function get_ip_address(): string
    {
        // Check for IP from shared internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        // Check for IP passed from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        // Check for remote address
        else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }

        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return 'INVALID';
        }

        return $ip;
    }
}

if (!function_exists('mask_email')) {
    /**
     * Mask email address for privacy
     *
     * @param string $email
     * @return string
     */
    function mask_email(?string $email): string
    {
        if ($email === null) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return 'invalid-email';
        }

        $localPart = $parts[0];
        $domain = $parts[1];

        $visibleChars = ceil(strlen($localPart) / 3);
        $masked = substr($localPart, 0, $visibleChars) . str_repeat('*', strlen($localPart) - $visibleChars);

        return $masked . '@' . $domain;
    }
}

if (!function_exists('mask_phone')) {
    /**
     * Mask phone number for privacy
     *
     * @param string $phone
     * @return string
     */
    function mask_phone(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }

        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) < 4) {
            return str_repeat('*', strlen($phone));
        }

        return substr($phone, 0, 2) . str_repeat('*', strlen($phone) - 4) . substr($phone, -2);
    }
}

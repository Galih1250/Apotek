<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->header('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection in older browsers
        $response->header('X-XSS-Protection', '1; mode=block');

        // Referrer Policy - protect user privacy
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - control browser features
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Strict Transport Security - force HTTPS for 1 year
        if (!app()->environment('local')) {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy - strict but allowing essential scripts
        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://cdn.tailwindcss.com; "
            . "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.tailwindcss.com; "
            . "img-src 'self' data: https:; "
            . "font-src 'self' data: https://fonts.bunny.net; "
            . "frame-src 'self' https://www.google.com; "
            . "connect-src 'self' https://api.github.com https://accounts.google.com https://www.google.com; "
            . "form-action 'self'; "
            . "base-uri 'self'; "
            . "frame-ancestors 'self'";

        $response->header('Content-Security-Policy', $csp);

        return $response;
    }
}

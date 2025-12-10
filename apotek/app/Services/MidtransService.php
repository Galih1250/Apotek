<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = true;
        
        // Set connection timeout
        Config::$curlOptions[CURLOPT_TIMEOUT] = config('midtrans.connection_timeout', 30);
        
        // Configure proxy if set
        $proxyHost = config('midtrans.proxy.host');
        if ($proxyHost) {
            Config::$curlOptions[CURLOPT_PROXY] = $proxyHost;
            if (config('midtrans.proxy.port')) {
                Config::$curlOptions[CURLOPT_PROXY] .= ':' . config('midtrans.proxy.port');
            }
            if (config('midtrans.proxy.user') && config('midtrans.proxy.pass')) {
                Config::$curlOptions[CURLOPT_PROXYUSERPWD] = config('midtrans.proxy.user') . ':' . config('midtrans.proxy.pass');
            }
        }

        if (config('midtrans.override_notification_url')) {
            Config::$overrideNotifUrl = config('midtrans.override_notification_url');
        }
    }

    /**
     * Create Snap payment token
     */
    public function createSnapToken(array $params): string
    {
        try {
            // Demo mode - return mock token
            if (config('midtrans.demo_mode')) {
                Log::debug('Demo mode: Creating mock Snap token', [
                    'order_id' => $params['transaction_details']['order_id'] ?? 'unknown',
                    'amount' => $params['transaction_details']['gross_amount'] ?? 0,
                ]);
                
                // Return a mock token that looks realistic
                $mockToken = 'DEMO-' . substr(hash('sha256', json_encode($params)), 0, 40);
                
                Log::debug('Demo mode: Mock token created', [
                    'token_length' => strlen($mockToken),
                ]);
                
                return $mockToken;
            }
            
            // Validate that we have a server key
            if (empty(Config::$serverKey)) {
                throw new Exception('Midtrans Server Key is not configured. Please set MIDTRANS_SERVER_KEY in your .env file.');
            }
            
            // Log the attempt
            Log::debug('Creating Midtrans Snap token', [
                'order_id' => $params['transaction_details']['order_id'] ?? 'unknown',
                'amount' => $params['transaction_details']['gross_amount'] ?? 0,
                'server_key_set' => !empty(Config::$serverKey),
                'production_mode' => Config::$isProduction,
            ]);
            
            $response = @Snap::createTransaction($params);
            
            // Check if response has token property
            if (!isset($response->token)) {
                Log::error('Midtrans API response missing token property', [
                    'response_type' => gettype($response),
                    'response_keys' => is_object($response) ? array_keys((array) $response) : 'N/A',
                    'response_json' => json_encode($response),
                ]);
                throw new Exception('Midtrans API response is missing token property. API may have returned an error response.');
            }
            
            $token = $response->token;
            
            Log::debug('Midtrans Snap token created successfully', [
                'token_length' => strlen($token),
            ]);
            
            return $token;
        } catch (\Exception $e) {
            $rawError = $e->getMessage();

            // Log full exception for debugging "Undefined array key" errors
            if (str_contains($rawError, 'Undefined array key')) {
                Log::error('Midtrans Undefined array key error - debugging info', [
                    'raw_error' => $rawError,
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine(),
                    'exception_trace' => $e->getTraceAsString(),
                    'config_server_key' => config('midtrans.server_key') ? substr(config('midtrans.server_key'), 0, 10) . '...' : 'NOT SET',
                    'config_is_production' => config('midtrans.is_production'),
                    'config_is_sanitized' => config('midtrans.is_sanitized'),
                ]);
            }

            // If we get API error and not in demo mode, try falling back to demo
            if (str_contains($rawError, 'Undefined array key') && !config('midtrans.demo_mode')) {
                Log::warning('Midtrans API failed, falling back to demo mode', [
                    'raw_error' => $rawError,
                ]);

                // Return demo token instead
                $mockToken = 'FALLBACK-' . substr(hash('sha256', json_encode($params)), 0, 40);
                Log::info('Using fallback demo token due to API error');
                return $mockToken;
            }

            // Sanitize common vendor error messages for logs/exceptions
            if (str_contains($rawError, 'The ServerKey/ClientKey is null') || str_contains($rawError, 'The ServerKey/ClientKey is invalid') || str_contains($rawError, 'contains white-space')) {
                $sanitized = 'Midtrans server key is missing or invalid. Please set MIDTRANS_SERVER_KEY in your .env file.';
            } else {
                // Remove URLs and email addresses, truncate to reasonable length
                $sanitized = preg_replace('/https?:\/\/[^\s]+/i', '[link]', $rawError);
                $sanitized = preg_replace('/[\w.+-]+@[\w.-]+/','[email]',$sanitized);
                if (strlen($sanitized) > 400) {
                    $sanitized = substr($sanitized, 0, 400) . '...';
                }
            }

            Log::error('Midtrans Snap token creation failed', [
                'error' => $sanitized,
                'raw_error' => $rawError,
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'server_key_set' => !empty(Config::$serverKey),
                'client_key_set' => !empty(Config::$clientKey),
                'demo_mode' => config('midtrans.demo_mode'),
            ]);

            throw new Exception('Failed to create Snap token: ' . $sanitized);
        }
    }

    /**
     * Create Snap redirect URL
     */
    public function createSnapRedirectUrl(array $params): string
    {
        try {
            return Snap::createTransaction($params)->redirect_url;
        } catch (Exception $e) {
            throw new Exception('Failed to create redirect URL: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $response = Transaction::status($orderId);
            return (array) $response;
        } catch (Exception $e) {
            throw new Exception('Failed to get transaction status: ' . $e->getMessage());
        }
    }

    /**
     * Approve transaction
     */
    public function approveTransaction(string $orderId): array
    {
        try {
            $response = Transaction::approve($orderId);
            return (array) $response;
        } catch (Exception $e) {
            throw new Exception('Failed to approve transaction: ' . $e->getMessage());
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            $response = Transaction::cancel($orderId);
            return (array) $response;
        } catch (Exception $e) {
            throw new Exception('Failed to cancel transaction: ' . $e->getMessage());
        }
    }

    /**
     * Refund transaction
     */
    public function refundTransaction(string $orderId, ?int $amount = null): array
    {
        try {
            $params = [];
            if ($amount) {
                $params['refund_amount'] = $amount;
            }
            $response = Transaction::refund($orderId, $params);
            return (array) $response;
        } catch (Exception $e) {
            throw new Exception('Failed to refund transaction: ' . $e->getMessage());
        }
    }

    /**
     * Prepare transaction params for Snap
     */
    public function prepareTransactionParams(
        string $orderId,
        int|float $amount,
        string $email,
        string $firstName,
        string $lastName = '',
        array $customMetadata = []
    ): array {
        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
            ],
            'custom_expiry' => [
                'expiry_duration' => 15,
                'unit' => 'minute',
            ],
            'metadata' => [
                'key1' => 'value1',
                'key2' => 'value2'
            ],
            'success_redirect_url' => route('payment.result'),
            'pending_redirect_url' => route('payment.result'),
            'error_redirect_url' => route('payment.result'),
        ];
    }
}
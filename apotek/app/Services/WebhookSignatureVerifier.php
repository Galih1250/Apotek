<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WebhookSignatureVerifier
{
    /**
     * Verify Midtrans webhook signature
     *
     * @param array $data
     * @param string $signature
     * @param string $serverKey
     * @return bool
     */
    public static function verifyMidtransSignature(array $data, string $signature, string $serverKey): bool
    {
        $orderId = $data['order_id'] ?? null;
        $statusCode = $data['status_code'] ?? null;
        $grossAmount = $data['gross_amount'] ?? null;

        if (!$orderId || $statusCode === null || $grossAmount === null) {
            Log::warning('Missing required Midtrans webhook fields', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'gross_amount' => $grossAmount,
            ]);
            return false;
        }

        $computedSignature = hash('sha512',
            $orderId . $statusCode . $grossAmount . $serverKey
        );

        if (!hash_equals($computedSignature, $signature)) {
            Log::warning('Midtrans signature verification failed', [
                'order_id' => $orderId,
                'expected' => substr($computedSignature, 0, 10) . '...',
                'received' => substr($signature, 0, 10) . '...',
            ]);
            return false;
        }

        return true;
    }

    /**
     * Sanitize webhook data
     *
     * @param array $data
     * @return array
     */
    public static function sanitizeWebhookData(array $data): array
    {
        return [
            'order_id' => sanitize_input($data['order_id'] ?? null),
            'status_code' => sanitize_input($data['status_code'] ?? null),
            'gross_amount' => is_numeric($data['gross_amount'] ?? null) ? (float) $data['gross_amount'] : null,
            'signature_key' => sanitize_input($data['signature_key'] ?? null),
            'transaction_status' => sanitize_input($data['transaction_status'] ?? null),
            'payment_type' => sanitize_input($data['payment_type'] ?? null),
        ];
    }
}

/**
 * Helper function to sanitize input
 */
if (!function_exists('sanitize_input')) {
    function sanitize_input(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

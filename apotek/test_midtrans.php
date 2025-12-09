<?php
require 'vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// Load environment
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configure Midtrans
Config::$serverKey = env('MIDTRANS_SERVER_KEY');
Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
Config::$isProduction = env('MIDTRANS_IS_PRODUCTION') === 'true';

echo "=== Midtrans Connection Test ===\n";
echo "Server Key: " . substr(Config::$serverKey, 0, 10) . "...\n";
echo "Client Key: " . substr(Config::$clientKey, 0, 10) . "...\n";
echo "Production: " . (Config::$isProduction ? 'Yes' : 'No') . "\n\n";

try {
    $params = [
        'transaction_details' => [
            'order_id' => 'TEST-' . time(),
            'gross_amount' => 100000,
        ],
        'customer_details' => [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ],
    ];

    echo "Attempting to create Snap token...\n";
    $token = Snap::getSnapToken($params);
    
    echo "✓ SUCCESS! Token created: " . substr($token, 0, 20) . "...\n";
    echo "✓ Midtrans credentials are working!\n";
    
} catch (Exception $e) {
    echo "✗ FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
?>

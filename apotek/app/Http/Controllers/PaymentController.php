<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Show payment form
     */
    public function showPaymentForm()
    {
        return view('payment.form');
    }

    /**
     * Create payment and get Snap token
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $orderId = 'ORDER-' . Str::uuid();
            
            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'amount' => $request->amount,
                'description' => $request->description ?? 'Payment',
                'status' => 'pending',
            ]);

            // Prepare Midtrans params
            $params = $this->midtransService->prepareTransactionParams(
                $orderId,
                $request->amount,
                $user->email,
                $user->name,
            );

            // Create Snap token
            $snapToken = $this->midtransService->createSnapToken($params);

            // Update transaction with token
            $transaction->update(['midtrans_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
 * Handle payment notification from Midtrans
 */
public function handleNotification(Request $request)
{
    $notif = json_decode($request->getContent(), true);
    
    if (!$notif) {
        return response()->json(['status' => 'error'], 400);
    }

    try {
        $orderId = $notif['order_id'] ?? null;
        
        if (!$orderId) {
            return response()->json(['status' => 'error'], 400);
        }

        // Get transaction status from Midtrans
        $statusResponse = $this->midtransService->getTransactionStatus($orderId);

        // Find transaction in database
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // Update transaction status
        $transactionStatus = $statusResponse['transaction_status'] ?? null;

        match ($transactionStatus) {
            'capture', 'settlement' => $transaction->markAsCompleted(),
            'pending' => $transaction->markAsPending(),
            'deny', 'cancel', 'expire' => $transaction->markAsFailed(),
            default => null,
        };

        // Store additional metadata
        $transaction->update([
            'payment_method' => $statusResponse['payment_type'] ?? null,
            'metadata' => $statusResponse,
        ]);

        return response()->json(['status' => 'ok']);
    } catch (\Exception $e) {
        Log::error('Midtrans notification error: ' . $e->getMessage());
        return response()->json(['status' => 'error'], 500);
    }
}

    /**
     * Show payment status/result
     */
    public function paymentResult(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('payment.form')->with('error', 'Order ID not provided');
        }

        $userId = Auth::id();
        Log::debug('Payment result lookup', [
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);

        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$transaction) {
            Log::warning('Transaction not found', [
                'order_id' => $orderId,
                'user_id' => $userId,
            ]);
            return redirect()->route('payment.form')->with('error', 'Transaction not found. Please check your order ID.');
        }

        // Handle demo mode status
        if (config('midtrans.demo_mode') && $request->query('demo_status')) {
            $demoStatus = $request->query('demo_status');
            
            // Map demo status to transaction status
            $statusMap = [
                'success' => 'completed',
                'pending' => 'pending',
                'failed' => 'failed',
            ];
            
            if (isset($statusMap[$demoStatus])) {
                $newStatus = $statusMap[$demoStatus];
                $transaction->update([
                    'status' => $newStatus,
                    'payment_method' => 'demo_' . $demoStatus,
                ]);
                
                Log::info('Demo payment completed', [
                    'order_id' => $orderId,
                    'demo_status' => $demoStatus,
                    'transaction_status' => $newStatus,
                ]);
            }
        } else if (!config('midtrans.demo_mode')) {
            // Real Midtrans - Check status from API
            try {
                $statusResponse = $this->midtransService->getTransactionStatus($orderId);
                
                if ($statusResponse) {
                    // Map Midtrans status to local status
                    $midtransStatus = $statusResponse['transaction_status'] ?? 'unknown';
                    $localStatus = 'pending';
                    
                    if (in_array($midtransStatus, ['capture', 'settlement'])) {
                        $localStatus = 'completed';
                    } elseif (in_array($midtransStatus, ['deny', 'cancel', 'expire'])) {
                        $localStatus = 'failed';
                    } elseif ($midtransStatus === 'pending') {
                        $localStatus = 'pending';
                    }
                    
                    // Update transaction with Midtrans data
                    $updateData = [
                        'status' => $localStatus,
                        'payment_method' => $statusResponse['payment_type'] ?? null,
                    ];
                    
                    // Store metadata including Midtrans response
                    $metadata = $transaction->metadata ?? [];
                    $metadata['midtrans_response'] = $statusResponse;
                    $updateData['metadata'] = $metadata;
                    
                    $transaction->update($updateData);
                    
                    Log::info('Transaction status updated from Midtrans', [
                        'order_id' => $orderId,
                        'midtrans_status' => $midtransStatus,
                        'local_status' => $localStatus,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to check Midtrans status', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
                // Continue anyway - use local status
            }
        }

        return view('payment.result', ['transaction' => $transaction]);
    }

    /**
     * Get payment history
     */
    public function history()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $transactions = $user
            ->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('payment.history', ['transactions' => $transactions]);
    }

    /**
     * Check transaction status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        try {
            $transaction = Transaction::where('order_id', $request->order_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $statusResponse = $this->midtransService->getTransactionStatus($request->order_id);

            return response()->json([
                'success' => true,
                'transaction_status' => $statusResponse['transaction_status'] ?? null,
                'local_status' => $transaction->status,
                'payment_method' => $statusResponse['payment_type'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

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

    // Get values from notification data
    $orderId = $notif['order_id'] ?? null;
    $statusCode = $notif['status_code'] ?? null;
    $grossAmount = $notif['gross_amount'] ?? null;
    $signatureKey = $notif['signature_key'] ?? null;

    // Verify signature
    $signature = hash('sha512',
        $orderId .
        $statusCode .
        $grossAmount .
        config('services.midtrans.server_key')
    );

    abort_unless($signature === $signatureKey, 403);


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

        // Store additional metadata under `midtrans` key
        $metadata = $transaction->metadata ?? [];
        $metadata['midtrans'] = $statusResponse;
        $transaction->update([
            'payment_method' => $statusResponse['payment_type'] ?? null,
            'metadata' => $metadata,
        ]);

        // If Midtrans provides a PDF invoice URL, try fetching and storing it (once)
        $pdfUrl = $statusResponse['pdf_url'] ?? $statusResponse['finish_redirect_url'] ?? null;
        if ($pdfUrl && empty($metadata['invoice_path'])) {
            try {
                $this->fetchAndStoreInvoice($transaction, $pdfUrl);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch invoice PDF', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            }
        }

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

                        // Store metadata under `midtrans` key
                        $metadata = $transaction->metadata ?? [];
                        $metadata['midtrans'] = $statusResponse;
                        $updateData['metadata'] = $metadata;

                        $transaction->update($updateData);

                        // Attempt to fetch and store PDF invoice if available
                        $pdfUrl = $statusResponse['pdf_url'] ?? $statusResponse['finish_redirect_url'] ?? null;
                        if ($pdfUrl && empty($metadata['invoice_path'])) {
                            try {
                                $this->fetchAndStoreInvoice($transaction, $pdfUrl);
                            } catch (\Exception $e) {
                                Log::warning('Failed to fetch invoice PDF (result view)', ['order_id' => $orderId, 'error' => $e->getMessage()]);
                            }
                        }

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

    /**
     * Download invoice PDF (authenticated - owner only)
     */
    public function downloadInvoice($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $metadata = $transaction->metadata ?? [];
        $path = $metadata['invoice_path'] ?? null;
        $pdfUrl = $metadata['midtrans']['pdf_url'] ?? $metadata['midtrans_pdf_url'] ?? null;

        if ($path && Storage::exists($path)) {
            return Storage::download($path, $orderId . '.pdf');
        }

        if ($pdfUrl) {
            try {
                $res = Http::get($pdfUrl);
                if ($res->ok()) {
                    // store for future
                    try {
                        $this->fetchAndStoreInvoice($transaction, $pdfUrl);
                    } catch (\Exception $e) {
                        Log::warning('Failed to store invoice on-demand', ['order_id' => $orderId, 'error' => $e->getMessage()]);
                    }

                    return response($res->body(), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $orderId . '.pdf"',
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch invoice on-demand', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            }
        }

        abort(404, 'Invoice not available');
    }

    /**
     * Preview invoice inline in browser (authenticated - owner only)
     */
    public function previewInvoice($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $metadata = $transaction->metadata ?? [];
        $path = $metadata['invoice_path'] ?? null;
        $pdfUrl = $metadata['midtrans']['pdf_url'] ?? $metadata['midtrans_pdf_url'] ?? null;

        if ($path && Storage::exists($path)) {
            $content = Storage::get($path);
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $orderId . '.pdf"',
            ]);
        }

        if ($pdfUrl) {
            try {
                $res = Http::get($pdfUrl);
                if ($res->ok()) {
                    return response($res->body(), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . $orderId . '.pdf"',
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch invoice preview on-demand', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            }
        }

        abort(404, 'Invoice not available');
    }

    /**
     * Helper to fetch an invoice PDF from a URL and store it on disk, updating transaction metadata
     */
    private function fetchAndStoreInvoice(Transaction $transaction, string $pdfUrl): void
    {
        // Validate basic URL
        if (!filter_var($pdfUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid PDF URL');
        }

        $res = Http::get($pdfUrl);
        if (!$res->ok()) {
            throw new \Exception('Failed to download PDF (HTTP ' . $res->status() . ')');
        }

        $contentType = $res->header('Content-Type', 'application/pdf');
        if (!str_contains($contentType, 'pdf') && !str_contains($contentType, 'application/octet-stream')) {
            // Not a PDF, but still attempt to store if content length > 0
            Log::warning('Downloaded invoice is not a PDF', ['order_id' => $transaction->order_id, 'content_type' => $contentType]);
        }

        $filename = 'invoices/' . $transaction->order_id . '-' . time() . '.pdf';
        $path = 'private/' . $filename; // storage/app/private/invoices/...

        Storage::put($path, $res->body());

        $metadata = $transaction->metadata ?? [];
        $metadata['invoice_path'] = $path;
        $metadata['invoice_stored_at'] = Carbon::now()->toDateTimeString();
        $metadata['midtrans_pdf_url'] = $pdfUrl;

        $transaction->update(['metadata' => $metadata]);
    }

    /**
     * Handle recurring notifications from Midtrans (public endpoint)
     */
    public function handleRecurringNotification(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        Log::info('Midtrans recurring notification received', ['payload' => $payload]);

        $orderId = $payload['order_id'] ?? $payload['transaction_details']['order_id'] ?? null;

        if ($orderId) {
            $transaction = Transaction::where('order_id', $orderId)->first();
            if ($transaction) {
                $metadata = $transaction->metadata ?? [];
                $metadata['midtrans_recurring'] = $payload;
                $transaction->update(['metadata' => $metadata]);
                Log::info('Stored recurring notification into transaction metadata', ['order_id' => $orderId]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Pay Account notifications from Midtrans (public endpoint)
     */
    public function handlePayAccountNotification(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        Log::info('Midtrans pay-account notification received', ['payload' => $payload]);

        $merchantId = $payload['merchant_id'] ?? null;

        // For now, just log and acknowledge. Optionally store in DB or notify admin.
        return response()->json(['status' => 'ok']);
    }

    /**
     * Finish redirect for payment (already handled by paymentResult). Kept for clarity.
     */
    public function finishRedirect(Request $request)
    {
        return $this->paymentResult($request);
    }

    /**
     * Unfinish redirect (customer pressed Back on VT-Web)
     */
    public function unfinishRedirect(Request $request)
    {
        $orderId = $request->query('order_id');
        return view('payment.unfinish', compact('orderId'));
    }

    /**
     * Error redirect (payment error)
     */
    public function paymentError(Request $request)
    {
        $orderId = $request->query('order_id');
        return view('payment.error', compact('orderId'));
    }

    /**
     * Admin page: show all Midtrans endpoints (absolute URLs) for use in Midtrans dashboard
     */
    public function midtransEndpoints()
    {
        $endpoints = [
            'payment_notification' => route('midtrans.webhook'),
            'recurring_notification' => route('midtrans.recurring'),
            'pay_account_notification' => route('midtrans.pay_account'),
            'finish_redirect' => route('payment.result'),
            'unfinish_redirect' => route('payment.unfinish'),
            'error_redirect' => route('payment.error'),
        ];

        return view('admin.midtrans_endpoints', compact('endpoints'));
    }
}

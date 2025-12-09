<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Result') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @if ($transaction)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status Card -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Transaction Status</h2>
                            
                            <div class="mb-4 p-4 rounded-lg 
                                @if($transaction->isCompleted()) bg-green-100 text-green-800
                                @elseif($transaction->isFailed()) bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif
                            ">
                                <span class="font-bold text-lg">
                                    @if($transaction->isCompleted())
                                        ✓ Payment Completed
                                    @elseif($transaction->isFailed())
                                        ✗ Payment Failed
                                    @else
                                        ⏳ Payment Pending
                                    @endif
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Order ID</label>
                                    <p class="text-lg font-mono">{{ $transaction->order_id }}</p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Amount</label>
                                    <p class="text-lg font-semibold">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p class="text-lg capitalize">{{ $transaction->status }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">Payment Method</label>
                                    <p class="text-lg">{{ $transaction->payment_method ?? 'Not determined yet' }}</p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-500">Date</label>
                                    <p class="text-lg">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Details Card -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Transaction Details</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Description</label>
                                    <p class="text-lg">{{ $transaction->description ?? '-' }}</p>
                                </div>

                                @if($transaction->payment_method)
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Payment Method Details</label>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                        </p>
                                    </div>
                                @endif

                                @if($transaction->isCompleted())
                                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                                        <p class="text-sm text-green-800 dark:text-green-200">
                                            ✓ Your payment has been successfully processed. Thank you!
                                        </p>
                                    </div>
                                @elseif($transaction->isFailed())
                                    <div class="mt-6 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg">
                                        <p class="text-sm text-red-800 dark:text-red-200">
                                            ✗ Your payment could not be processed. Please try again or contact support.
                                        </p>
                                    </div>
                                @else
                                    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            ⏳ Your payment is still being processed. Please wait or check back later.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex gap-4 flex-wrap">
                        <a href="{{ route('payment.form') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Make Another Payment
                        </a>
                        <a href="{{ route('payment.history') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                            View History
                        </a>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-medium">
                            Back to Dashboard
                        </a>
                    </div>
                @else
                    <div class="text-center py-12 bg-red-50 dark:bg-red-900 rounded-lg">
                        <h2 class="text-2xl font-bold text-red-800 dark:text-red-200 mb-4">Transaction Not Found</h2>
                        <p class="text-red-600 dark:text-red-300 mb-6">
                            We couldn't find the transaction you're looking for. This could happen if:
                        </p>
                        <ul class="text-red-600 dark:text-red-300 mb-6 text-left max-w-md mx-auto space-y-2">
                            <li>• The transaction ID is incorrect</li>
                            <li>• The transaction belongs to a different account</li>
                            <li>• The transaction has been deleted</li>
                        </ul>
                        <a href="{{ route('payment.form') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Try Again
                        </a>
                        <a href="{{ route('payment.history') }}" class="inline-block ml-2 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                            View History
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh status every 3 seconds if payment is pending
    @if($transaction && $transaction->isPending())
    setTimeout(function() {
        fetch('{{ route("payment.check-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: '{{ $transaction->order_id }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.transaction_status === 'settlement') {
                location.reload();
            }
        });
    }, 3000);
    @endif
</script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment History') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">{{ __('Transactions') }}</h3>
                    <a href="{{ route('payment.form') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        + New Payment
                    </a>
                </div>

                @if ($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Order ID</th>
                                    <th class="px-6 py-3 font-semibold">Amount</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 font-semibold">Payment Method</th>
                                    <th class="px-6 py-3 font-semibold">Date</th>
                                    <th class="px-6 py-3 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 font-mono text-sm">{{ $transaction->order_id }}</td>
                                        <td class="px-6 py-4 font-semibold">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                @if($transaction->isCompleted()) bg-green-100 text-green-800
                                                @elseif($transaction->isFailed()) bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif
                                            ">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->payment_method ? ucfirst(str_replace('_', ' ', $transaction->payment_method)) : '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $transaction->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('payment.result', ['order_id' => $transaction->order_id]) }}" 
                                               class="text-blue-600 hover:text-blue-700 font-medium">
                                                View
                                            </a>

                                            @if((isset($transaction->metadata['midtrans']['pdf_url']) && $transaction->metadata['midtrans']['pdf_url']) || isset($transaction->metadata['invoice_path']))
                                                <a href="{{ route('payment.invoice.preview', ['order_id' => $transaction->order_id]) }}" class="ml-3 text-green-600 hover:text-green-700 font-medium">Preview Invoice</a>
                                                <a href="{{ route('payment.invoice', ['order_id' => $transaction->order_id]) }}" class="ml-3 text-gray-600 hover:text-gray-800 font-medium">Download</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-lg text-gray-500 mb-4">No payment history yet.</p>
                        <a href="{{ route('payment.form') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Make Your First Payment
                        </a>
                    </div>
                @endif

                <!-- Back Link -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
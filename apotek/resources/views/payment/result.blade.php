@if($transaction && isset($transaction->metadata['midtrans']))
    @php
        $midtrans = $transaction->metadata['midtrans'];
        $invoicePath = $transaction->metadata['invoice_path'] ?? null;
        $invoiceStoredAt = $transaction->metadata['invoice_stored_at'] ?? null;
        $pdfUrl = $midtrans['pdf_url'] ?? $midtrans['finish_redirect_url'] ?? null;
    @endphp

    <div class="space-y-6">

        <!-- Status -->
        <div class="p-4 rounded-lg
            @if($transaction->isCompleted()) bg-green-100 text-green-800
            @elseif($transaction->isFailed()) bg-red-100 text-red-800
            @else bg-yellow-100 text-yellow-800
            @endif">
            <strong>Status:</strong> {{ ucfirst($transaction->status) }}
        </div>

        {{-- Prefer stored invoice when available --}}
        @if($invoicePath)
            <div class="mb-4 flex items-center gap-3">
                <a href="{{ route('payment.invoice', ['order_id' => $transaction->order_id]) }}" class="px-5 py-2 bg-green-600 text-white rounded-lg font-medium">Download Invoice (PDF)</a>
                <a href="{{ route('payment.invoice.preview', ['order_id' => $transaction->order_id]) }}" class="px-5 py-2 bg-gray-600 text-white rounded-lg font-medium">Preview Invoice</a>
                @if($invoiceStoredAt)
                    <span class="text-sm text-gray-500 ml-3">Stored: {{ $invoiceStoredAt }}</span>
                @endif
            </div>

            <div class="border rounded-lg overflow-hidden h-[600px]">
                <iframe
                    src="{{ route('payment.invoice.preview', ['order_id' => $transaction->order_id]) }}"
                    class="w-full h-full"
                    frameborder="0">
                </iframe>
            </div>

        {{-- Fallback to Midtrans-provided preview if available --}}
        @elseif($pdfUrl)
            <div class="border rounded-lg overflow-hidden h-[600px]">
                <iframe
                    src="{{ $pdfUrl }}"
                    class="w-full h-full"
                    frameborder="0">
                </iframe>
            </div>

            <div class="flex gap-4 mt-4">
                <a href="{{ route('payment.invoice', ['order_id' => $transaction->order_id]) }}" class="px-5 py-2 bg-green-600 text-white rounded-lg font-medium">Download Invoice (fetch & save)</a>
                <a href="{{ route('payment.history') }}" class="px-5 py-2 bg-gray-600 text-white rounded-lg font-medium">Payment History</a>
            </div>
        @else
            <p class="text-gray-500">Invoice preview not available.</p>
        @endif

    </div>
@else
    <p class="text-red-600">Invoice data not found.</p>
@endif

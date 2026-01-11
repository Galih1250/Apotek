@if($transaction && isset($transaction->metadata['midtrans']))
    @php
        $midtrans = $transaction->metadata['midtrans'];
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

        <!-- Invoice Preview -->
        @if(isset($midtrans['finish_redirect_url']))
            <div class="border rounded-lg overflow-hidden h-[600px]">
                <iframe
                    src="{{ $midtrans['finish_redirect_url'] }}"
                    class="w-full h-full"
                    frameborder="0">
                </iframe>
            </div>
        @else
            <p class="text-gray-500">Invoice preview not available.</p>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            @if(isset($midtrans['pdf_url']))
                <a href="{{ $midtrans['pdf_url'] }}"
                   target="_blank"
                   class="px-5 py-2 bg-green-600 text-white rounded-lg font-medium">
                    Download Invoice (PDF)
                </a>
            @endif

            <a href="{{ route('payment.history') }}"
               class="px-5 py-2 bg-gray-600 text-white rounded-lg font-medium">
                Payment History
            </a>
        </div>

    </div>
@else
    <p class="text-red-600">Invoice data not found.</p>
@endif

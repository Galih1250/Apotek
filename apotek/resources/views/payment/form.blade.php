<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Payment Form -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Enter Payment Amount</h2>
                        <form id="paymentForm" class="space-y-4">
                            @csrf
                            <div>
                                <label for="amount" class="block text-sm font-medium mb-2">Amount (Rp)</label>
                                <input 
                                    type="number" 
                                    id="amount" 
                                    name="amount" 
                                    min="10000" 
                                    step="1000"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                                    placeholder="e.g., 100000"
                                    required
                                >
                                <small class="text-gray-500">Minimum Rp 10,000</small>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium mb-2">Description (Optional)</label>
                                <textarea 
                                    id="description" 
                                    name="description"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                                    placeholder="Payment description..."
                                ></textarea>
                            </div>

                            <button 
                                type="submit" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition"
                                id="payButton"
                            >
                                Proceed to Payment
                            </button>
                        </form>
                    </div>

                    <!-- Payment Information -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg space-y-4">
                            <div>
                                <h3 class="font-semibold mb-2">Accepted Payment Methods:</h3>
                                <ul class="text-sm space-y-1">
                                    <li>✓ Credit/Debit Card</li>
                                    <li>✓ Bank Transfer</li>
                                    <li>✓ E-Wallet (GCash, OVO, DANA)</li>
                                    <li>✓ BNPL (Akulaku, Kredivo)</li>
                                </ul>
                            </div>
                            
                            <hr class="dark:border-gray-600">
                            
                            <div>
                                <h3 class="font-semibold mb-2">Important:</h3>
                                <ul class="text-sm space-y-1">
                                    <li>• Payment is secured by Midtrans</li>
                                    <li>• You will be redirected to payment gateway</li>
                                    <li>• Your transaction will be recorded</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('payment.history') }}" class="text-blue-600 hover:text-blue-700">
                        ← View Payment History
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>

<!-- Snap Script - Only load in production/real mode -->
@if (!config('midtrans.demo_mode'))
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

<script>
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = document.getElementById('amount').value;
    const description = document.getElementById('description').value;
    const btn = document.getElementById('payButton');
    
    // Disable button
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    fetch('{{ route("payment.create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            amount: amount,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            @if (config('midtrans.demo_mode'))
                // Demo mode - show demo payment dialog
                showDemoPaymentDialog(data.snap_token, data.order_id, amount);
            @else
                // Real Midtrans - Check if snap is available
                if (typeof snap !== 'undefined') {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = '{{ route("payment.result") }}?order_id=' + data.order_id;
                        },
                        onPending: function(result) {
                            window.location.href = '{{ route("payment.result") }}?order_id=' + data.order_id;
                        },
                        onError: function(result) {
                            alert('Payment failed. Please try again.');
                            btn.disabled = false;
                            btn.textContent = 'Proceed to Payment';
                        },
                        onClose: function() {
                            alert('Payment cancelled.');
                            btn.disabled = false;
                            btn.textContent = 'Proceed to Payment';
                        }
                    });
                } else {
                    // Snap not available, show error
                    alert('Payment gateway is not available. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Proceed to Payment';
                }
            @endif
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.textContent = 'Proceed to Payment';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Proceed to Payment';
    });
});

@if (config('midtrans.demo_mode'))
// Demo mode payment dialog
function showDemoPaymentDialog(token, orderId, amount) {
    const dialog = document.createElement('div');
    dialog.id = 'demoPaymentDialog';
    dialog.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    
    dialog.innerHTML = `
        <div style="background: white; border-radius: 8px; padding: 2rem; max-width: 400px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <h2 style="margin-top: 0; color: #1f2937; text-align: center;">🎉 Demo Payment</h2>
            <p style="color: #666; text-align: center;">Select payment status to complete:</p>
            
            <div style="background: #f3f4f6; padding: 1rem; border-radius: 6px; margin: 1rem 0; font-size: 0.9rem;">
                <strong>Order ID:</strong> ${orderId}<br>
                <strong>Amount:</strong> Rp ${parseInt(amount).toLocaleString('id-ID')}
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <button onclick="completeDemoPayment('${orderId}', 'success')" style="
                    padding: 0.75rem;
                    background: #10b981;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                ">✓ Success</button>
                
                <button onclick="completeDemoPayment('${orderId}', 'pending')" style="
                    padding: 0.75rem;
                    background: #f59e0b;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                ">⏳ Pending</button>
                
                <button onclick="completeDemoPayment('${orderId}', 'failed')" style="
                    padding: 0.75rem;
                    background: #ef4444;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                ">✗ Failed</button>
                
                <button onclick="closeDemoDialog()" style="
                    padding: 0.75rem;
                    background: #6b7280;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                ">Cancel</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(dialog);
}

function completeDemoPayment(orderId, status) {
    window.location.href = '{{ route("payment.result") }}?order_id=' + orderId + '&demo_status=' + status;
}

function closeDemoDialog() {
    const dialog = document.getElementById('demoPaymentDialog');
    if (dialog) dialog.remove();
    
    const btn = document.getElementById('payButton');
    btn.disabled = false;
    btn.textContent = 'Proceed to Payment';
}
@endif
</script>


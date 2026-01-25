@extends('layouts.app')

@section('content')
  <main class="max-w-3xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow p-6 text-center">
      <h2 class="text-lg font-semibold mb-4">Payment Not Completed</h2>
      <p class="mb-4 text-gray-600">Your payment was not completed. You can try again or contact support.</p>
      @if(isset($orderId))
        <p class="text-sm text-gray-500 mb-4">Order: <code>{{ $orderId }}</code></p>
      @endif
      <a href="{{ route('store.index') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg">Back to Shop</a>
    </div>
  </main>
@endsection
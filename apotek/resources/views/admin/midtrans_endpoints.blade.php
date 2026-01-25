<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Midtrans Endpoints</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sky-50 text-slate-800">
  <header class="bg-red-600">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
          <span class="text-red-600 font-semibold">AP</span>
        </div>
        <h1 class="text-white font-semibold">Midtrans Endpoints</h1>
      </div>
      <a href="{{ route('admin.index') }}" class="text-white">← Back</a>
    </div>
    <div class="h-1 bg-sky-400"></div>
  </header>

  <main class="max-w-4xl mx-auto px-4 py-8">
    <p class="mb-4 text-sm text-gray-600">Use these URLs in your Midtrans dashboard. If you are testing locally with <strong>ngrok</strong>, replace the host (e.g. https://xxxxx.ngrok.io) and keep the path.</p>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
      @foreach($endpoints as $label => $url)
        <div class="flex items-center justify-between gap-3">
          <div class="flex-1">
            <div class="text-sm text-slate-600 font-medium">{{ ucfirst(str_replace('_', ' ', $label)) }}</div>
            <div class="text-xs text-gray-500">{{ $url }}</div>
          </div>

          <div class="flex items-center gap-2">
            <button data-copy="{{ $url }}" class="px-3 py-1 rounded bg-slate-200">Copy</button>
            <a href="{{ $url }}" target="_blank" class="px-3 py-1 rounded bg-blue-600 text-white">Open</a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-6 text-sm text-gray-500">
      <p>Note: Make sure your webhook endpoint is reachable from the internet when adding it to Midtrans. For local testing we recommend using <a href="https://ngrok.com" class="text-blue-600">ngrok</a> or similar tunneling tools.</p>
    </div>
  </main>

  <script>
    document.querySelectorAll('button[data-copy]').forEach(btn => {
      btn.addEventListener('click', () => {
        navigator.clipboard.writeText(btn.dataset.copy).then(() => {
          btn.innerText = 'Copied';
          setTimeout(() => btn.innerText = 'Copy', 1500);
        });
      });
    });
  </script>
</body>
</html>
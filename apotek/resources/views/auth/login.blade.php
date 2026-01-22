<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Wijaya Medika Klinik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="bg-white text-slate-800">

    <header class="bg-red-600">
      <div class="max-w-md mx-auto px-4 py-6 flex flex-col items-center gap-3">
        <img src="{{ asset('images/logo/logo.png') }}" alt="WMK Logo" class="h-20 w-20 rounded-full object-cover">
        <div class="text-white text-2xl font-semibold">Selamat Datang</div>
      </div>
      <div class="h-1 bg-sky-400"></div>
    </header>

    <main class="max-w-md mx-auto px-4 py-8">
      <div class="text-center text-red-700 text-2xl font-semibold">
        Wijaya Medika Klinik
      </div>

      <!-- LOGIN / SIGNUP PAGE (no CSS changed) -->

<div class="mt-6 flex items-center justify-center gap-2">
  <button id="loginTab" type="button" class="px-4 py-2 rounded-full bg-red-600 text-white font-medium transition active:scale-95">
    Login
  </button>
  <button id="signupTab" type="button" class="px-4 py-2 rounded-full bg-slate-200 text-slate-800 font-medium transition active:scale-95">
    Sign Up
  </button>
</div>

<!-- LOGIN FORM -->
<form id="loginForm" action="{{ route('login') }}" method="POST" class="mt-6 space-y-4">
  @csrf

   @if ($errors->any())
  <div class="text-red-600 mb-4">
    <ul class="list-disc list-inside">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

  <div class="relative">
    <input name="email" type="email" placeholder="Email" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
  </div>
  <div class="relative">
    <input name="password" type="password" placeholder="Password" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
  </div>
  <button type="submit" class="w-full rounded-full bg-rose-400 text-white py-3 font-medium transition active:scale-95 hover:shadow-md">
    Login
  </button>
</form>

<!-- SIGNUP FORM -->
<form id="signupForm" action="{{ route('register') }}" method="POST" class="mt-6 space-y-4 hidden">
  @csrf
  <div class="relative">
    <input name="name" type="text" placeholder="Nama Lengkap" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
  </div>
  <div class="relative">
    <input name="email" type="email" placeholder="Email" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
  </div>
  <div class="relative">
    <input name="password" type="password" placeholder="Password" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
  </div>
  <div class="relative">
    <input name="password_confirmation" type="password" placeholder="Konfirmasi Password" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
  </div>
  <div class="flex justify-center">
    <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
  </div>
  <button type="submit" class="w-full rounded-full bg-rose-400 text-white py-3 font-medium transition active:scale-95 hover:shadow-md">
    Sign Up
  </button>
</form>

<a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 mx-auto border border-gray-300 rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
  <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" class="w-5 h-5" />
  Sign in with Google
</a>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
  const loginTab = document.getElementById('loginTab');
  const signupTab = document.getElementById('signupTab');
  const loginForm = document.getElementById('loginForm');
  const signupForm = document.getElementById('signupForm');

  // Tab switching
  loginTab.addEventListener('click', () => {
    loginTab.classList.add('bg-red-600','text-white');
    loginTab.classList.remove('bg-slate-200','text-slate-800');

    signupTab.classList.add('bg-slate-200','text-slate-800');
    signupTab.classList.remove('bg-red-600','text-white');

    loginForm.classList.remove('hidden');
    signupForm.classList.add('hidden');
  });

  signupTab.addEventListener('click', () => {
    signupTab.classList.add('bg-red-600','text-white');
    signupTab.classList.remove('bg-slate-200','text-slate-800');

    loginTab.classList.add('bg-slate-200','text-slate-800');
    loginTab.classList.remove('bg-red-600','text-white');

    signupForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
  });
</script>

<script>
loginForm.addEventListener('submit', function(e) {
  console.log("Login form submitted");
});
</script>

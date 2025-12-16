<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Wijaya Medika Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="bg-white text-slate-800">

    <header class="bg-red-600">
      <div class="max-w-md mx-auto px-4 py-6 flex flex-col items-center gap-3">
        <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center">
          <span class="text-red-600 font-bold">WMK</span>
        </div>
        <div class="text-white text-2xl font-semibold">Selamat Datang</div>
      </div>
      <div class="h-1 bg-sky-400"></div>
    </header>

    <main class="max-w-md mx-auto px-4 py-8">
      <div class="text-center text-red-700 text-2xl font-semibold">
        Wijaya Medika Klinik
      </div>

      <div class="mt-6 flex items-center justify-center gap-2">
        <button id="loginTab" class="px-4 py-2 rounded-full bg-red-600 text-white font-medium transition active:scale-95">
          Login
        </button>

        <button id="signupTab" class="px-4 py-2 rounded-full bg-slate-200 text-slate-800 font-medium transition active:scale-95">
          Sign Up
        </button>
      </div>

      <!-- LOGIN FORM -->
      <form id="loginForm" action="{{ route('login') }}" method="POST" class="mt-6 space-y-4">
        @csrf
        <div class="relative">
          <input name="email" type="email" placeholder="Email"
            class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M1.5 8.67v8.58a2.25 2.25 0 002.25 2.25h16.5a2.25 2.25 0 002.25-2.25V8.67l-9.01 5.34a2.25 2.25 0 01-2.28 0L1.5 8.67z"/>
              <path d="M21.75 6.75v-.75A2.25 2.25 0 0019.5 3.75h-15A2.25 2.25 0 002.25 6v.75l9.01 5.34a2.25 2.25 0 002.28 0l8.21-4.59z"/>
            </svg>
          </span>
        </div>

        <div class="relative">
          <input name="password" type="password" placeholder="Password"
            class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" required />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 1.5a5.25 5.25 0 00-5.25 5.25V9H5.25A2.25 2.25 0 003 11.25v8.25A2.25 2.25 0 005.25 21.75h13.5A2.25 2.25 0 0021 19.5v-8.25A2.25 2.25 0 0018.75 9H17.25V6.75A5.25 5.25 0 0012 1.5z"/>
            </svg>
          </span>
        </div>

        <button type="submit"
          class="w-full rounded-full bg-rose-400 text-white py-3 font-medium transition active:scale-95 hover:shadow-md">
          Login
        </button>
      </form>

      <!-- SIGNUP FORM -->
      <form id="signupForm" action="{{ route('register') }}" method="POST" class="mt-6 space-y-4 hidden">
        @csrf

        <div class="relative">
          <input name="name" type="text" placeholder="Nama Lengkap"
            class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none"
            required />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7.5 8.25a7.5 7.5 0 1115 0 .75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75z"/>
            </svg>
          </span>
        </div>

        <div class="relative">
          <input name="email" type="email" placeholder="Email"
            class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none"
            required />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M1.5 8.67v8.58a2.25 2.25 0 002.25 2.25h16.5a2.25 2.25 0 002.25-2.25V8.67l-9.01 5.34a2.25 2.25 0 01-2.28 0L1.5 8.67z"/>
            </svg>
          </span>
        </div>

        <div class="relative">
          <input name="password" type="password" placeholder="Password"
            class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none"
            required />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor">
              <path d="M12 1.5a5.25 5.25 0 00-5.25 5.25V9H5.25A2.25 2.25 0 003 11.25v8.25A2.25 2.25 0 005.25 21.75h13.5A2.25 2.25 0 0021 19.5v-8.25A2.25 2.25 0 0018.75 9H17.25V6.75A5.25 5.25 0 0012 1.5z"/>
            </svg>
          </span>
        </div>

        <div class="relative">
          <input name="password_confirmation" type="password" placeholder="Konfirmasi Password"
            class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none"
            required />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor">
              <path d="M12 1.5a5.25 5.25 0 00-5.25 5.25V9H5.25A2.25 2.25 0 003 11.25v8.25A2.25 2.25 0 005.25 21.75h13.5A2.25 2.25 0 0021 19.5v-8.25A2.25 2.25 0 0018.75 9H17.25V6.75A5.25 5.25 0 0012 1.5z"/>
            </svg>
          </span>
        </div>

        <button type="submit"
          class="w-full rounded-full bg-rose-400 text-white py-3 font-medium transition active:scale-95 hover:shadow-md">
          Sign Up
        </button>
      </form>
      
      <a href="{{ route('auth.google') }}"
        class="flex items-center justify-center gap-3 mx-auto border border-gray-300 rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        <img
          src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
          alt="Google"
          class="w-5 h-5"
        />
        Sign in with Google
      </a>



    </main>


    <script>
      const loginTab = document.getElementById('loginTab')
      const signupTab = document.getElementById('signupTab')
      const loginForm = document.getElementById('loginForm')
      const signupForm = document.getElementById('signupForm')

      loginTab.addEventListener('click', () => {
        loginTab.classList.add('bg-red-600', 'text-white')
        loginTab.classList.remove('bg-slate-200', 'text-slate-800')

        signupTab.classList.add('bg-slate-200', 'text-slate-800')
        signupTab.classList.remove('bg-red-600', 'text-white')

        loginForm.classList.remove('hidden')
        signupForm.classList.add('hidden')
      })

      signupTab.addEventListener('click', () => {
        signupTab.classList.add('bg-red-600', 'text-white')
        signupTab.classList.remove('bg-slate-200', 'text-slate-800')

        loginTab.classList.add('bg-slate-200', 'text-slate-800')
        loginTab.classList.remove('bg-red-600', 'text-white')

        signupForm.classList.remove('hidden')
        loginForm.classList.add('hidden')
      })
    </script>

  </body>
</html>

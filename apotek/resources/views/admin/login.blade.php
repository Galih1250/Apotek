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
      <div class="text-center text-red-700 text-2xl font-semibold">Wijaya Medika Klinik</div>
      <div class="mt-6 flex items-center justify-center gap-2">
        <button id="loginTab" class="px-4 py-2 rounded-full bg-red-600 text-white font-medium transition-transform active:scale-95">Login</button>
        <button id="signupTab" class="px-4 py-2 rounded-full bg-slate-200 text-slate-800 font-medium transition-transform active:scale-95">Sign Up</button>
      </div>
      <form id="loginForm" action="#" method="post" class="mt-6 space-y-4">
        <div class="relative">
          <input type="text" placeholder="Username" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7.5 8.25a7.5 7.5 0 1 1 15 0 .75.75 0 0 1-.75.75h-13.5a.75.75 0 0 1-.75-.75Z" />
            </svg>
          </span>
        </div>
        <div class="relative">
          <input type="password" placeholder="Password" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25V9H5.25A2.25 2.25 0 0 0 3 11.25v8.25A2.25 2.25 0 0 0 5.25 21.75h13.5A2.25 2.25 0 0 0 21 19.5v-8.25A2.25 2.25 0 0 0 18.75 9H17.25V6.75A5.25 5.25 0 0 0 12 1.5Zm-3.75 7.5V6.75a3.75 3.75 0 0 1 7.5 0V9Z" />
            </svg>
          </span>
        </div>
        <button type="submit" class="w-full rounded-full bg-rose-400 text-white py-3 font-medium transition-transform duration-200 active:scale-95 hover:shadow-md">Login</button>
      </form>
      <form id="signupForm" action="#" method="post" class="mt-6 space-y-4 hidden">
        <div class="relative">
          <input type="text" placeholder="Nama Lengkap" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7.5 8.25a7.5 7.5 0 1 1 15 0 .75.75 0 0 1-.75.75h-13.5a.75.75 0 0 1-.75-.75Z" /></svg>
          </span>
        </div>
        <div class="relative">
          <input type="email" placeholder="Email" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M1.5 8.67v8.58a2.25 2.25 0 0 0 2.25 2.25h16.5a2.25 2.25 0 0 0 2.25-2.25V8.67l-9.01 5.34a2.25 2.25 0 0 1-2.28 0L1.5 8.67Z" /><path d="M21.75 6.75v-.75A2.25 2.25 0 0 0 19.5 3.75h-15A2.25 2.25 0 0 0 2.25 6v.75l9.01 5.34a2.25 2.25 0 0 0 2.28 0l8.21-4.59Z" /></svg>
          </span>
        </div>
        <div class="relative">
          <input type="text" placeholder="Username" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7.5 8.25a7.5 7.5 0 1 1 15 0 .75.75 0 0 1-.75.75h-13.5a.75.75 0 0 1-.75-.75Z" /></svg>
          </span>
        </div>
        <div class="relative">
          <input type="password" placeholder="Password" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25V9H5.25A2.25 2.25 0 0 0 3 11.25v8.25A2.25 2.25 0 0 0 5.25 21.75h13.5A2.25 2.25 0 0 0 21 19.5v-8.25A2.25 2.25 0 0 0 18.75 9H17.25V6.75A5.25 5.25 0 0 0 12 1.5Zm-3.75 7.5V6.75a3.75 3.75 0 0 1 7.5 0V9Z" /></svg>
          </span>
        </div>
        <div class="relative">
          <input type="password" placeholder="Konfirmasi Password" class="w-full rounded-full bg-gray-300/80 px-4 py-3 pr-12 placeholder-slate-600 focus:outline-none" />
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25V9H5.25A2.25 2.25 0 0 0 3 11.25v8.25A2.25 2.25 0 0 0 5.25 21.75h13.5A2.25 2.25 0 0 0 21 19.5v-8.25A2.25 2.25 0 0 0 18.75 9H17.25V6.75A5.25 5.25 0 0 0 12 1.5Zm-3.75 7.5V6.75a3.75 3.75 0 0 1 7.5 0V9Z" /></svg>
          </span>
        </div>
        <button type="submit" class="w-full rounded-full bg-rose-400 text-white py-3 font-medium transition-transform duration-200 active:scale-95 hover:shadow-md">Sign Up</button>
      </form>
    </main>
    <script>
      const form = document.getElementById('loginForm')
      const signupForm = document.getElementById('signupForm')
      const loginTab = document.getElementById('loginTab')
      const signupTab = document.getElementById('signupTab')
      const setActive = (tab) => {
        if (tab === 'login') {
          loginTab?.classList.add('bg-red-600','text-white')
          loginTab?.classList.remove('bg-slate-200','text-slate-800')
          signupTab?.classList.add('bg-slate-200','text-slate-800')
          signupTab?.classList.remove('bg-red-600','text-white')
          form?.classList.remove('hidden')
          signupForm?.classList.add('hidden')
        } else {
          signupTab?.classList.add('bg-red-600','text-white')
          signupTab?.classList.remove('bg-slate-200','text-slate-800')
          loginTab?.classList.add('bg-slate-200','text-slate-800')
          loginTab?.classList.remove('bg-red-600','text-white')
          signupForm?.classList.remove('hidden')
          form?.classList.add('hidden')
        }
      }
      loginTab?.addEventListener('click', (e) => { e.preventDefault(); setActive('login') })
      signupTab?.addEventListener('click', (e) => { e.preventDefault(); setActive('signup') })
      form?.addEventListener('submit', (e) => {
        e.preventDefault()
        const adminUrl = "{{ route('admin') }}"
        window.location.href = adminUrl
      })
      signupForm?.addEventListener('submit', (e) => {
        e.preventDefault()
        const adminUrl = "{{ route('admin') }}"
        window.location.href = adminUrl
      })
    </script>
  </body>
  </html>

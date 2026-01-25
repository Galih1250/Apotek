<x-guest-layout>
    <div class="text-center text-red-700 text-2xl font-semibold">Create your account</div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <input id="name" name="name" type="text" placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-full bg-gray-200 px-4 py-3 placeholder-slate-600 focus:outline-none" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <input id="email" name="email" type="email" placeholder="Email" value="{{ old('email') }}" required
                   class="w-full rounded-full bg-gray-200 px-4 py-3 placeholder-slate-600 focus:outline-none" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <input id="password" name="password" type="password" placeholder="Password" required
                   class="w-full rounded-full bg-gray-200 px-4 py-3 placeholder-slate-600 focus:outline-none" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Konfirmasi Password" required
                   class="w-full rounded-full bg-gray-200 px-4 py-3 placeholder-slate-600 focus:outline-none" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center">
            <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
        </div>

        <button type="submit" class="w-full rounded-full bg-red-600 text-white py-3 font-medium">Sign Up</button>

        <div class="mt-4 text-center text-sm text-gray-600">
            <a href="{{ route('login') }}" class="text-red-600 underline">Already registered? Login</a>
        </div>
    </form>
</x-guest-layout>


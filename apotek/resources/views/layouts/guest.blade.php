<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased bg-sky-50">
        <header class="bg-red-600">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
                        <span class="text-red-600 font-semibold">AP</span>
                    </div>
                    <div class="text-white font-medium">Wijaya Medika</div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="text-white text-sm">Login</a>
                </div>
            </div>
            <div class="h-1 bg-sky-400"></div>
        </header>

        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-6">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-sm rounded-2xl ring-1 ring-slate-200 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

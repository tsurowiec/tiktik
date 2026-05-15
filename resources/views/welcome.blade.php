<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tik-Tik</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-indigo-50 dark:bg-zinc-950 flex flex-col items-center justify-center antialiased">

    <div class="flex flex-col items-center gap-10 px-6 text-center">

        {{-- Logo --}}
        <div class="flex flex-col items-center gap-4">
            <div class="flex items-center justify-center size-20 rounded-2xl bg-indigo-900 shadow-lg">
                <x-app-logo-icon class="size-12 text-white" />
            </div>
            <h1 class="text-5xl font-bold tracking-tight text-zinc-900 dark:text-white">Tik-Tik</h1>
        </div>

        {{-- Tagline --}}
        <p class="max-w-sm text-lg text-zinc-500 dark:text-zinc-400">
            Tasks and countdowns, together.<br/>
            Stay on track with what matters.
        </p>
        <p class="max-w-sm text-lg text-zinc-500 dark:text-zinc-400">

        </p>

        {{-- CTAs --}}
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ route('tasks') }}" class="px-6 py-3 rounded-xl bg-indigo-900 text-white font-semibold hover:bg-zinc-900 transition">
                    Go to app
                </a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-indigo-900 text-white font-semibold hover:bg-zinc-900 transition">
                    Log in
                </a>
            @endauth
        </div>

    </div>

</body>
</html>

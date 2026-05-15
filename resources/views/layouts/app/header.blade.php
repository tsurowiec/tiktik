<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="h-dvh bg-indigo-50 dark:bg-zinc-800">
        <flux:header class="border-b border-blue-100 bg-indigo-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="w-full max-w-2xl mx-auto px-4 md:px-8 flex items-center gap-2">
                <x-app-logo href="{{ route('tasks') }}" wire:navigate />

                <flux:spacer />

                <x-desktop-user-menu />
            </div>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

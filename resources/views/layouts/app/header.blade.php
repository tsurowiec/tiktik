<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-indigo-50 dark:bg-zinc-800">
        <flux:header class="border-b border-blue-100 bg-indigo-200 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="w-full max-w-2xl mx-auto px-4 md:px-8 flex items-center gap-2">
                <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

                <x-app-logo href="{{ route('tasks') }}" wire:navigate />

                <flux:navbar class="-mb-px max-lg:hidden">
                    <flux:navbar.item icon="check-circle" :href="route('tasks')" :current="request()->routeIs('tasks')" wire:navigate>
                        {{ __('Tasks') }}
                    </flux:navbar.item>
                </flux:navbar>

                <flux:spacer />

                <x-desktop-user-menu />
            </div>
        </flux:header>

        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo href="{{ route('tasks') }}" wire:navigate />
                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="check-circle" :href="route('tasks')" :current="request()->routeIs('tasks')" wire:navigate>
                    {{ __('Tasks') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

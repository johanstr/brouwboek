<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'BrouwBoek' }} · {{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">

    {{-- Het menu aan de linkerkant (op een telefoon: uitklapbaar) --}}
    <flux:sidebar sticky collapsible="mobile" class="bg-zinc-50 border-e border-zinc-200 dark:bg-zinc-900 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand href="{{ route('dashboard') }}" name="BrouwBoek" />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="shopping-cart" :href="route('orders.index')" :current="request()->routeIs('orders.*')" wire:navigate>Bestellingen</flux:sidebar.item>
            <flux:sidebar.item icon="document-text" :href="route('invoices.index')" :current="request()->routeIs('invoices.*')" wire:navigate>Facturen</flux:sidebar.item>
            <flux:sidebar.item icon="beaker" :href="route('products.index')" :current="request()->routeIs('products.*')" wire:navigate>Producten</flux:sidebar.item>
            <flux:sidebar.item icon="building-storefront" :href="route('customers.index')" :current="request()->routeIs('customers.*')" wire:navigate>Klanten</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        {{-- Wie is er ingelogd? --}}
        <flux:dropdown position="top" align="start">
            <flux:sidebar.profile :name="auth()->user()->name" />

            <flux:menu>
                <flux:menu.item icon="user" :href="route('profile')" wire:navigate>Profiel</flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle">Uitloggen</flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    {{-- Bovenbalk, alleen zichtbaar op kleine schermen --}}
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <flux:heading>BrouwBoek</flux:heading>
    </flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts
</body>
</html>

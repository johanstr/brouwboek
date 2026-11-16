<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Welkom' }} · {{ config('app.name') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-amber-50 dark:bg-zinc-900">
    <div class="flex min-h-screen flex-col items-center justify-center p-6">
        <div class="mb-6 text-center">
            <div class="text-4xl">🍺</div>
            <flux:heading size="xl">BrouwBoek</flux:heading>
            <flux:text>Brouwerij De Gulle Gans</flux:text>
        </div>

        <div class="w-full max-w-sm rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
            {{ $slot }}
        </div>
    </div>

    @fluxScripts
</body>
</html>

<x-layouts::guest title="Account maken">
    <flux:heading size="lg" class="mb-4">Account maken</flux:heading>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <flux:input name="name" label="Naam" :value="old('name')" required autofocus />

        <flux:input name="email" type="email" label="E-mailadres" :value="old('email')" required />

        <flux:input name="password" type="password" label="Wachtwoord" required viewable />

        <flux:input name="password_confirmation" type="password" label="Herhaal wachtwoord" required viewable />

        <flux:button type="submit" variant="primary" class="w-full">Account maken</flux:button>
    </form>

    <div class="mt-4 text-sm">
        Heb je al een account? <flux:link :href="route('login')">Log in</flux:link>
    </div>
</x-layouts::guest>

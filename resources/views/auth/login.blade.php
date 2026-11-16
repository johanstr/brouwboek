<x-layouts::guest title="Inloggen">
    <flux:heading size="lg" class="mb-4">Inloggen</flux:heading>

    {{-- Melding na het aanvragen van een nieuw wachtwoord --}}
    @session('status')
        <flux:callout variant="success" icon="check-circle" class="mb-4" :heading="$value" />
    @endsession

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <flux:input name="email" type="email" label="E-mailadres" :value="old('email')" required autofocus />

        <flux:input name="password" type="password" label="Wachtwoord" required viewable />

        <flux:checkbox name="remember" label="Onthoud mij" />

        <flux:button type="submit" variant="primary" class="w-full">Inloggen</flux:button>
    </form>

    <div class="mt-4 flex justify-between text-sm">
        <flux:link :href="route('password.request')">Wachtwoord vergeten?</flux:link>
        <flux:link :href="route('register')">Account maken</flux:link>
    </div>
</x-layouts::guest>

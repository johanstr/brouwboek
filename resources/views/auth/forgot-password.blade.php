<x-layouts::guest title="Wachtwoord vergeten">
    <flux:heading size="lg">Wachtwoord vergeten?</flux:heading>
    <flux:text class="mb-4">Vul je e-mailadres in. Je krijgt een mail met een link om een nieuw wachtwoord te kiezen.</flux:text>

    @session('status')
        <flux:callout variant="success" icon="check-circle" class="mb-4" :heading="$value" />
    @endsession

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <flux:input name="email" type="email" label="E-mailadres" :value="old('email')" required autofocus />

        <flux:button type="submit" variant="primary" class="w-full">Stuur mij een link</flux:button>
    </form>

    <div class="mt-4 text-sm">
        <flux:link :href="route('login')">Terug naar inloggen</flux:link>
    </div>
</x-layouts::guest>

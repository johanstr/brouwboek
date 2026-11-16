<x-layouts::guest title="Nieuw wachtwoord">
    <flux:heading size="lg" class="mb-4">Kies een nieuw wachtwoord</flux:heading>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        {{-- Het geheime token uit de link in de mail --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <flux:input name="email" type="email" label="E-mailadres" :value="old('email', $request->email)" required />

        <flux:input name="password" type="password" label="Nieuw wachtwoord" required viewable />

        <flux:input name="password_confirmation" type="password" label="Herhaal wachtwoord" required viewable />

        <flux:button type="submit" variant="primary" class="w-full">Wachtwoord opslaan</flux:button>
    </form>
</x-layouts::guest>

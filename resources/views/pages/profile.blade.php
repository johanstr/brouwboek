<?php

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profiel')] class extends Component
{
    public string $name = '';
    public string $email = '';

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    // We gebruiken dezelfde 'action' die Fortify ook gebruikt
    public function updateProfile(UpdateUserProfileInformation $updater): void
    {
        $updater->update(auth()->user(), [
            'name' => $this->name,
            'email' => $this->email,
        ]);

        Flux::toast('Je gegevens zijn opgeslagen.', variant: 'success');
    }

    public function updatePassword(UpdateUserPassword $updater): void
    {
        $updater->update(auth()->user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast('Je wachtwoord is gewijzigd.', variant: 'success');
    }
};
?>

<div class="max-w-lg space-y-10">
    <form wire:submit="updateProfile" class="space-y-4">
        <flux:heading size="lg">Mijn gegevens</flux:heading>
        <flux:input wire:model="name" label="Naam" />
        <flux:input wire:model="email" label="E-mailadres" type="email" />
        <flux:button type="submit" variant="primary">Opslaan</flux:button>
    </form>

    <flux:separator />

    <form wire:submit="updatePassword" class="space-y-4">
        <flux:heading size="lg">Wachtwoord wijzigen</flux:heading>
        <flux:input wire:model="current_password" label="Huidig wachtwoord" type="password" viewable />
        <flux:input wire:model="password" label="Nieuw wachtwoord" type="password" viewable />
        <flux:input wire:model="password_confirmation" label="Herhaal nieuw wachtwoord" type="password" viewable />
        <flux:button type="submit" variant="primary">Wachtwoord wijzigen</flux:button>
    </form>
</div>

<?php

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Klanten')] class extends Component
{
    public string $search = '';

    public CustomerForm $form;

    #[Computed]
    public function customers()
    {
        return Customer::query()
            ->withCount('orders')
            ->when($this->search, fn ($query) => $query
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('city', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    public function create(): void
    {
        $this->form->reset();
        $this->resetValidation();
        Flux::modal('customer-form')->show();
    }

    public function edit(Customer $customer): void
    {
        $this->resetValidation();
        $this->form->setCustomer($customer);
        Flux::modal('customer-form')->show();
    }

    public function save(): void
    {
        $this->form->save();

        Flux::modal('customer-form')->close();
        Flux::toast('De klant is opgeslagen.', variant: 'success');
    }
};
?>

<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Klanten</flux:heading>
            <flux:text>Slijterijen en drankwinkels die jouw bier verkopen.</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">Nieuwe klant</flux:button>
    </div>

    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Zoek op naam of plaats..." class="mb-4 max-w-sm" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Naam</flux:table.column>
            <flux:table.column class="max-md:hidden">Contactpersoon</flux:table.column>
            <flux:table.column>Plaats</flux:table.column>
            <flux:table.column align="end" class="max-md:hidden">Bestellingen</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->customers as $customer)
                <flux:table.row :key="$customer->id">
                    <flux:table.cell variant="strong">{{ $customer->name }}</flux:table.cell>
                    <flux:table.cell class="max-md:hidden">{{ $customer->contact_name }}</flux:table.cell>
                    <flux:table.cell>{{ $customer->city }}</flux:table.cell>
                    <flux:table.cell align="end" class="max-md:hidden">{{ $customer->orders_count }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $customer->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="customer-form" class="w-full max-w-lg">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $form->customer ? 'Klant bewerken' : 'Nieuwe klant' }}</flux:heading>

            <flux:input wire:model="form.name" label="Naam winkel" placeholder="Slijterij De Kurk" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.contact_name" label="Contactpersoon" />
                <flux:input wire:model="form.phone" label="Telefoon" type="tel" />
            </div>

            <flux:input wire:model="form.email" label="E-mailadres (voor facturen)" type="email" />
            <flux:input wire:model="form.address" label="Adres" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="form.postcode" label="Postcode" />
                <div class="col-span-2">
                    <flux:input wire:model="form.city" label="Plaats" />
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Annuleren</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Opslaan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

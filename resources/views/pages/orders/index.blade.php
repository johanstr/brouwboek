<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Bestellingen')] class extends Component
{
    use WithPagination;

    // Het filter staat ook in de adresbalk: /bestellingen?status=open
    #[Url]
    public string $status = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return Order::with('customer', 'lines')
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->latest('order_date')
            ->latest('id')
            ->paginate(15);
    }
};
?>

<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Bestellingen</flux:heading>
            <flux:text>Alle bestellingen die je bij je klanten hebt opgenomen.</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('orders.create')" wire:navigate>Nieuwe bestelling</flux:button>
    </div>

    <flux:select wire:model.live="status" class="mb-4 max-w-xs">
        <flux:select.option value="">Alle bestellingen</flux:select.option>
        @foreach (OrderStatus::cases() as $case)
            <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:table :paginate="$this->orders">
        <flux:table.columns>
            <flux:table.column>Nr.</flux:table.column>
            <flux:table.column>Datum</flux:table.column>
            <flux:table.column>Klant</flux:table.column>
            <flux:table.column align="end" class="max-md:hidden">Flessen</flux:table.column>
            <flux:table.column align="end" class="max-md:hidden">Bedrag</flux:table.column>
            <flux:table.column align="end">Status</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->orders as $order)
                <flux:table.row :key="$order->id">
                    <flux:table.cell>#{{ $order->id }}</flux:table.cell>
                    <flux:table.cell>{{ $order->order_date->format('d-m-Y') }}</flux:table.cell>
                    <flux:table.cell variant="strong">
                        <flux:link :href="route('orders.show', $order)" wire:navigate>{{ $order->customer->name }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell align="end" class="max-md:hidden">{{ $order->bottleCount() }}</flux:table.cell>
                    <flux:table.cell align="end" class="max-md:hidden">€ {{ number_format($order->subtotal(), 2, ',', '.') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:badge size="sm" :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">Er zijn (nog) geen bestellingen.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>

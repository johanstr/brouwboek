<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    #[Computed]
    public function openOrders(): int
    {
        return Order::where('status', OrderStatus::Open)->count();
    }

    #[Computed]
    public function bottlesInStock(): int
    {
        return Product::sum('stock');
    }

    #[Computed]
    public function lowStock()
    {
        return app(StockService::class)->lowStock();
    }

    #[Computed]
    public function latestOrders()
    {
        return Order::with('customer', 'lines')->latest('order_date')->latest('id')->take(5)->get();
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="xl">Goedendag, {{ auth()->user()->name }}!</flux:heading>
        <flux:text>Zo staat Brouwerij De Gulle Gans er vandaag voor.</flux:text>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <flux:card>
            <flux:text>Open bestellingen</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $this->openOrders }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Flessen op voorraad</flux:text>
            <flux:heading size="xl" class="mt-1">{{ number_format($this->bottlesInStock, 0, ',', '.') }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Bijna op</flux:text>
            <flux:heading size="xl" class="mt-1 {{ $this->lowStock->isNotEmpty() ? 'text-red-600' : '' }}">{{ $this->lowStock->count() }}</flux:heading>
        </flux:card>
    </div>

    @if ($this->lowStock->isNotEmpty())
        <flux:callout variant="warning" icon="exclamation-triangle" heading="Tijd om te brouwen!">
            <flux:callout.text>
                De voorraad van deze bieren is lager dan het minimum:
                {{ $this->lowStock->map(fn ($p) => "{$p->name} ({$p->stock})")->implode(', ') }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    <div>
        <div class="mb-2 flex items-center justify-between">
            <flux:heading size="lg">Laatste bestellingen</flux:heading>
            <flux:button size="sm" icon="plus" :href="route('orders.create')" wire:navigate>Nieuwe bestelling</flux:button>
        </div>

        <flux:table>
            <flux:table.rows>
                @foreach ($this->latestOrders as $order)
                    <flux:table.row :key="$order->id">
                        <flux:table.cell>{{ $order->order_date->format('d-m-Y') }}</flux:table.cell>
                        <flux:table.cell variant="strong">
                            <flux:link :href="route('orders.show', $order)" wire:navigate>{{ $order->customer->name }}</flux:link>
                        </flux:table.cell>
                        <flux:table.cell class="max-sm:hidden">{{ $order->bottleCount() }} flessen</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:badge size="sm" :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>

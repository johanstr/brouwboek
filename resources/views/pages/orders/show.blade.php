<?php

use App\Enums\OrderStatus;
use App\Jobs\SendInvoice;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderService;
use Flux\Flux;
use Livewire\Component;

new class extends Component
{
    // Route model binding: /bestellingen/3 geeft bestelling 3
    public Order $order;

    public function render()
    {
        return $this->view()->title('Bestelling #' . $this->order->id);
    }

    public function markAsPacked(OrderService $orders): void
    {
        $orders->markAsPacked($this->order);

        Flux::toast('De bestelling is ingepakt.', variant: 'success');
    }

    public function createInvoice(InvoiceService $invoices): void
    {
        $invoice = $invoices->createForOrder($this->order);

        Flux::toast("Factuur {$invoice->number} is gemaakt.", variant: 'success');
    }

    public function sendInvoice(): void
    {
        SendInvoice::dispatch($this->order->invoice);

        Flux::toast('De factuur wordt gemaild naar ' . $this->order->customer->email . '.', variant: 'success');
    }

    public function delete(OrderService $orders)
    {
        try {
            $orders->cancel($this->order);
        } catch (\InvalidArgumentException $e) {
            Flux::toast($e->getMessage(), variant: 'danger');

            return;
        }

        Flux::toast('De bestelling is verwijderd.');

        return $this->redirectRoute('orders.index', navigate: true);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">Bestelling #{{ $order->id }}</flux:heading>
            <flux:text>{{ $order->order_date->format('d-m-Y') }}</flux:text>
        </div>
        <flux:badge :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
    </div>

    <flux:card>
        <flux:heading>{{ $order->customer->name }}</flux:heading>
        <flux:text>
            {{ $order->customer->contact_name }}<br>
            {{ $order->customer->address }}, {{ $order->customer->postcode }} {{ $order->customer->city }}<br>
            {{ $order->customer->email }}
        </flux:text>
        @if ($order->notes)
            <flux:callout icon="chat-bubble-left" class="mt-4" :heading="$order->notes" />
        @endif
    </flux:card>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Bier</flux:table.column>
            <flux:table.column align="end">Aantal</flux:table.column>
            <flux:table.column align="end">Prijs</flux:table.column>
            <flux:table.column align="end">Totaal</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($order->lines as $line)
                <flux:table.row :key="$line->id">
                    <flux:table.cell>{{ $line->product->name }}</flux:table.cell>
                    <flux:table.cell align="end">{{ $line->quantity }}</flux:table.cell>
                    <flux:table.cell align="end">€ {{ number_format($line->price, 2, ',', '.') }}</flux:table.cell>
                    <flux:table.cell align="end">€ {{ number_format($line->total(), 2, ',', '.') }}</flux:table.cell>
                </flux:table.row>
            @endforeach
            <flux:table.row>
                <flux:table.cell variant="strong">Totaal excl. btw</flux:table.cell>
                <flux:table.cell align="end" variant="strong">{{ $order->bottleCount() }}</flux:table.cell>
                <flux:table.cell></flux:table.cell>
                <flux:table.cell align="end" variant="strong">€ {{ number_format($order->subtotal(), 2, ',', '.') }}</flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>

    {{-- De knoppen hangen af van de status van de bestelling --}}
    <div class="flex flex-wrap gap-2">
        <flux:button icon="printer" :href="route('orders.packing-list', $order)" target="_blank">Paklijst (PDF)</flux:button>

        @if ($order->status === OrderStatus::Open)
            <flux:button icon="archive-box" wire:click="markAsPacked">Markeer als ingepakt</flux:button>
        @endif

        @if ($order->status === OrderStatus::Packed)
            <flux:button icon="document-text" variant="primary" wire:click="createInvoice">Factuur maken</flux:button>
        @endif

        @if ($order->invoice)
            <flux:button icon="arrow-down-tray" :href="route('invoices.pdf', $order->invoice)">Factuur {{ $order->invoice->number }}</flux:button>
            <flux:button icon="envelope" variant="primary" wire:click="sendInvoice">Mail factuur</flux:button>
        @endif

        @if ($order->status === OrderStatus::Open)
            <flux:button icon="trash" variant="danger" wire:click="delete"
                wire:confirm="Weet je zeker dat je deze bestelling wilt verwijderen?">Verwijderen</flux:button>
        @endif
    </div>

    @if ($order->invoice?->sent_at)
        <flux:text>
            <flux:icon.check-circle variant="micro" class="inline text-green-600" />
            Factuur gemaild op {{ $order->invoice->sent_at->format('d-m-Y \o\m H:i') }}.
        </flux:text>
    @endif
</div>

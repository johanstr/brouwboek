<?php

use App\Jobs\SendInvoice;
use App\Models\Invoice;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Facturen')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function invoices()
    {
        return Invoice::with('order.customer')->orderByDesc('number')->paginate(15);
    }

    // De mail gaat op de wachtrij (queue); de brouwer hoeft niet te wachten
    public function send(Invoice $invoice): void
    {
        SendInvoice::dispatch($invoice);

        Flux::toast("Factuur {$invoice->number} wordt verstuurd naar {$invoice->order->customer->email}.", variant: 'success');
    }
};
?>

<div>
    <div class="mb-6">
        <flux:heading size="xl">Facturen</flux:heading>
        <flux:text>Download facturen voor je administratie of mail ze naar je klanten.</flux:text>
    </div>

    <flux:table :paginate="$this->invoices">
        <flux:table.columns>
            <flux:table.column>Nummer</flux:table.column>
            <flux:table.column class="max-md:hidden">Datum</flux:table.column>
            <flux:table.column>Klant</flux:table.column>
            <flux:table.column align="end">Totaal</flux:table.column>
            <flux:table.column class="max-md:hidden">Gemaild</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->invoices as $invoice)
                <flux:table.row :key="$invoice->id">
                    <flux:table.cell class="font-mono">{{ $invoice->number }}</flux:table.cell>
                    <flux:table.cell class="max-md:hidden">{{ $invoice->invoice_date->format('d-m-Y') }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $invoice->order->customer->name }}</flux:table.cell>
                    <flux:table.cell align="end">€ {{ number_format($invoice->total, 2, ',', '.') }}</flux:table.cell>
                    <flux:table.cell class="max-md:hidden">
                        @if ($invoice->sent_at)
                            <flux:badge size="sm" color="green" icon="check">{{ $invoice->sent_at->format('d-m H:i') }}</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc">Nog niet</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm" variant="ghost" icon="arrow-down-tray" :href="route('invoices.pdf', $invoice)" tooltip="Download PDF" />
                        <flux:button size="sm" variant="ghost" icon="envelope" wire:click="send({{ $invoice->id }})" tooltip="Mail naar klant" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">Er zijn nog geen facturen.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>

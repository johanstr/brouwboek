<?php

use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Nieuwe bestelling')] class extends Component
{
    public string $customer_id = '';
    public string $notes = '';

    // Per product het aantal flessen: [product_id => aantal]
    public array $quantities = [];

    #[Computed]
    public function customers()
    {
        return Customer::orderBy('name')->get();
    }

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }

    // Het totaalbedrag (excl. btw) van wat er nu is ingevuld
    #[Computed]
    public function total(): float
    {
        return $this->products->sum(
            fn (Product $product) => $product->price * (int) ($this->quantities[$product->id] ?? 0)
        );
    }

    // Knopjes - en +: steeds een half krat (12 flessen) erbij of eraf
    public function change(int $productId, int $amount): void
    {
        $new = (int) ($this->quantities[$productId] ?? 0) + $amount;

        $this->quantities[$productId] = max(0, $new);
    }

    // Laravel geeft de OrderService automatisch mee (dependency injection)
    public function save(OrderService $orders)
    {
        $this->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'notes' => ['nullable', 'max:500'],
        ], [], ['customer_id' => 'klant', 'notes' => 'opmerking']);

        try {
            $order = $orders->create(
                Customer::findOrFail($this->customer_id),
                $this->quantities,
                $this->notes,
            );
        } catch (\InvalidArgumentException $e) {
            // Bijv. "Kies minstens één bier." of "Er zijn maar 12 flessen ..."
            $this->addError('quantities', $e->getMessage());

            return;
        }

        Flux::toast('De bestelling is opgeslagen.', variant: 'success');

        return $this->redirectRoute('orders.show', $order, navigate: true);
    }
};
?>

<div class="mx-auto max-w-2xl">
    <flux:heading size="xl">Nieuwe bestelling</flux:heading>
    <flux:text class="mb-6">Vul in wat de klant bestelt. Werkt ook prima op je telefoon!</flux:text>

    <form wire:submit="save" class="space-y-6">
        <flux:select wire:model="customer_id" label="Klant" placeholder="Kies een klant...">
            @foreach ($this->customers as $customer)
                <flux:select.option :value="$customer->id">{{ $customer->name }} ({{ $customer->city }})</flux:select.option>
            @endforeach
        </flux:select>

        <div>
            <flux:label>Bieren</flux:label>

            <div class="mt-2 divide-y divide-zinc-200 rounded-lg border border-zinc-200 dark:divide-zinc-700 dark:border-zinc-700">
                @foreach ($this->products as $product)
                    <div class="flex items-center gap-3 p-3" wire:key="product-{{ $product->id }}">
                        <div class="min-w-0 flex-1">
                            <div class="truncate font-medium">{{ $product->name }}</div>
                            <div class="text-sm text-zinc-500">
                                € {{ number_format($product->price, 2, ',', '.') }} · nog {{ $product->stock }} op voorraad
                            </div>
                        </div>

                        <flux:button size="sm" icon="minus" wire:click="change({{ $product->id }}, -12)" />
                        <input type="number" min="0" wire:model.live.debounce.400ms="quantities.{{ $product->id }}"
                            class="w-16 rounded-md border border-zinc-300 px-2 py-1 text-center dark:border-zinc-600 dark:bg-zinc-800"
                            placeholder="0">
                        <flux:button size="sm" icon="plus" wire:click="change({{ $product->id }}, 12)" />
                    </div>
                @endforeach
            </div>

            <flux:error name="quantities" />
        </div>

        <flux:textarea wire:model="notes" label="Opmerking (optioneel)" rows="2" placeholder="Bijv. graag leveren na 14:00" />

        <div class="flex items-center justify-between rounded-lg bg-amber-50 p-4 dark:bg-zinc-900">
            <div>
                <flux:text>Totaal excl. btw</flux:text>
                <flux:heading size="lg">€ {{ number_format($this->total, 2, ',', '.') }}</flux:heading>
            </div>
            <flux:button type="submit" variant="primary" icon="check">Opslaan</flux:button>
        </div>
    </form>
</div>

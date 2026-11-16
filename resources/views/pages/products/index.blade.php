<?php

use App\Exports\StockExport;
use App\Imports\ProductsImport;
use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Flux\Flux;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Producten')] class extends Component
{
    use WithFileUploads;
    use WithPagination;

    // De zoekterm, gekoppeld aan het zoekveld
    public string $search = '';

    // Het formulier voor nieuw/bewerken
    public ProductForm $form;

    // Voor het aanvullen van de voorraad
    public ?int $restockId = null;
    public string $restockAmount = '';

    // Het Excel-bestand voor de import
    public $file;

    // Fouten uit het Excel-bestand: ['Rij 3: prijs is verplicht.', ...]
    public array $importErrors = [];

    // Na elke wijziging van de zoekterm: terug naar pagina 1
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->search, fn ($query) => $query
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }

    public function create(): void
    {
        $this->form->reset();
        $this->resetValidation();
        Flux::modal('product-form')->show();
    }

    public function edit(Product $product): void
    {
        $this->resetValidation();
        $this->form->setProduct($product);
        Flux::modal('product-form')->show();
    }

    public function save(): void
    {
        $this->form->save();

        Flux::modal('product-form')->close();
        Flux::toast('Het product is opgeslagen.', variant: 'success');
    }

    public function restock(Product $product): void
    {
        $this->restockId = $product->id;
        $this->restockAmount = '';
        $this->resetValidation();
        Flux::modal('restock')->show();
    }

    public function saveRestock(): void
    {
        $this->validate(['restockAmount' => ['required', 'integer', 'min:1']], [], ['restockAmount' => 'aantal']);

        $product = Product::findOrFail($this->restockId);
        $product->increment('stock', (int) $this->restockAmount);

        Flux::modal('restock')->close();
        Flux::toast("{$this->restockAmount} flessen {$product->name} bijgeboekt.", variant: 'success');
    }

    public function import(): void
    {
        $this->validate(['file' => ['required', 'file', 'mimes:xlsx,csv', 'max:2048']]);

        $this->importErrors = [];

        try {
            Excel::import(new ProductsImport, $this->file);
        } catch (ValidationException $e) {
            foreach ($e->failures() as $failure) {
                $this->importErrors[] = "Rij {$failure->row()}: " . implode(' ', $failure->errors());
            }

            return;
        }

        $this->reset('file');
        Flux::modal('import')->close();
        Flux::toast('De producten zijn geïmporteerd.', variant: 'success');
    }

    // Download de voorraadlijst als Excel-bestand
    public function export()
    {
        return Excel::download(new StockExport, 'voorraad-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function delete(Product $product): void
    {
        if ($product->orderLines()->exists()) {
            Flux::toast('Dit product staat in een bestelling en kan niet worden verwijderd.', variant: 'danger');

            return;
        }

        $product->delete();
        Flux::toast('Het product is verwijderd.');
    }
};
?>

<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Producten</flux:heading>
            <flux:text>Alle bieren van De Gulle Gans en hun voorraad.</flux:text>
        </div>

        <div class="flex gap-2">
            <flux:button icon="arrow-up-tray" x-on:click="$flux.modal('import').show()">Importeren</flux:button>
            <flux:button icon="arrow-down-tray" wire:click="export">Voorraadlijst</flux:button>
            <flux:button variant="primary" icon="plus" wire:click="create">Nieuw product</flux:button>
        </div>
    </div>

    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Zoek op naam of artikelnummer..." class="mb-4 max-w-sm" />

    <flux:table :paginate="$this->products">
        <flux:table.columns>
            <flux:table.column>Artikelnr.</flux:table.column>
            <flux:table.column>Naam</flux:table.column>
            <flux:table.column class="max-md:hidden">Stijl</flux:table.column>
            <flux:table.column align="end">Prijs</flux:table.column>
            <flux:table.column align="end">Voorraad</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell class="font-mono text-xs">{{ $product->sku }}</flux:table.cell>
                    <flux:table.cell variant="strong">{{ $product->name }}</flux:table.cell>
                    <flux:table.cell class="max-md:hidden">{{ $product->style }} · {{ $product->abv }}%</flux:table.cell>
                    <flux:table.cell align="end">€ {{ number_format($product->price, 2, ',', '.') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        @if ($product->isLowOnStock())
                            <flux:badge color="red" size="sm" icon="exclamation-triangle">{{ $product->stock }}</flux:badge>
                        @else
                            <flux:badge color="green" size="sm">{{ $product->stock }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item icon="pencil-square" wire:click="edit({{ $product->id }})">Bewerken</flux:menu.item>
                                <flux:menu.item icon="plus-circle" wire:click="restock({{ $product->id }})">Voorraad aanvullen</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" variant="danger"
                                    wire:click="delete({{ $product->id }})"
                                    wire:confirm="Weet je zeker dat je {{ $product->name }} wilt verwijderen?">Verwijderen</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">Geen producten gevonden.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Pop-up: nieuw product of product bewerken --}}
    <flux:modal name="product-form" class="w-full max-w-lg">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $form->product ? 'Product bewerken' : 'Nieuw product' }}</flux:heading>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.sku" label="Artikelnummer" placeholder="GG-BLOND" />
                <flux:input wire:model="form.style" label="Bierstijl" placeholder="Blond" />
            </div>

            <flux:input wire:model="form.name" label="Naam" placeholder="Gulle Gans Blond" />

            <div class="grid grid-cols-3 gap-4">
                <flux:input wire:model="form.abv" label="Alcohol %" type="number" step="0.1" />
                <flux:input wire:model="form.volume_cl" label="Inhoud (cl)" type="number" />
                <flux:input wire:model="form.price" label="Prijs (€)" type="number" step="0.01" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.stock" label="Voorraad" type="number" />
                <flux:input wire:model="form.min_stock" label="Minimale voorraad" type="number" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Annuleren</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Opslaan</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Pop-up: producten importeren uit Excel --}}
    <flux:modal name="import" class="w-full max-w-md">
        <form wire:submit="import" class="space-y-4">
            <flux:heading size="lg">Producten importeren</flux:heading>
            <flux:text>
                Kies een Excel-bestand met in rij 1 de kopjes <strong>Artikelnummer, Naam, Stijl, Alcohol,
                Inhoud (cl), Prijs, Voorraad</strong> en <strong>Minimale voorraad</strong>.
                Bestaat een artikelnummer al, dan wordt dat product bijgewerkt.
            </flux:text>

            <flux:input type="file" wire:model="file" label="Excel-bestand" accept=".xlsx,.csv" />

            @if ($importErrors)
                <flux:callout variant="danger" icon="x-circle" heading="Er is niets geïmporteerd:">
                    <flux:callout.text>
                        @foreach ($importErrors as $error)
                            {{ $error }}<br>
                        @endforeach
                    </flux:callout.text>
                </flux:callout>
            @endif

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Annuleren</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Importeren</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Pop-up: voorraad aanvullen --}}
    <flux:modal name="restock" class="w-full max-w-sm">
        <form wire:submit="saveRestock" class="space-y-4">
            <flux:heading size="lg">Voorraad aanvullen</flux:heading>
            <flux:text>Hoeveel flessen zijn er gebrouwen en gebotteld?</flux:text>

            <flux:input wire:model="restockAmount" label="Aantal flessen" type="number" min="1" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Annuleren</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Bijboeken</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

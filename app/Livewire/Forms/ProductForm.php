<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProductForm extends Form
{
    // Het product dat we bewerken (null = nieuw product)
    public ?Product $product = null;

    public string $sku = '';
    public string $name = '';
    public string $style = '';
    public string $abv = '';
    public string $volume_cl = '33';
    public string $price = '';
    public string $stock = '0';
    public string $min_stock = '24';

    protected function rules(): array
    {
        return [
            'sku' => ['required', 'max:20', Rule::unique('products')->ignore($this->product)],
            'name' => ['required', 'max:100'],
            'style' => ['nullable', 'max:50'],
            'abv' => ['required', 'numeric', 'between:0,20'],
            'volume_cl' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'sku' => 'artikelnummer',
            'name' => 'naam',
            'style' => 'bierstijl',
            'abv' => 'alcoholpercentage',
            'volume_cl' => 'inhoud',
            'price' => 'prijs',
            'stock' => 'voorraad',
            'min_stock' => 'minimale voorraad',
        ];
    }

    // Vul het formulier met de gegevens van een bestaand product
    public function setProduct(Product $product): void
    {
        $this->product = $product;

        $this->sku = $product->sku;
        $this->name = $product->name;
        $this->style = (string) $product->style;
        $this->abv = (string) $product->abv;
        $this->volume_cl = (string) $product->volume_cl;
        $this->price = (string) $product->price;
        $this->stock = (string) $product->stock;
        $this->min_stock = (string) $product->min_stock;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->product) {
            $this->product->update($data);
        } else {
            Product::create($data);
        }

        $this->reset();
    }
}

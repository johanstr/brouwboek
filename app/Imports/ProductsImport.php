<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithUpserts, SkipsEmptyRows
{
    // Eén rij uit Excel wordt één Product.
    // Dankzij WithHeadingRow gebruiken we de kolomnamen uit rij 1.
    public function model(array $row): Model|null
    {
        return new Product([
            'sku' => strtoupper(trim($row['artikelnummer'])),
            'name' => $row['naam'],
            'style' => $row['stijl'] ?? null,
            'abv' => $row['alcohol'],
            'volume_cl' => $row['inhoud_cl'] ?? 33,
            'price' => $row['prijs'],
            'stock' => $row['voorraad'] ?? 0,
            'min_stock' => $row['minimale_voorraad'] ?? 24,
        ]);
    }

    // Bestaat het artikelnummer al? Dan wordt het product bijgewerkt.
    public function uniqueBy(): string
    {
        return 'sku';
    }

    // Regels voor elke rij, net als bij een formulier
    public function rules(): array
    {
        return [
            'artikelnummer' => ['required', 'max:20'],
            'naam' => ['required', 'max:100'],
            'alcohol' => ['required', 'numeric', 'between:0,20'],
            'inhoud_cl' => ['nullable', 'integer', 'min:1'],
            'prijs' => ['required', 'numeric', 'min:0'],
            'voorraad' => ['nullable', 'integer', 'min:0'],
            'minimale_voorraad' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

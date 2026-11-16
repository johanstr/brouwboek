<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    // 1. Welke gegevens? Alle producten, op naam gesorteerd
    public function query(): Builder
    {
        return Product::query()->orderBy('name');
    }

    // 2. De kopjes in rij 1 (dezelfde als bij de import!)
    public function headings(): array
    {
        return [
            'Artikelnummer', 'Naam', 'Stijl', 'Alcohol', 'Inhoud (cl)',
            'Prijs', 'Voorraad', 'Minimale voorraad', 'Voorraadwaarde', 'Bijbrouwen?',
        ];
    }

    // 3. Van één Product maken we één rij in Excel
    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->style,
            (float) $product->abv,
            $product->volume_cl,
            (float) $product->price,
            $product->stock,
            $product->min_stock,
            round($product->stock * $product->price, 2),
            $product->isLowOnStock() ? 'JA' : '',
        ];
    }

    // 4. Opmaak: de kopjes vetgedrukt
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    // 5. Getalnotatie: euro's in kolom F en I
    public function columnFormats(): array
    {
        return [
            'F' => '€ #,##0.00',
            'I' => '€ #,##0.00',
        ];
    }
}

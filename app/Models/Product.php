<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['sku', 'name', 'style', 'abv', 'volume_cl', 'price', 'stock', 'min_stock'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'abv' => 'decimal:1',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
        ];
    }

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    // Is de voorraad lager dan de ondergrens?
    public function isLowOnStock(): bool
    {
        return $this->stock < $this->min_stock;
    }
}

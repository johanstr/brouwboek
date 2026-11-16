<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class StockService
{
    // Is er genoeg voorraad van dit product?
    public function hasEnough(Product $product, int $quantity): bool
    {
        return $product->stock >= $quantity;
    }

    // Flessen eraf (bij een bestelling)
    public function remove(Product $product, int $quantity): void
    {
        if (! $this->hasEnough($product, $quantity)) {
            throw new InvalidArgumentException("Er zijn maar {$product->stock} flessen {$product->name} op voorraad.");
        }

        $product->decrement('stock', $quantity);
    }

    // Flessen erbij (na het brouwen, of als een bestelling vervalt)
    public function add(Product $product, int $quantity): void
    {
        $product->increment('stock', $quantity);
    }

    // Alle producten waarvan de voorraad onder het minimum zit
    public function lowStock(): Collection
    {
        return Product::whereColumn('stock', '<', 'min_stock')->orderBy('stock')->get();
    }
}

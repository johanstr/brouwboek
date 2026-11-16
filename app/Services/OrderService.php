<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    // Laravel geeft ons automatisch een StockService (dependency injection)
    public function __construct(
        private StockService $stock,
    ) {}

    /**
     * Maak een nieuwe bestelling.
     *
     * @param  array<int, int>  $quantities  [product_id => aantal flessen]
     */
    public function create(Customer $customer, array $quantities, ?string $notes = null): Order
    {
        // Alleen regels met meer dan 0 flessen
        $quantities = array_filter(array_map('intval', $quantities), fn ($q) => $q > 0);

        if (empty($quantities)) {
            throw new InvalidArgumentException('Kies minstens één bier.');
        }

        // Alles of niets: lukt één stap niet, dan wordt alles teruggedraaid
        return DB::transaction(function () use ($customer, $quantities, $notes) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_date' => now(),
                'notes' => $notes ?: null,
            ]);

            foreach ($quantities as $productId => $quantity) {
                // lockForUpdate: niemand anders kan dit product tegelijk aanpassen
                $product = Product::lockForUpdate()->findOrFail($productId);

                $this->stock->remove($product, $quantity);

                $order->lines()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

            return $order;
        });
    }

    // Een open bestelling verwijderen; de flessen gaan terug in de voorraad
    public function cancel(Order $order): void
    {
        if ($order->status !== OrderStatus::Open) {
            throw new InvalidArgumentException('Alleen open bestellingen kun je verwijderen.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->lines as $line) {
                $this->stock->add($line->product, $line->quantity);
            }

            $order->delete();
        });
    }

    public function markAsPacked(Order $order): void
    {
        $order->update(['status' => OrderStatus::Packed]);
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoiceService
{
    // Het btw-percentage voor bier in Nederland
    public const VAT_RATE = 0.21;

    // Hoeveel dagen heeft de klant om te betalen?
    public const PAYMENT_DAYS = 14;

    public function createForOrder(Order $order): Invoice
    {
        if ($order->invoice()->exists()) {
            throw new InvalidArgumentException('Voor deze bestelling is al een factuur gemaakt.');
        }

        return DB::transaction(function () use ($order) {
            $subtotal = round($order->subtotal(), 2);
            $vat = round($subtotal * self::VAT_RATE, 2);

            $invoice = $order->invoice()->create([
                'number' => $this->nextNumber(),
                'invoice_date' => now(),
                'due_date' => now()->addDays(self::PAYMENT_DAYS),
                'subtotal' => $subtotal,
                'vat' => $vat,
                'total' => $subtotal + $vat,
            ]);

            $order->update(['status' => OrderStatus::Invoiced]);

            return $invoice;
        });
    }

    // Het volgende factuurnummer, bijv. 2026-0001, 2026-0002, ...
    public function nextNumber(): string
    {
        $year = now()->year;

        $last = Invoice::where('number', 'like', "{$year}-%")
            ->orderByDesc('number')
            ->value('number');

        $next = $last ? ((int) substr($last, 5)) + 1 : 1;

        return $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}

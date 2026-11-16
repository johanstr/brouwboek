<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendInvoice implements ShouldQueue
{
    use Queueable;

    // Hoe vaak proberen we het als het mislukt?
    public int $tries = 3;

    // Hoeveel seconden wachten we tussen de pogingen?
    public int $backoff = 60;

    public function __construct(
        public Invoice $invoice,
    ) {}

    public function handle(): void
    {
        $customer = $this->invoice->order->customer;

        Mail::to($customer->email, $customer->name)
            ->send(new InvoiceMail($this->invoice));

        $this->invoice->update(['sent_at' => now()]);
    }

    // Wordt aangeroepen als alle pogingen mislukt zijn
    public function failed(?Throwable $exception): void
    {
        Log::error("Factuur {$this->invoice->number} kon niet worden gemaild: " . $exception?->getMessage());
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Services\PdfService;

class PdfController extends Controller
{
    public function __construct(
        private PdfService $pdf,
    ) {}

    // Paklijst: openen in de browser, zodat je hem direct kunt printen
    public function packingList(Order $order)
    {
        return $this->pdf->packingList($order)->stream("paklijst-{$order->id}.pdf");
    }

    // Factuur: downloaden voor je eigen administratie
    public function invoice(Invoice $invoice)
    {
        return $this->pdf->invoice($invoice)->download($this->pdf->invoiceFilename($invoice));
    }
}

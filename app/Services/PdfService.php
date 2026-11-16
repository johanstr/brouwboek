<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    // De factuur als PDF
    public function invoice(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load('order.customer', 'order.lines.product');

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice])
            ->setPaper('a4');
    }

    // De paklijst als PDF
    public function packingList(Order $order): \Barryvdh\DomPDF\PDF
    {
        $order->load('customer', 'lines.product');

        return Pdf::loadView('pdf.packing-list', ['order' => $order])
            ->setPaper('a4');
    }

    // De bestandsnaam van een factuur, bijv. factuur-2026-0001.pdf
    public function invoiceFilename(Invoice $invoice): string
    {
        return "factuur-{$invoice->number}.pdf";
    }
}

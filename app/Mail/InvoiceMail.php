<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    // Deze public property kun je in de mail-view gebruiken als $invoice
    public function __construct(
        public Invoice $invoice,
    ) {}

    // De 'envelop': onderwerp, afzender, ...
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Factuur {$this->invoice->number} van " . config('brewery.name'),
        );
    }

    // De inhoud: een Markdown-mail
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invoice',
        );
    }

    // De bijlage: de factuur als PDF
    public function attachments(): array
    {
        $pdf = app(PdfService::class);

        return [
            Attachment::fromData(
                fn () => $pdf->invoice($this->invoice)->output(),
                $pdf->invoiceFilename($this->invoice),
            )->withMime('application/pdf'),
        ];
    }
}

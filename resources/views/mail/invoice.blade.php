<x-mail::message>
# Beste {{ $invoice->order->customer->contact_name ?? $invoice->order->customer->name }},

Bedankt voor je bestelling bij {{ config('brewery.name') }}!
In de bijlage vind je factuur **{{ $invoice->number }}**.

<x-mail::table>
| Omschrijving | Bedrag |
|:------------ | ------:|
| Subtotaal | € {{ number_format($invoice->subtotal, 2, ',', '.') }} |
| Btw 21% | € {{ number_format($invoice->vat, 2, ',', '.') }} |
| **Totaal** | **€ {{ number_format($invoice->total, 2, ',', '.') }}** |
</x-mail::table>

Wil je het bedrag vóór **{{ $invoice->due_date->format('d-m-Y') }}** overmaken naar
{{ config('brewery.iban') }} onder vermelding van het factuurnummer?

Proost!<br>
{{ config('brewery.name') }}
</x-mail::message>

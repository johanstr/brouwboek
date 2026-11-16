@extends('pdf.layout', ['title' => 'Factuur', 'subtitle' => $invoice->number])

@section('content')
    @php($customer = $invoice->order->customer)

    <table style="margin-top: 30px;">
        <tr>
            <td style="width: 55%;">
                <div class="muted">Factuur aan</div>
                <strong>{{ $customer->name }}</strong><br>
                @if ($customer->contact_name) t.a.v. {{ $customer->contact_name }}<br> @endif
                {{ $customer->address }}<br>
                {{ $customer->postcode }} {{ $customer->city }}
            </td>
            <td>
                <table>
                    <tr><td class="muted">Factuurnummer</td><td class="right">{{ $invoice->number }}</td></tr>
                    <tr><td class="muted">Factuurdatum</td><td class="right">{{ $invoice->invoice_date->format('d-m-Y') }}</td></tr>
                    <tr><td class="muted">Vervaldatum</td><td class="right">{{ $invoice->due_date->format('d-m-Y') }}</td></tr>
                    <tr><td class="muted">Bestelling</td><td class="right">#{{ $invoice->order->id }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>Omschrijving</th>
                <th class="right">Aantal</th>
                <th class="right">Prijs</th>
                <th class="right">Totaal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->order->lines as $line)
                <tr>
                    <td>{{ $line->product->name }} <span class="muted">({{ $line->product->volume_cl }} cl)</span></td>
                    <td class="right">{{ $line->quantity }}</td>
                    <td class="right">€ {{ number_format($line->price, 2, ',', '.') }}</td>
                    <td class="right">€ {{ number_format($line->total(), 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotaal</td><td class="right">€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
        <tr><td>Btw 21%</td><td class="right">€ {{ number_format($invoice->vat, 2, ',', '.') }}</td></tr>
        <tr class="grand"><td>Totaal</td><td class="right">€ {{ number_format($invoice->total, 2, ',', '.') }}</td></tr>
    </table>

    <p style="margin-top: 40px;">
        Wil je het totaalbedrag vóór <strong>{{ $invoice->due_date->format('d-m-Y') }}</strong> overmaken naar
        <strong>{{ config('brewery.iban') }}</strong> o.v.v. factuurnummer <strong>{{ $invoice->number }}</strong>?
    </p>
    <p>Proost en bedankt voor je bestelling!</p>
@endsection

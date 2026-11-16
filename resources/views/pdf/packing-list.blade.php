@extends('pdf.layout', ['title' => 'Paklijst', 'subtitle' => 'Bestelling #' . $order->id])

@section('content')
    <table style="margin-top: 30px;">
        <tr>
            <td style="width: 55%;" class="box">
                <div class="muted">Afleveradres</div>
                <strong>{{ $order->customer->name }}</strong><br>
                {{ $order->customer->address }}<br>
                {{ $order->customer->postcode }} {{ $order->customer->city }}<br>
                {{ $order->customer->phone }}
            </td>
            <td style="padding-left: 20px;">
                <div class="muted">Besteld op</div>
                {{ $order->order_date->format('d-m-Y') }}
                @if ($order->notes)
                    <div class="muted" style="margin-top: 8px;">Opmerking</div>
                    {{ $order->notes }}
                @endif
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th style="width: 30px;">✓</th>
                <th>Artikelnr.</th>
                <th>Bier</th>
                <th class="right">Flessen</th>
                <th class="right">Kratten (24)</th>
                <th class="right">Los</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->lines as $line)
                <tr>
                    <td><span class="check"></span></td>
                    <td>{{ $line->product->sku }}</td>
                    <td>{{ $line->product->name }}</td>
                    <td class="right">{{ $line->quantity }}</td>
                    <td class="right">{{ intdiv($line->quantity, 24) }}</td>
                    <td class="right">{{ $line->quantity % 24 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;"><strong>Totaal: {{ $order->bottleCount() }} flessen</strong></p>

    <table style="margin-top: 50px;">
        <tr>
            <td>Ingepakt door: ............................................</td>
            <td>Datum: ......................</td>
        </tr>
    </table>
@endsection

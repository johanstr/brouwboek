<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        /* Let op: dompdf kent geen flexbox of grid. Gebruik tabellen! */
        @page { margin: 30px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #27272a; }
        h1 { font-size: 22px; color: #b45309; margin: 0 0 4px 0; }
        .muted { color: #71717a; }
        table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .lines { margin-top: 24px; }
        .lines th { background: #b45309; color: #fff; text-align: left; padding: 6px; }
        .lines td { padding: 6px; border-bottom: 1px solid #e4e4e7; }
        .right, .lines th.right { text-align: right; }
        .totals { width: 45%; margin-left: 55%; margin-top: 12px; }
        .totals td { padding: 4px 6px; }
        .grand td { font-weight: bold; font-size: 13px; border-top: 2px solid #b45309; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #a1a1aa; }
        .box { border: 1px solid #e4e4e7; padding: 10px; }
        .check { width: 14px; height: 14px; border: 1px solid #52525b; display: inline-block; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 70px;">
                <img src="{{ public_path('images/logo.png') }}" style="width: 60px;">
            </td>
            <td>
                <strong>{{ config('brewery.name') }}</strong><br>
                {{ config('brewery.address') }}<br>
                {{ config('brewery.postcode') }} {{ config('brewery.city') }}<br>
                <span class="muted">{{ config('brewery.email') }} · {{ config('brewery.phone') }}</span>
            </td>
            <td class="right">
                <h1>{{ $title }}</h1>
                {{ $subtitle ?? '' }}
            </td>
        </tr>
    </table>

    @yield('content')

    <div class="footer">
        {{ config('brewery.name') }} · KvK {{ config('brewery.kvk') }} · btw {{ config('brewery.btw') }} · IBAN {{ config('brewery.iban') }}
    </div>
</body>
</html>

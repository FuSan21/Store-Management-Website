@php
    // Dompdf's bundled fonts have no taka sign, so amounts read "Tk 1,250".
    $tk = fn ($amount) => 'Tk '.number_format((float) $amount, 2);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->number() }}</title>
    <style>
        @page { margin: 36px 40px; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #0a0a0a; font-size: 11px; line-height: 1.45; }
        .muted { color: #5f5f5f; }
        .mark { display: inline-block; width: 22px; height: 22px; background: #d92b21; border-radius: 5px; vertical-align: middle; }
        .brand { font-size: 15px; font-weight: bold; vertical-align: middle; margin-left: 8px; }
        h1 { font-size: 22px; margin: 0; letter-spacing: -0.3px; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { vertical-align: top; padding: 0; }
        .items { margin-top: 26px; }
        .items th { text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.6px; color: #5f5f5f; border-bottom: 1.5px solid #0a0a0a; padding: 6px 4px; }
        .items td { border-bottom: 1px solid #e3e3e3; padding: 8px 4px; }
        .right { text-align: right; }
        .totals { width: 46%; margin-left: 54%; margin-top: 14px; }
        .totals td { padding: 4px; }
        .grand td { border-top: 1.5px solid #0a0a0a; font-size: 13px; font-weight: bold; padding-top: 8px; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #f2f2f2; font-size: 10px; }
        .foot { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; color: #5f5f5f; border-top: 1px solid #e3e3e3; padding-top: 8px; }
    </style>
</head>
<body>
    <table class="meta">
        <tr>
            <td><span class="mark"></span><span class="brand">{{ config('store.name') }}</span>
                <div class="muted" style="margin-top: 6px;">{{ config('store.contact.area') }}<br>{{ config('store.contact.email') }}</div>
            </td>
            <td class="right">
                <h1>Invoice</h1>
                <div style="margin-top: 4px;">Order {{ $order->number() }}</div>
                <div class="muted">{{ $order->created_at->format('j F Y') }}</div>
                <div style="margin-top: 6px;"><span class="status">{{ $order->status->label() }}</span></div>
            </td>
        </tr>
    </table>

    <table class="meta" style="margin-top: 28px;">
        <tr>
            <td style="width: 50%;">
                <div class="muted" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.6px;">Bill to</div>
                <div style="margin-top: 4px;"><strong>{{ $order->user?->name }}</strong><br>{{ $order->billing_address }}<br>{{ $order->user?->email }}</div>
            </td>
            <td style="width: 50%;">
                <div class="muted" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.6px;">Deliver to</div>
                <div style="margin-top: 4px;">{{ $order->shipping_address }}@if ($order->phone)<br>{{ $order->phone }}@endif<br>{{ $order->shipping_method->label() }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr><th>Item</th><th>BMUK</th><th class="right">Qty</th><th class="right">Unit price</th><th class="right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach ($order->orderDetails as $line)
                <tr>
                    <td>{{ $line->product?->title ?? 'Discontinued product' }}<br><span class="muted">{{ $line->product?->filter_type }}</span></td>
                    <td>{{ $line->bmuk_no }}</td>
                    <td class="right">{{ $line->quantity }}</td>
                    <td class="right">{{ $tk($line->price()) }}</td>
                    <td class="right">{{ $tk($line->lineTotal()) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="right">{{ $tk($order->subtotal) }}</td></tr>
        @if ((float) $order->discount > 0)
            <tr><td class="muted">Discount{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</td><td class="right">-{{ $tk($order->discount) }}</td></tr>
        @endif
        <tr><td class="muted">Delivery</td><td class="right">{{ (float) $order->shipping_cost > 0 ? $tk($order->shipping_cost) : 'Free' }}</td></tr>
        <tr class="grand"><td>Total</td><td class="right">{{ $tk($order->total_cost) }}</td></tr>
        <tr><td class="muted">Payment</td><td class="right">{{ $order->payment_method->label() }}</td></tr>
    </table>

    <div class="foot">Thank you for your order. Questions? Write to {{ config('store.contact.email') }} with your order number.</div>
</body>
</html>

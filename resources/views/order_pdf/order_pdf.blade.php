<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order {{ $order->orderno }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12px; }
        h1 { margin-bottom: 4px; }
        .meta { margin-bottom: 18px; }
        .parties { width: 100%; margin-bottom: 20px; }
        .parties td { width: 50%; vertical-align: top; padding: 8px; border: 1px solid #ddd; }
        table.items { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        .items th { background: #e8f4fb; }
        .number { text-align: right !important; }
        .total { margin-top: 14px; text-align: right; font-size: 14px; font-weight: bold; }
        .remark { margin-top: 18px; }
    </style>
</head>
<body>
    @php
        $buyer = $order->buyers;
        $seller = $order->sellers;
        $buyerName = $buyer?->shop_name ?? $buyer?->trade_name ?? $buyer?->legal_name ?? $buyer?->owner_name ?? '-';
        $sellerName = $seller?->shop_name ?? $seller?->trade_name ?? $seller?->legal_name ?? $seller?->owner_name ?? '-';
        $buyerAddress = $buyer?->address_line ?? $buyer?->billing_address ?? '-';
        $sellerAddress = $seller?->address_line ?? $seller?->billing_address ?? '-';
        $buyerPhone = $buyer?->mobile_number ?? $buyer?->mobile ?? '-';
        $sellerPhone = $seller?->mobile_number ?? $seller?->mobile ?? '-';
    @endphp

    <h1>Order Detail</h1>
    <div class="meta">
        <strong>Order No:</strong> {{ $order->orderno }} &nbsp;&nbsp;
        <strong>Order Date:</strong> {{ $order->order_date }} &nbsp;&nbsp;
        <strong>Customer Type:</strong> {{ $order->customer_type }}<br>
        <strong>Created By:</strong> {{ $order->createdbyname?->name ?? '-' }}
    </div>

    <table class="parties" cellspacing="0">
        <tr>
            <td>
                <strong>Seller / Parent</strong><br><br>
                <strong>{{ $sellerName }}</strong><br>
                {{ $sellerAddress }}<br>
                Phone: {{ $sellerPhone }}
            </td>
            <td>
                <strong>Buyer Customer</strong><br><br>
                <strong>{{ $buyerName }}</strong><br>
                {{ $buyerAddress }}<br>
                Phone: {{ $buyerPhone }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Product Code</th>
                <th class="number">Quantity</th>
                <th class="number">Rate</th>
                <th class="number">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderdetails as $detail)
                <tr>
                    <td>{{ $detail->products?->product_name ?? '-' }}</td>
                    <td>{{ $detail->products?->product_code ?? '-' }}</td>
                    <td class="number">{{ $detail->quantity }}</td>
                    <td class="number">{{ number_format((float) $detail->price, 2) }}</td>
                    <td class="number">{{ number_format((float) $detail->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">Grand Total: {{ number_format((float) $order->grand_total, 2) }}</div>
    <div class="remark"><strong>Remark:</strong> {{ $order->order_remark ?: '-' }}</div>
</body>
</html>

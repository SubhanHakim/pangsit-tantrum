<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pesanan #{{ $order->order_id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            background-color: #fff;
            color: #333;
        }
        .container {
            max-width: 100%;
            background-color: #fff;
            padding: 10px;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #811D0E;
            position: relative;
        }
        .header::before {
            content: "";
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            height: 10px;
            background: repeating-linear-gradient(45deg, #811D0E, #811D0E 10px, transparent 10px, transparent 20px);
            opacity: 0.1;
        }
        .logo {
            display: block;
            margin: 0 auto 5px auto;
            width: 50px;
            height: 50px;
            background-color: #811D0E;
            border-radius: 50%;
            position: relative;
            overflow: hidden;
        }
        .logo::after {
            content: "PT";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .store-name {
            font-size: 16pt;
            font-weight: bold;
            color: #811D0E;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .store-info {
            font-size: 8pt;
            color: #555;
            margin-top: 3px;
        }
        .receipt-title {
            font-weight: bold;
            text-align: center;
            margin: 12px 0;
            font-size: 12pt;
            color: #333;
            background-color: #f9f1ec;
            padding: 6px;
            border-radius: 15px;
        }
        .order-info {
            margin: 15px 0;
            font-size: 9pt;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            border-left: 3px solid #811D0E;
        }
        .order-info div {
            margin-bottom: 3px;
        }
        .order-label {
            color: #666;
            display: inline-block;
            width: 90px;
        }
        .divider {
            border-bottom: 1px dashed #ccc;
            margin: 10px 0;
        }
        .bold-divider {
            border-bottom: 2px dashed #811D0E;
            margin: 15px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .items-table th {
            text-align: left;
            font-weight: bold;
            padding: 5px 0;
            color: #811D0E;
            border-bottom: 1px solid #eee;
        }
        .items-table td {
            padding: 6px 0;
            vertical-align: top;
            border-bottom: 1px dotted #eee;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .menu-name {
            font-weight: bold;
        }
        .total-section {
            margin-top: 10px;
            font-size: 10pt;
            background-color: #f9f1ec;
            padding: 10px;
            border-radius: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 12pt;
            margin-top: 5px;
            color: #811D0E;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8pt;
            color: #888;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        .footer-message {
            margin: 15px 0;
            font-size: 10pt;
            text-align: center;
            font-weight: bold;
            color: #555;
        }
        .item-note {
            font-style: italic;
            font-size: 8pt;
            color: #777;
            margin-top: 2px;
        }
        .item-topping {
            padding-left: 12px;
            font-size: 8pt;
            color: #666;
            position: relative;
            margin-top: 2px;
        }
        .item-topping:before {
            content: "+";
            position: absolute;
            left: 3px;
            color: #811D0E;
        }
        .order-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            background-color: #4CAF50;
            color: white;
        }
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        .qr-code img {
            width: 80px;
            height: 80px;
        }
        .qr-code-text {
            font-size: 7pt;
            color: #888;
            margin-top: 3px;
        }
        .thank-you {
            font-size: 14pt;
            font-weight: bold;
            color: #811D0E;
            text-align: center;
            margin-bottom: 5px;
        }
        .ornament {
            text-align: center;
            font-size: 12pt;
            color: #811D0E;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"></div>
            <div class="store-name">PANGSIT TANTRUM</div>
            <div class="store-info">
                Jl. Contoh Alamat No. 123, Kota<br>
                Telp: 0812-3456-7890
            </div>
        </div>
        
        <div class="receipt-title">STRUK PESANAN</div>
        
        <div class="order-info">
            <div><span class="order-label">No. Pesanan</span>: <strong>{{ $order->order_id }}</strong></div>
            <div><span class="order-label">Tanggal</span>: {{ $order->created_at->format('d M Y H:i') }}</div>
            <div><span class="order-label">Pelanggan</span>: {{ $order->customer_name }}</div>
            <div><span class="order-label">No. Meja</span>: {{ $order->table_number }}</div>
            <div><span class="order-label">Status</span>: 
                <span class="order-status">
                    {{ $order->status == 'success' || $order->status == 'paid' ? 'LUNAS' : strtoupper($order->status) }}
                </span>
            </div>
        </div>
        
        <div class="bold-divider"></div>
        
        <table class="items-table">
            <tr>
                <th width="55%">Item</th>
                <th width="15%">Qty</th>
                <th width="30%" class="text-right">Harga</th>
            </tr>
            
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="menu-name">{{ $item->menu->name ?? 'Menu tidak tersedia' }}</div>
                    @if($item->note)
                        <div class="item-note">Catatan: {{ $item->note }}</div>
                    @endif
                    @if($item->toppings && is_string($item->toppings))
                        @foreach(json_decode($item->toppings) as $topping)
                            <div class="item-topping">{{ $topping->name ?? '' }} (Rp {{ number_format($topping->price ?? 0, 0, ',', '.') }})</div>
                        @endforeach
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>
        
        <div class="bold-divider"></div>
        
        <div class="total-section">
            <div class="total-row grand-total">
                <div>TOTAL</div>
                <div>Rp {{ number_format($order->total, 0, ',', '.') }}</div>
            </div>
        </div>
        
        <div class="ornament">~~~~~~~~~~~~~~</div>
        <div class="thank-you">Terima Kasih</div>
        <div class="footer-message">
            Selamat menikmati!
        </div>
        <div class="ornament">~~~~~~~~~~~~~~</div>
        
        <div class="footer">
            Struk ini adalah bukti pembayaran yang sah<br>
            {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>
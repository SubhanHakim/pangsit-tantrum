<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch Struk Pesanan</title>
    <style>
        /* Style untuk multi-struk */
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .receipt {
            page-break-after: always;
            padding: 10px;
            max-width: 80mm;
            margin: 0 auto;
        }
        /* Sisanya sama seperti style di atas */
        /* ... */
    </style>
</head>
<body>
    @foreach($orders as $order)
    <div class="receipt">
        <!-- Copy template struk dari file order.blade.php -->
        <div class="header">
            <div class="store-name">PANGSIT TANTRUM</div>
            <div class="store-info">
                Jl. Contoh Alamat No. 123, Kota<br>
                Telp: 0812-3456-7890
            </div>
        </div>
        
        <!-- Sisanya sama seperti template struk di atas -->
        <!-- ... -->
    </div>
    @endforeach
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code Pangsit Tantrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .qr-container {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .restaurant-name {
            color: #811D0E;
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .qr-image {
            margin: 20px 0;
        }
        .instructions {
            margin-top: 20px;
            text-align: left;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        .instructions h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .instructions ol {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="qr-container">
            <div class="restaurant-name">PANGSIT TANTRUM</div>
            
            <div class="qr-image">
                <img src="{{ route('qrcode.download') }}" alt="QR Code Menu" width="250">
            </div>
            
            <p class="mb-3">Scan QR Code untuk melihat menu dan memesan</p>
            
            <a href="{{ route('qrcode.download') }}" class="btn btn-primary">Download QR Code</a>
            
            <div class="instructions mt-4">
                <h3>Cara Pemesanan:</h3>
                <ol>
                    <li>Scan QR code di atas</li>
                    <li>Pilih menu yang diinginkan</li>
                    <li>Tambahkan ke keranjang</li>
                    <li>Masukkan nomor meja Anda saat checkout</li>
                    <li>Lakukan pembayaran</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
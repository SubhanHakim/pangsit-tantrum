<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; }
        .container { width: 100%; margin: 0 auto; }
        h1 { text-align: center; margin-bottom: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { margin-bottom: 30px; }
        .summary-item { margin-bottom: 10px; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
        .date-range { text-align: center; font-style: italic; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Keuangan</h1>
            <div class="date-range">
                Periode: {{ \Carbon\Carbon::parse($data['startDate'])->format('d M Y') }} - 
                {{ \Carbon\Carbon::parse($data['endDate'])->format('d M Y') }}
            </div>
        </div>
        
        <div class="summary">
            <div class="summary-item">
                <span class="label">Total Pendapatan:</span> 
                Rp {{ number_format($data['totalRevenue'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="summary-item">
                <span class="label">Jumlah Transaksi:</span> 
                {{ $data['totalTransactions'] ?? 0 }}
            </div>
            <div class="summary-item">
                <span class="label">Rata-rata per Transaksi:</span> 
                Rp {{ number_format($data['averageTransaction'] ?? 0, 0, ',', '.') }}
            </div>
        </div>
        
        <h3>Menu Terlaris</h3>
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th style="text-align: right;">Jumlah Terjual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['topMenus'] ?? [] as $menu)
                    <tr>
                        <td>{{ $menu->name }}</td>
                        <td style="text-align: right;">{{ $menu->total_qty }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
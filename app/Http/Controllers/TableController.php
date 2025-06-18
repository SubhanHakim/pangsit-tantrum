<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function generate($id)
    {
        $table = Table::findOrFail($id);
        
        // Generate unique code jika belum ada
        if (!$table->qr_code) {
            $table->qr_code = Str::uuid();
            $table->save();
        }
        
        // Buat URL yang mengarah ke halaman pembayaran dengan parameter meja
        $url = route('checkout', ['table_code' => $table->qr_code]);
        
        // Generate QR code
        $qrCode = QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->generate($url);
                
        // Untuk download QR code sebagai file
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="table-'.$table->name.'.png"');
    }
}

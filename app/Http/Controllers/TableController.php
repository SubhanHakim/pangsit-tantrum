<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class TableController extends Controller
{
    // public function generate($id)
    // {
    //     $table = Table::findOrFail($id);
        
    //     // Generate unique code jika belum ada
    //     if (!$table->qr_code) {
    //         $table->qr_code = Str::uuid();
    //         $table->save();
    //     }
        
    //     // Buat URL yang mengarah ke halaman pembayaran dengan parameter meja
    //     $url = route('payment', ['table_code' => $table->qr_code]);
        
    //     // Generate QR code
    //     $result = Builder::create()
    //         ->writer(new PngWriter())
    //         ->data($url)
    //         ->encoding(new Encoding('UTF-8'))
    //         ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    //         ->size(300)
    //         ->margin(10)
    //         ->build();
            
    //     // Download QR code sebagai file
    //     return response($result->getString())
    //         ->header('Content-Type', $result->getMimeType())
    //         ->header('Content-Disposition', 'attachment; filename="table-'.$table->name.'.png"');
    // }
}

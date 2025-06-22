<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh as High;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;

class QrCodeController extends Controller
{
    public function generate()
    {
        // URL ke halaman menu
        $url = url('/');
        
        // Cara alternatif yang lebih sederhana untuk versi endroid/qr-code terbaru
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(300)
            ->setMargin(10);
            
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
            
        // Download QR code sebagai file
        return response($result->getString())
            ->header('Content-Type', $result->getMimeType())
            ->header('Content-Disposition', 'attachment; filename="pangsit-tantrum-menu.png"');
    }
    
    public function view()
    {
        // Halaman web untuk melihat QR code
        return view('qrcode');
    }
}
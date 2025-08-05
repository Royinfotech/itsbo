<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;

class QRController extends Controller
{
    public function studentQr($studentId)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);

        // You can encode any data, here we use the student ID
        $qrImage = $writer->writeString($studentId);

        // Return as a PNG image response
        return response($qrImage)
            ->header('Content-Type', 'image/png');
    }
} 
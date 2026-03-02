<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function sertifikat()
    {
        $pdf = Pdf::loadView('pdf.sertifikat')
                    ->setPaper('a4', 'landscape');
        return $pdf->download('sertifikat.pdf');
    }

    public function pengumuman()
    {
        $pdf = Pdf::loadView('pdf.pengumuman')
                    ->setPaper('a4', 'portrait');
        return $pdf->download('pengumuman.pdf');
    }
}

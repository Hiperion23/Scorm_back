<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificationController extends Controller
{
    public function generar(Request $request)
    {
        $empresa = $request->input('empresa', 'Nombre de la Empresa');
        $fecha = $request->input('fecha', now()->format('d/m/Y'));

        $pdf = Pdf::loadView('certification', compact('empresa', 'fecha'))
                  ->setPaper('A4', 'landscape');

        return $pdf->stream("Certificado_{$empresa}.pdf");
    }
}

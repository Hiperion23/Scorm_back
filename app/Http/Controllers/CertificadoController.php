<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;

class CertificadoController extends Controller
{
    public function generar()
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->setChroot([
            base_path(),
            public_path(),
            storage_path()
        ]);

        $datos = [
            'nombre' => 'RAFAEL CALLIRGOS SILVA',
            'curso' => 'PROGRAMA INTEGRADO DE CUMPLIMIENTO 2022',
            'duracion' => '01 HORA',
            'fecha' => now()->format('d/m/y')
        ];

        $pdf = Pdf::loadView('certificado.plantilla', $datos);
        $dompdf = $pdf->getDomPDF();
        $dompdf->setOptions($options);

        $pdf->setPaper([0, 0, 842, 595], 'portrait');

        return $pdf->stream('certificado.pdf');
    }
}

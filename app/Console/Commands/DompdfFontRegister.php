<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DompdfFontRegister extends Command
{
    protected $signature = 'dompdf:register-font';
    protected $description = 'Registra la fuente personalizada GreycliffCF-Bold para DomPDF';

    public function handle()
    {
        $fontDir = storage_path('fonts');
        $fontName = 'greycliffcfbold';
        $fontPath = $fontDir . '/GreycliffCF-Bold.ttf';

        if (!file_exists($fontPath)) {
            $this->error("La fuente no se encuentra en: $fontPath");
            return;
        }

        $destination = $fontDir . '/' . $fontName . '.ttf';
        if (!copy($fontPath, $destination)) {
            $this->error("No se pudo copiar la fuente.");
            return;
        }

        $this->info("Fuente '$fontName' registrada correctamente en: $destination");
    }
}

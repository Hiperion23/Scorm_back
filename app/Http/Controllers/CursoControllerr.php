<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class CursoController extends Controller
{
    public function index()
    {
        return response()->json([
            [
                "slug" => "alimentacion-saludable",
                "titulo" => "Alimentación Saludable",
                "categoria" => "Nutrición",
                "duracion" => "1 hora",
                "descripcion" => "Aprende a llevar una dieta equilibrada.",
                "ruta" => "/cursos/comida",
                "thumbnail" => "https://images.unsplash.com/photo-1546069901-ba9599a7e63c"
            ],
            [
                "slug" => "fundamentos-futbol",
                "titulo" => "Fundamentos del Fútbol",
                "categoria" => "Deporte",
                "duracion" => "1.5 horas",
                "descripcion" => "Domina las bases del fútbol desde cero.",
                "ruta" => "/cursos/futbol",
                "thumbnail" => "https://images.pexels.com/photos/25525183/pexels-photo-25525183.jpeg"
            ],
            [
                "slug" => "introduccion-viajes",
                "titulo" => "Introducción a los Viajes",
                "categoria" => "Cultura y ocio",
                "duracion" => "45 minutos",
                "descripcion" => "Descubre el poder transformador de viajar.",
                "ruta" => "/cursos/viaje",
                "thumbnail" => "https://images.pexels.com/photos/28821827/pexels-photo-28821827.jpeg"
            ]
        ]);
    }

    public function exportar(Request $request, $slug)
    {
        $html = $request->input('html');

        if (!$html) {
            return response()->json(['error' => 'No se recibió contenido HTML desde el frontend'], 400);
        }

        // Carpeta temporal
        $tempDir = storage_path("app/scorm_tmp_{$slug}");
        File::deleteDirectory($tempDir);
        File::makeDirectory($tempDir, 0755, true);

        // HTML renderizado del front
$tailwindCdn = '<script src="https://cdn.tailwindcss.com"></script>';

$contenido = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>{$slug}</title>
  {$tailwindCdn}
</head>
<body class="bg-white text-gray-800">
  {$html}
</body>
</html>
HTML;

        file_put_contents("{$tempDir}/index.html", $contenido);

        // Archivo manifest SCORM
        $manifest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<manifest identifier="MANIFEST-{$slug}" version="1.0"
  xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2"
  xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.imsproject.org/xsd/imscp_rootv1p1p2
  imscp_rootv1p1p2.xsd
  http://www.adlnet.org/xsd/adlcp_rootv1p2
  adlcp_rootv1p2.xsd">

  <organizations default="ORG-1">
    <organization identifier="ORG-1">
      <title>{$slug}</title>
      <item identifier="ITEM-1" identifierref="RES-1">
        <title>{$slug}</title>
      </item>
    </organization>
  </organizations>

  <resources>
    <resource identifier="RES-1" type="webcontent" adlcp:scormtype="sco" href="index.html">
      <file href="index.html"/>
    </resource>
  </resources>
</manifest>
XML;

        file_put_contents("{$tempDir}/imsmanifest.xml", $manifest);

        // Crear ZIP limpio
        $zipPath = storage_path("app/public/scorm/zips/{$slug}.zip");
        File::delete($zipPath);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'No se pudo crear el ZIP'], 500);
        }

        // Solo index.html y imsmanifest.xml en la raíz del ZIP
        $zip->addFile("{$tempDir}/index.html", "index.html");
        $zip->addFile("{$tempDir}/imsmanifest.xml", "imsmanifest.xml");
        $zip->close();

        // Limpiar temporal
        File::deleteDirectory($tempDir);

        return response()->download($zipPath);
    }
}

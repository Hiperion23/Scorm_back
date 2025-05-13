<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ScormController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:zip',
            'nombre' => 'required|string|max:255'
        ]);

        $file = $request->file('archivo');
        $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $folder = 'scorm/' . $slug . '-' . uniqid();
        $storagePath = storage_path('app/public/' . $folder);

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $tempZip = storage_path('app/temp_' . uniqid() . '.zip');
        $file->move(dirname($tempZip), basename($tempZip));

        $zip = new ZipArchive;
        if ($zip->open($tempZip) === TRUE) {
            $zip->extractTo($storagePath);
            $zip->close();
        } else {
            return response()->json(['error' => 'No se pudo abrir el archivo ZIP'], 400);
        }

        $manifestPath = $storagePath . '/imsmanifest.xml';
        if (!file_exists($manifestPath)) {
            return response()->json(['error' => 'imsmanifest.xml no encontrado'], 400);
        }

        $xml = simplexml_load_file($manifestPath);
        $resource = $xml->resources->resource[0];
        $launchFile = (string) $resource['href'] ?? 'index.html';

        if (!file_exists($storagePath . '/' . $launchFile)) {
            return response()->json(['error' => "Archivo de inicio '$launchFile' no encontrado"], 400);
        }

        $id = DB::table('scorm_courses')->insertGetId([
            'name' => $request->nombre,
            'slug' => $slug,
            'launch_file' => $launchFile,
            'folder_path' => $folder,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Curso SCORM cargado correctamente',
            'id' => $id,
            'launch_url' => asset("storage/{$folder}/{$launchFile}")
        ]);
    }
}

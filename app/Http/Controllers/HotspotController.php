<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use App\Models\Alternativa;
use Illuminate\Http\Request;

class HotspotController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'intro_video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:600000',
            'tiempo' => 'required|integer',
            'pausar_en' => 'required|integer',
            'habilidad' => 'required|integer',
            'opcion' => 'required|array',
            'ancho' => 'required|array',
            'alto' => 'required|array',
            'eje_x' => 'required|array',
            'eje_y' => 'required|array',
            'radio' => 'required|array',
            'img_rpta' => 'array'
        ]);

        $videoPath = $request->file('intro_video')->store('videos', 'public');

        $hotspot = Hotspot::create([
            'intro_video' => $videoPath,
            'tiempo' => $request->tiempo,
            'pausar_en' => $request->pausar_en,
            'habilidad' => $request->habilidad,
        ]);

        foreach ($request->opcion as $index => $opcion) {
            $imgPath = null;
            if (isset($request->img_rpta[$index])) {
                $imgPath = $request->img_rpta[$index]->store('imagenes', 'public');
            }

            $hotspot->alternativas()->create([
                'opcion' => $opcion,
                'ancho' => $request->ancho[$index],
                'alto' => $request->alto[$index],
                'eje_x' => $request->eje_x[$index],
                'eje_y' => $request->eje_y[$index],
                'radio' => $request->radio[$index],
                'img_rpta' => $imgPath,
            ]);
        }

        return response()->json(['message' => 'Hotspot guardado'], 201);
    }

    public function show($id)
    {
        $hotspot = Hotspot::with('alternativas')->findOrFail($id);

        return response()->json([
            'video_url' => asset('storage/' . $hotspot->intro_video),
            'tiempo' => $hotspot->tiempo,
            'pausar_en' => $hotspot->pausar_en,
            'habilidad' => $hotspot->habilidad,
            'alternativas' => $hotspot->alternativas->map(function ($item) {
                return [
                    'id' => $item->id,
                    'opcion' => $item->opcion,
                    'ancho' => $item->ancho,
                    'alto' => $item->alto,
                    'eje_x' => $item->eje_x,
                    'eje_y' => $item->eje_y,
                    'radio' => $item->radio,
                    'img_url' => $item->img_rpta ? asset('storage/' . $item->img_rpta) : null
                ];
            })
        ]);
    }
}

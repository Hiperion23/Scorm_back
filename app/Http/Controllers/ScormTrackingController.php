<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ScormTracking;
use Illuminate\Support\Facades\Log;

class ScormTrackingController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Verificar que el usuario esté logeado
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'No autenticado'], 401);
            }

            // Validar los datos del request
            $validated = $request->validate([
                'course_id' => 'required|exists:scorm_courses,id',
                'element'   => 'required|string',
                'value'     => 'nullable|string',
            ]);

            // Guardar el tracking
            ScormTracking::create([
                'user_id'   => $user->id,
                'course_id' => $validated['course_id'],
                'attempt'   => 1,
                'element'   => $validated['element'],
                'value'     => $validated['value'],
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            // Loguear el error para depurar
            Log::error('SCORM_TRACKING_ERROR', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'error' => 'Error al guardar el progreso',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ScormTrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json('Funciona correctamente');
});

// Registro
Route::post('/register', [RegisterController::class, 'register']);

// Middleware de autenticación con Sanctum
Route::middleware([
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    StartSession::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
])->group(function () {
    // Autenticación
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Rutas protegidas
    Route::get('/cursos', [CursoController::class, 'index']);

    // 🟢 RUTA FALTANTE (agrega esto)
    Route::post('/scorm/track', [ScormTrackingController::class, 'store']);
});

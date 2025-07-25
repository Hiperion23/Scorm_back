<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\ScormController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ScormLauncherController;
use App\Http\Controllers\ScormRuntimeController;
use App\Http\Controllers\CertificationController;

// Este grupo garantiza que todas estas rutas tengan el middleware 'web'
Route::middleware('web')->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/certification', [CertificationController::class, 'generar']);

    // Certificado PDF
    Route::get('/certificado', [CertificadoController::class, 'generar']);

    // Formulario de subida
    Route::get('/scorm/form', function () {
        return view('scorm.upload');
    });

    // Subida del SCORM
    Route::post('/scorm/upload', [ScormController::class, 'upload']);

    // Listado de cursos
    Route::get('/cursos', [CursoController::class, 'index']);
    Route::post('/cursos/{slug}/exportar', [CursoController::class, 'exportar']);

    // Lanzar visor SCORM (ahora protegido)
    Route::get('/scorm/launch/{id}', [ScormLauncherController::class, 'launch']);

    // CSRF y Autenticación
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->noContent();
    });

    Route::post('/login', function (Request $request) {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $request->session()->regenerate();
        return response()->json(['message' => 'Autenticado']);
    });

    Route::get('/login', function () {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'message' => 'Esta ruta solo acepta solicitudes POST para autenticación. Por favor, utiliza la aplicación frontend para iniciar sesión.'
            ], 400);
        }

        return redirect('http://localhost:5173/login');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json(['message' => 'Sesión cerrada']);
    });

    Route::get('/user', function () {
        return Auth::user();
    })->middleware('auth');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['message' => 'Registrado correctamente', 'user' => $user]);
    });

    // Rutas SCORM Runtime (protegidas)
    Route::middleware(['auth'])->group(function () {
        Route::post('/scorm/runtime/initialize', [ScormRuntimeController::class, 'initialize']);
        Route::post('/scorm/runtime/get-value', [ScormRuntimeController::class, 'getValue']);
        Route::post('/scorm/runtime/set-value', [ScormRuntimeController::class, 'setValue']);
        Route::post('/scorm/runtime/commit', [ScormRuntimeController::class, 'commit']);
        Route::post('/scorm/runtime/finish', [ScormRuntimeController::class, 'finish']);
        Route::get('/scorm/runtime/get-last-error', [ScormRuntimeController::class, 'getLastError']);
        Route::get('/scorm/runtime/get-error-string', [ScormRuntimeController::class, 'getErrorString']);
        Route::get('/scorm/runtime/get-diagnostic', [ScormRuntimeController::class, 'getDiagnostic']);
    });
});

/* Route::get('/{any}', function () {
    return file_get_contents(public_path('visor/index.html'));
})->where('any', '.*'); */

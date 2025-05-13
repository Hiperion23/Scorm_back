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

Route::get('/', function () {
    return view('welcome');
});

// Certificado PDF
Route::get('/certificado', [CertificadoController::class, 'generar']);

Route::post('/scorm/upload', [ScormController::class, 'upload']);

// Listado de cursos
Route::get('/cursos', [CursoController::class, 'index']);
Route::post('/cursos/{slug}/exportar', [CursoController::class, 'exportar']);

Route::get('/scorm/form', function () {
    return view('scorm.upload');
});

Route::get('/scorm/launch/{id}', [ScormLauncherController::class, 'launch']);

// Autenticación con Sanctum
Route::middleware('web')->group(function () {
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
});

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

// Route::get('/{any}', function () {
//     return file_get_contents(public_path('index.html'));
// })->where('any', '.*');

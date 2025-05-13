<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CursoController extends Controller
{
    public function index()
{
    $cursos = DB::table('scorm_courses')->get();

    $cursos = $cursos->map(function ($curso) {
        $curso->launch_url = url("/scorm/launch/{$curso->id}");
        return $curso;
    });

    return response()->json($cursos);
}


}

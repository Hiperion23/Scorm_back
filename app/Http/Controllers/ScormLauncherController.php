<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ScormLauncherController extends Controller
{
    public function launch($id)
    {
        $curso = DB::table('scorm_courses')->find($id);

        if (!$curso) {
            return response('Curso SCORM no encontrado', 404);
        }

        $indexPath = storage_path("app/public/{$curso->folder_path}/{$curso->launch_file}");

        if (!file_exists($indexPath)) {
            return response("Archivo '{$curso->launch_file}' no encontrado", 404);
        }

        $html = file_get_contents($indexPath);

        if (!$html || trim($html) === '') {
            return response("El archivo '{$curso->launch_file}' está vacío", 500);
        }

        $base = asset("storage/{$curso->folder_path}");

        // Inyectar <base> + SCORM API justo al inicio del <head>
        $fullInjection = <<<HTML
<base href="{$base}/">
<script>
  console.log("✅ Inyectando SCORM API");

  (() => {
    const scormData = {
      "cmi.core.student_name": "Ramírez, Hamilton",
      "cmi.core.student_id": "hamilton@daktico.com",
      "cmi.core.lesson_status": "not attempted",
      "cmi.core.score.raw": "0",
      "cmi.suspend_data": ""
    };

    let initialized = false;

    window.API = {
      LMSInitialize: () => {
        console.log("[SCORM] LMSInitialize");
        initialized = true;
        return "true";
      },
      LMSGetValue: (element) => {
        console.log("[SCORM] LMSGetValue", element);
        return initialized ? scormData[element] ?? "" : "";
      },
      LMSSetValue: (element, value) => {
        console.log("[SCORM] LMSSetValue", element, value);
        if (!initialized) return "false";
        scormData[element] = value;

        fetch("/api/scorm/track", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            course_id: "{ $curso->id }",
            element: element,
            value: value
        })
        });
        return "true";
      },
      LMSCommit: () => {
        console.log("[SCORM] LMSCommit");
        return "true";
      },
      LMSFinish: () => {
        console.log("[SCORM] LMSFinish");
        initialized = false;
        return "true";
      },
      LMSGetLastError: () => "0",
      LMSGetErrorString: () => "No error",
      LMSGetDiagnostic: () => ""
    };
  })();
</script>
HTML;

        $html = preg_replace('/<head[^>]*>/i', "$0\n{$fullInjection}", $html);

        $baseAssets = $base . '/assets/';

        // Reescribir rutas absolutas a /assets/
        $html = preg_replace('/src=[\'"]\/assets\/(.*?)[\'"]/', 'src="' . $baseAssets . '$1"', $html);
        $html = preg_replace('/href=[\'"]\/assets\/(.*?)[\'"]/', 'href="' . $baseAssets . '$1"', $html);

        // Reescribir referencias absolutas a /index.html
        $html = str_replace('src="/index.html"', 'src="' . $base . '/index.html"', $html);
        $html = str_replace("src='/index.html'", "src='" . $base . "/index.html'", $html);
        $html = str_replace('href="/index.html"', 'href="' . $base . '/index.html"', $html);
        $html = str_replace("href='/index.html'", "href='" . $base . "/index.html'", $html);

        // Detectar si iframe.setAttribute('src', '/index.html') y repararlo
        $html = preg_replace(
            '/setAttribute\(\s*[\'"]src[\'"]\s*,\s*[\'"]\/index\.html[\'"]\s*\)/',
            'setAttribute("src", "' . $base . '/index.html")',
            $html
        );

        // Inyectar refuerzo en caso no exista <base> y sea manipulado por JS
        $jsBaseOverride = <<<HTML
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const base = document.querySelector('base');
    if (!base) {
      const b = document.createElement('base');
      b.href = "{$base}/";
      document.head.appendChild(b);
    }
  });
</script>
HTML;

        $html = str_ireplace('</head>', $jsBaseOverride . "\n</head>", $html);

        return response($html)->header('Content-Type', 'text/html');
    }
}

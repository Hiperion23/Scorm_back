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

        $path = storage_path("app/public/{$curso->folder_path}/{$curso->launch_file}");
        if (!file_exists($path)) {
            return response("Archivo de inicio no encontrado: {$curso->launch_file}", 404);
        }

        $html = file_get_contents($path);
        if (!$html || trim($html) === '') {
            return response("El archivo '{$curso->launch_file}' está vacío", 500);
        }

        $base = asset("storage/{$curso->folder_path}");


        $jsFiles = glob(storage_path("app/public/{$curso->folder_path}") . '/html5/lib/scripts/*.js');
        foreach ($jsFiles as $jsFile) {
            if (strpos(basename($jsFile), 'bootstrapper') !== false || strpos(basename($jsFile), 'frame') !== false) {
                $jsContent = file_get_contents($jsFile);

                $jsContent = preg_replace(
                    '/window\.location\.pathname\.split\([^)]+\)\.slice\([^)]+\)\.join\([^)]+\)/',
                    '"' . "/storage/{$curso->folder_path}" . '"',
                    $jsContent
                );

                $jsContent = str_replace(
                    'window.location.pathname',
                    '"/' . $curso->folder_path . '/' . $curso->launch_file . '"',
                    $jsContent
                );

                file_put_contents($jsFile, $jsContent);
            }
        }

        // 2. Inyectar script de interceptación temprana
        $apiInjection = <<<HTML
<base href="{$base}/">
<script>
  (() => {
    console.log(" Interceptación AGRESIVA de rutas SCORM");

    const baseCorrect = "{$base}";
    const wrongBasePath = "/scorm/launch/{$id}";

    function fixUrl(url) {
      if (typeof url !== 'string') return url;

      if (url.includes(wrongBasePath)) {
        const newUrl = url.replace(wrongBasePath, baseCorrect);
        console.log(" URL CORREGIDA:", url, "→", newUrl);
        return newUrl;
      }

      return url;
    }

    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalFetch = window.fetch;
    const originalCreateElement = document.createElement;

    XMLHttpRequest.prototype.open = function(method, url, ...args) {
      return originalXHROpen.call(this, method, fixUrl(url), ...args);
    };

    // Fetch
    window.fetch = function(input, init) {
      if (typeof input === 'string') {
        input = fixUrl(input);
      }
      return originalFetch.call(this, input, init);
    };

    // createElement para scripts dinámicos
    document.createElement = function(tagName) {
      const element = originalCreateElement.call(this, tagName);

      if (tagName.toLowerCase() === 'script') {
        const originalSetter = Object.getOwnPropertyDescriptor(HTMLScriptElement.prototype, 'src')?.set;
        if (originalSetter) {
          Object.defineProperty(element, 'src', {
            set: function(value) {
              const fixed = fixUrl(value);
              console.log(" Script src fijado:", value, "→", fixed);
              originalSetter.call(this, fixed);
            },
            get: function() { return this.getAttribute('src'); }
          });
        }
      }

      return element;
    };

    // ✅ MONKEYPATCHING window.location
    const realLocation = window.location;
    Object.defineProperty(window, 'location', {
      value: new Proxy(realLocation, {
        get(target, prop) {
          if (prop === 'pathname') {
            // Devolver ruta falsa que haga que construyan la URL correcta
            return "/{$curso->folder_path}/{$curso->launch_file}";
          }
          return target[prop];
        }
      }),
      configurable: true
    });

    //  SCORM API
    let initialized = false;
    let terminated = false;

    const scormData = {
      "cmi.core.lesson_status": "not attempted",
      "cmi.core.score.raw": "0",
      "cmi.suspend_data": ""
    };

    window.API = {
      LMSInitialize: () => {
        console.log("[SCORM] LMSInitialize");
        initialized = true;
        return "true";
      },
      LMSGetValue: (el) => {
        console.log("[SCORM] LMSGetValue", el);
        return initialized ? (scormData[el] || "") : "";
      },
      LMSSetValue: (el, val) => {
        console.log("[SCORM] LMSSetValue", el, val);
        if (!initialized || terminated) return "false";
        scormData[el] = val;

        originalFetch("/api/scorm/track", {
          method: "POST",
          credentials: "include",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            course_id: {$curso->id},
            element: el,
            value: val
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
        terminated = true;
        return "true";
      },
      LMSGetLastError: () => "0",
      LMSGetErrorString: () => "No error",
      LMSGetDiagnostic: () => ""
    };
  })();
</script>
HTML;

        // Inyectar en el HTML
        $html = preg_replace('/<head[^>]*>/i', '$0' . "\n" . $apiInjection, $html);

        //  REESCRIBIR DIRECTAMENTE EN EL HTML cualquier referencia problemática
        $html = preg_replace('/(src|href)=["\']\/(html5\/[^"\']*)["\']/', '$1="' . $base . '/$2"', $html);
        $html = preg_replace('/(src|href)=["\']([^http][^"\']*)["\']/', '$1="' . $base . '/$2"', $html);

        return response($html)
            ->header('Content-Type', 'text/html');
    }
}

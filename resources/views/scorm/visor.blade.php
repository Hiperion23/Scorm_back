<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Visor SCORM</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      background-color: #000;
    }
    iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
  </style>
  <script>
    window.API = {
      LMSInitialize: function () {
        console.log('[SCORM API] LMSInitialize');
        return 'true';
      },
      LMSGetValue: function (element) {
        console.log('[SCORM API] LMSGetValue', element);
        const data = {
          "cmi.core.student_name": "Ramírez, Hamilton",
          "cmi.core.student_id": "hamilton@daktico.com",
          "cmi.core.lesson_status": "incomplete",
          "cmi.core.score.raw": "0"
        };
        return data[element] || "";
      },
      LMSSetValue: function (element, value) {
        console.log('[SCORM API] LMSSetValue', element, value);
        return 'true';
      },
      LMSCommit: function () {
        console.log('[SCORM API] LMSCommit');
        return 'true';
      },
      LMSFinish: function () {
        console.log('[SCORM API] LMSFinish');
        return 'true';
      },
      LMSGetLastError: function () {
        return '0';
      },
      LMSGetErrorString: function () {
        return 'No error';
      },
      LMSGetDiagnostic: function () {
        return '';
      }
    };
  </script>
</head>
<body>
  <iframe src="{{ $launchUrl }}" allowfullscreen></iframe>
</body>
</html>

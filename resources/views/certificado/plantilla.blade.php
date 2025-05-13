<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        @font-face {
            font-family: 'greycliffcfbold';
            src: url('{{ storage_path('fonts/Montserrat-BlackItalic.ttf') }}') format('truetype');
            font-weight: bold;
        }

        body {
            font-family: 'greycliffcfbold', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('{{ public_path("images/certificado-fondo.png") }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            width: 100%;
            height: 100vh;
            color: white;
        }

        .contenido {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
        }

        .nombre {
            position: absolute;
            top: 337px;
            width: 100%;
            font-size: 50px;
            font-weight: bold;
            color: #A2FFB5;
            opacity: 0.75;
        }

        .curso {
            position: absolute;
            top: 455px;
            left: 50%;
            transform: translateX(-50%);
            max-width: 700px;
            font-size: 40px;
            font-weight: bold;
            line-height: 1.4;
            word-wrap: break-word;
            opacity: 0.65;
        }

        .fecha {
            position: absolute;
            top: 579px;
            left: 64.8%;
            font-size: 23px;
            font-weight: bold;
            color: #A2FFB5;
            opacity: 0.75;
        }
    </style>
</head>
<body>
    <div class="contenido">
        <div class="nombre">{{ $nombre }}</div>
        <div class="curso">{{ $curso }}</div>
        <div class="fecha">{{ $fecha }}</div>
    </div>
</body>
</html>

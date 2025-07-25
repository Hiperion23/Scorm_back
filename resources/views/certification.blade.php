<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Certificado</title>
  <style>
    @page {
      margin: 0;
    }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #ffffff;
      color: #000;
      position: relative;
    }
    .ondas {
      position: absolute;
      left: 0;
      top: 60px;
      width: 240px;
      height: auto;
    }
    .contenido {
      position: relative;
      padding: 40px 60px;
      z-index: 10;
    }
    .encabezado {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .logo {
      height: 60px;
    }
    .titulo-superior {
      font-size: 14px;
      color: #1a2d6a;
      font-weight: 600;
      margin-top: 8px;
    }
    .bloque-azul {
      margin-top: 20px;
      background-color: #1a2d6a;
      color: #fff;
      padding: 14px;
      font-size: 20px;
      font-weight: bold;
      text-align: center;
    }
    .empresa {
      text-align: center;
      color: #1a2d6a;
      font-size: 18px;
      font-weight: bold;
      margin-top: 10px;
    }
    .descripcion {
      margin-top: 30px;
      font-size: 14px;
      text-align: center;
      line-height: 1.6;
    }
    .pie {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 50px;
    }
    .firma {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
    .firma img {
      height: 45px;
      margin-bottom: 5px;
    }
    .nombre-firma {
      font-size: 14px;
      font-weight: bold;
      color: #1a2d6a;
    }
    .cargo-firma {
      font-size: 12px;
      color: #4285F4;
    }
    .logo-tasa {
      height: 50px;
    }
  </style>
</head>
<body>
  <img src="{{ public_path('images/ondas.png') }}" class="ondas" alt="ondas">
  <div class="contenido">
    <div class="encabezado">
      <img src="{{ public_path('images/crecemos-juntos.png') }}" class="logo" alt="Logo Crecemos Juntos">
      <div class="titulo-superior">CONSTANCIA DE PARTICIPACIÓN</div>
    </div>

    <div class="bloque-azul">
      PROYECTO "CRECEMOS JUNTOS"
    </div>

    <div class="empresa">
      {{ $empresa }}
    </div>

    <div class="descripcion">
      Por haber concluido exitosamente las capacitaciones dirigidas a proveedores estratégicos del proyecto “Crecemos Juntos”.<br>
      Efectuado el {{ $fecha }}.
    </div>

    <div class="pie">
      <div class="firma">
        <img src="{{ public_path('images/firma.png') }}" alt="Firma">
        <div class="nombre-firma">Gabriela Méndez Giraldo</div>
        <div class="cargo-firma">SUBGERENTE DE COMPRAS</div>
      </div>
      <img src="{{ public_path('images/logo-tasa.png') }}" class="logo-tasa" alt="Logo Tasa">
    </div>
  </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subir SCORM</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Space Grotesk', sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(to bottom right, #1e293b, #312e81);
      color: #f3f4f6;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .container {
      width: 100%;
      max-width: 600px;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 2rem;
      text-align: center;
      background: linear-gradient(to right, #60a5fa, #c084fc);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
      position: relative;
      letter-spacing: 0.05em;
    }

    h1::after {
      content: '';
      position: absolute;
      bottom: -3px;
      left: 0;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, #60a5fa, #c084fc, #60a5fa);
      background-size: 200% 100%;
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    form {
      background-color: rgba(30, 41, 59, 0.8);
      backdrop-filter: blur(10px);
      border-radius: 1rem;
      padding: 2rem;
      border: 2px solid rgba(96, 165, 250, 0.3);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2), 0 0 15px rgba(96, 165, 250, 0.2);
      transition: all 0.3s ease;
    }

    form:hover {
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3), 0 0 20px rgba(96, 165, 250, 0.3);
      border-color: rgba(96, 165, 250, 0.5);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-size: 1rem;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #a5b4fc;
    }

    input[type="text"] {
      width: 100%;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      border: 1px solid #4b5563;
      background-color: rgba(30, 41, 59, 0.7);
      color: white;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    input[type="text"]:focus {
      outline: none;
      border-color: #8b5cf6;
      box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.3);
    }

    .file-input-container {
      position: relative;
      overflow: hidden;
      display: inline-block;
      width: 100%;
    }

    .file-input-label {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 0.75rem 1rem;
      background: linear-gradient(to right, rgba(96, 165, 250, 0.1), rgba(139, 92, 246, 0.1));
      color: white;
      border: 1px dashed #60a5fa;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .file-input-label:hover {
      background: linear-gradient(to right, rgba(96, 165, 250, 0.2), rgba(139, 92, 246, 0.2));
      border-color: #c084fc;
    }

    .file-input-icon {
      margin-right: 0.5rem;
      font-size: 1.2rem;
    }

    input[type="file"] {
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }

    .file-name {
      margin-top: 0.5rem;
      font-size: 0.875rem;
      color: #a5b4fc;
      text-align: center;
      min-height: 1.5rem;
    }

    button[type="submit"] {
      display: block;
      width: 100%;
      padding: 0.875rem;
      margin-top: 1rem;
      background: linear-gradient(to right, #7c3aed, #4f46e5);
      color: white;
      border: none;
      border-radius: 0.5rem;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    button[type="submit"]::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: all 0.6s ease;
    }

    button[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
    }

    button[type="submit"]:hover::before {
      left: 100%;
    }

    button[type="submit"]:active {
      transform: translateY(0);
    }

    .error-message {
      color: #f87171;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }

    .required::after {
      content: '*';
      color: #f87171;
      margin-left: 0.25rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1> Subir ZIP </h1>

    <form method="POST" action="{{ url('/scorm/upload') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label for="nombre" class="required">Nombre del ZIP</label>
        <input type="text" id="nombre" name="nombre" required placeholder="Ingrese el nombre del curso">
      </div>

      <div class="form-group">
        <label for="archivo" class="required">Archivo (.zip)</label>
        <div class="file-input-container">
          <label class="file-input-label">
            <span class="file-input-icon">📎</span>
            <span class="file-input-text">Seleccionar archivo</span>
            <input type="file" id="archivo" name="archivo" accept=".zip" required>
          </label>
        </div>
        <div class="file-name" id="file-name"></div>
      </div>

      <button type="submit">
        Subir curso
      </button>
    </form>
  </div>

  <script>
    document.getElementById('archivo').addEventListener('change', function(e) {
      const fileName = e.target.files[0] ? e.target.files[0].name : '';
      document.getElementById('file-name').textContent = fileName;
    });
  </script>
</body>
</html>

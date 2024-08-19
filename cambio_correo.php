<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitar Código</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      width: 100%;
      max-width: 1200px;
    }

    .card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 300px;
      padding: 20px;
      box-sizing: border-box;
    }

    .card-header {
      padding-bottom: 16px;
    }

    .card-title {
      font-size: 1.5rem;
      margin: 0;
    }

    .card-description {
      font-size: 0.875rem;
      color: #666;
    }

    .card-content {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    label {
      font-weight: bold;
    }

    input {
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 8px;
      width: 100%;
      box-sizing: border-box;
    }

    input:focus {
      border-color: #000000;
      outline: none;
    }

    .btn {
      background-color: #000000;
      color: #fff;
      border: none;
      border-radius: 4px;
      padding: 12px;
      font-size: 1rem;
      cursor: pointer;
      width: 100%;
    }

    .btn:hover {
      background-color: #7d7a7a;
    }

    .card-footer {
      margin-top: 10px;
      padding-top: 16px;
    }

    .error-message {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Formulario para solicitar el código de verificación -->
    <div class="card">
      <div class="card-header">
        <center>
          <h2 class="card-title">Solicitud de Código</h2>
          <p class="card-description">Ingresa tu email para recibir un código de verificación.</p>
        </center>
      </div>
      <div class="card-content">
        <form action="enviar_codigo.php" method="post">
          <div class="form-group">
            <label for="email">Correo</label>
            <input id="email" name="email" type="email" placeholder="correo@example.com" required>
            <br>
          </div>
          <button type="submit" class="btn">Enviar código de verificación</button>
          <?php if (isset($_GET['status']) && $_GET['status'] == 'enviado'): ?>
            <p style="color: green; font-weight: bold; margin-top: 10px;">El correo con el código de verificación ha sido enviado.</p>
          <?php endif; ?>
          <?php if (isset($_GET['error']) && $_GET['error'] == 'email_no_encontrado'): ?>
            <center>
              <p class="error-message">El correo ingresado no pertenece a ninguna cuenta. Intenta con otro.</p>
            </center>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

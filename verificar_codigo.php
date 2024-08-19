<?php
session_start();

// Obtener el mensaje desde la sesión
$messageType = isset($_SESSION['messageType']) ? $_SESSION['messageType'] : '';
$messageText = isset($_SESSION['messageText']) ? $_SESSION['messageText'] : '';

// Limpiar los mensajes de la sesión después de mostrarlos
unset($_SESSION['messageType']);
unset($_SESSION['messageText']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
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
      margin-top: 10px;
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

    .message {
      font-size: 0.875rem;
      margin-top: 10px;
      padding: 8px;
      border-radius: 4px;
      text-align: center;
    }

    .message.success {
      margin-bottom: 10px;
      color: green;
      background-color: #e0ffe0;
    }

    .message.error {
      color: red;
      background-color: #ffe0e0;
    }
  </style>
</head>
<body>
    <!-- Formulario para verificar el código y cambiar la contraseña -->
    <div class="card">
      <div class="card-header">
        <center>
          <h2 class="card-title">Cambiar Contraseña</h2>
          <p class="card-description">Ingresa el código de verificación y tu nueva contraseña.</p>
        </center>
      </div>
      <div class="card-content">
        <form action="cambio_contraseña.php" method="post" id="changePasswordForm">
          <input type="hidden" name="email" id="hiddenEmail" value="">
          <div class="form-group">
            <label for="verificationCode">Código de verificación</label>
            <input id="verificationCode" name="verificationCode" type="number" placeholder="Código de verificación" required>
          </div>
          <div class="form-group">
            <label for="newPassword">Contraseña Nueva</label>
            <input id="newPassword" name="newPassword" type="password" required>
          </div>
          <div class="form-group">
            <label for="confirmPassword">Confirmar Contraseña</label>
            <input id="confirmPassword" name="confirmPassword" type="password" required>
            <span id="passwordMatchMessage" class="message"></span>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn">Cambiar Contraseña</button>
          </div>
        </form>
        <span id="verificationMessage" class="message"></span>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('changePasswordForm');
        const password = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const passwordMatchMessage = document.getElementById('passwordMatchMessage');
        const verificationMessage = document.getElementById('verificationMessage');
        const hiddenEmail = document.getElementById('hiddenEmail');

        // Actualiza el valor del campo oculto 'email' con el valor de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const email = urlParams.get('email');
        if (email) {
            hiddenEmail.value = email;
        }

        // Verifica si hay un mensaje de error o éxito pasado desde PHP
        const messageType = urlParams.get('messageType');
        const messageText = urlParams.get('messageText');

        console.log('Message Type:', messageType);
        console.log('Message Text:', messageText);

        if (verificationMessage && messageType && messageText) {
            if (messageType === 'error') {
                verificationMessage.textContent = messageText;
                verificationMessage.className = 'message error';
            } else if (messageType === 'success') {
                passwordMatchMessage.textContent = messageText;
                passwordMatchMessage.className = 'message success';
                setTimeout(() => {
                    window.location.href = 'index.php'; // Cambia a la página deseada
                }, 3000); // Redirige después de 3 segundos
            }
        }

        // Validar las contraseñas antes de enviar el formulario
        form.addEventListener('submit', function (event) {
            if (password.value !== confirmPassword.value) {
                event.preventDefault(); // Evita el envío del formulario
                passwordMatchMessage.textContent = 'Las contraseñas no coinciden.';
                passwordMatchMessage.className = 'message error';
            } else {
                passwordMatchMessage.textContent = '';
            }
        });
    });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <meta name="google-signin-client_id"
        content="430057594671-0n66vr24ra65frk4qmso1tg0eipmbvoj.apps.googleusercontent.com">
        <script>
        function handleCredentialResponse(response) {
            const token = response.credential;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'login-google.php'); // Enviar al archivo PHP
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const result = JSON.parse(xhr.responseText);
                    if (result.success) {
                        // Redirigir a index.php con el ID de usuario
                        window.location.href = 'index.php?google_id=' + encodeURIComponent(result.google_id);
                    } else {
                        alert(result.message); // Mostrar mensaje de error
                    }
                } else {
                    console.error('Error en la solicitud AJAX');
                }
            };
            xhr.send('id_token=' + encodeURIComponent(token));
        }
    </script>
    <style>
        .error-container {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .card {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            padding: 1rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .card-description {
            margin-top: 0.5rem;
            color: #6b7280;
        }

        .card-content {
            padding: 1rem;
        }

        .button-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .button {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border: 2px solid #d1d5db;
            border-radius: 0.375rem;
            background: #ffffff;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }

        .button:hover {
            background: #f3f4f6;
        }

        .button.outline {
            border-color: #9ca3af;
            color: #374151;
        }

        .icon {
            margin-right: 0.5rem;
            height: 1.25rem;
            width: 1.25rem;
            stroke: currentColor;
        }
    </style>
</head>

<body>
    <section class="form-login">
        <h1>Inicio Sesión</h1>
        <form class="form" method="POST" action="login-nomal.php" id="login-form">
            <div class="flex-column">
                <label>Correo electrónico</label>
            </div>
            <div class="inputForm">
                <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg">
                    <g id="Layer_3" data-name="Layer 3">
                        <path
                            d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z">
                        </path>
                    </g>
                </svg>
                <input type="email" name="email" id="email" class="input" placeholder="Ingrese su Correo" required>
            </div>

            <div class="flex-column">
                <label>Contraseña</label>
            </div>
            <div class="inputForm">
                <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0">
                    </path>
                    <path
                        d="m304 224c-8.832031 0-16-7.167969-16-16v-80c0-52.929688-43.070312-96-96-96s-96 43.070312-96 96v80c0 8.832031-7.167969 16-16 16s-16-7.167969-16-16v-80c0-70.59375 57.40625-128 128-128s128 57.40625 128 128v80c0 8.832031-7.167969 16-16 16zm0 0">
                    </path>
                </svg>
                <input type="password" id="password" name="password" class="input" placeholder="Ingrese su Contraseña"
                    required>
            </div>

            <div id="error-container" class="error-container">
                <?php
                // Mostrar errores PHP aquí si es necesario
                if (isset($_GET['error'])) {
                    echo htmlspecialchars($_GET['error']);
                }
                ?>
            </div>

            <div class="flex-row">
                <a href="cambio_correo.php" class="span">¿Olvidó la contraseña?</a>
            </div>
            <button type="submit" name="submit" id="submit" class="button-submit">Iniciar Sesión</button>
            <p class="p">¿No tienes una cuenta?<a class="span" href="register.php">Regístrate</a></p>
            <p class="p line">Or With</p>
            <center>
                <div id="g_id_onload"
                    data-client_id="430057594671-0n66vr24ra65frk4qmso1tg0eipmbvoj.apps.googleusercontent.com"
                    data-callback="handleCredentialResponse" data-auto_prompt="false"></div>
                <div class="g_id_signin" data-type="standard"></div>
            </center>
        </form>
    </section>
</body>

</html>

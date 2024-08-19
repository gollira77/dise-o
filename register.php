<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta name="google-signin-client_id"
        content="430057594671-0n66vr24ra65frk4qmso1tg0eipmbvoj.apps.googleusercontent.com">
        <script>
        function handleCredentialResponse(response) {
            const token = response.credential;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'register-google.php'); // Enviar al archivo PHP
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

        // Función para cargar el JSON de países y llenar el <select>
        function loadCountryCodes() {
            fetch('countries.json')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('country_code');
                    data.forEach(country => {
                        const option = document.createElement('option');
                        option.value = country.code;
                        option.textContent = `${country.name} +${country.code}`;
                        select.appendChild(option);
                    });
                    // Establecer el valor predeterminado
                    select.value = '54'; // Código de país por defecto para Argentina
                })
                .catch(error => console.error('Error al cargar los códigos de país:', error));
        }

        // Ejecutar la función al cargar la página
        window.onload = loadCountryCodes;
    </script>
    <style>
        h1 {
            margin-top: 10px;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
        }

        select {
            padding: 10px;
            border-radius: 4px;
            margin: 0;
            border: 1px solid #ffffff;
            font-size: 16px;
        }

        .input {
            padding: 10px;
            border-radius: 4px;
            font-size: 16px;
            flex: 1;
        }

        .icon-wrapper {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .icon-wrapper svg {
            width: 25px;
            height: 25px;
        }

        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin-bottom: 0;
        }

        .error-message {
            color: red;
            margin: 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <section class="form-login">
        <h1>Crear Cuenta</h1>
        <form class="form" action="send_verification_email.php" method="POST">
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'email_exists'): ?>
                <p class="error-message">El correo electrónico ya está registrado. Por favor, utiliza otro correo.</p>
            <?php endif; ?>
        <?php endif; ?>
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
                <input type="email" name="user_email" id="user_email" class="input" placeholder="Ingrese su Correo"
                    required>
            </div>

            <div class="flex-column">
                <label>Nombre de Usuario</label>
            </div>
            <div class="inputForm">
                <div class="icon-wrapper">
                    <svg height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.31 0-6 2.69-6 6v1h12v-1c0-3.31-2.69-6-6-6z">
                        </path>
                    </svg>
                    <input type="text" name="username" id="username" class="input"
                        placeholder="Ingrese su Nombre de Usuario" required>
                </div>
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
                <input type="password" id="user_password" name="user_password" class="input"
                    placeholder="Ingrese su Contraseña" required>
            </div>

            <div class="flex-column">
                <label>Número de Teléfono</label>
            </div>
            <div class="inputForm">
                <div class="input-wrapper">
                    <select id="country_code" name="country_code" class="input_select">
                    </select>
                    <input type="text" id="phone_number" name="phone_number" class="input"
                        placeholder="Ingrese su numero" required>
                </div>
            </div>

            <div class="g-recaptcha" data-sitekey="6LczCycqAAAAAEYYSNOdM25Z9l3yooWbN8_ZslBN"></div>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'recaptcha'): ?>
                <div class="error-message">La validación del reCAPTCHA falló. Por favor, inténtalo de nuevo.</div>
            <?php endif; ?>

            <button type="submit" name="submit1" id="submit1" class="button-submit">Registrate</button>
            <p class="p">¿No tienes una cuenta?<a class="span" href="login.php">Inicie Sesion</a></p>
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
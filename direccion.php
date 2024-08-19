<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dirección de Envío</title>
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

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 400px;
            align-content: center;
            border: 1px solid #ddd;
        }

        .card-header {
            padding: 16px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #000;
            margin: 0;
            margin-top: 10px
        }

        .card-description {
            color: #666;
            margin: 8px 0 0;
        }

        .card-content {
            padding: 16px;
        }

        .form {
            display: grid;
            gap: 16px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .label {
            font-weight: bold;
            color: #000;
        }

        .input {
            width: 70%;
            padding: 8px;
            border: 1px solid #000;
            border-radius: 4px;
            background-color: #fff;
            color: #000;
        }

        .button {
            width: 100%;
            padding: 12px;
            border: 1px solid #000;
            border-radius: 10px;
            background-color: #000;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }

        .button:hover {
            background-color: #333;
        }

        .card-footer {
            padding: 30px;
        }

        .message {
            text-align: center;
            padding: 16px;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="card">
        <?php
        if (isset($_GET['mensaje'])) {
            $mensaje = $_GET['mensaje'];
            if ($mensaje == 'direccion_guardada') {
                echo '<div class="message success">Dirección guardada para el envío.</div>';
            } elseif ($mensaje == 'error') {
                echo '<div class="message error">Ocurrió un error al guardar la dirección.</div>';
            }
        }
        ?>
        <form method="post" action="insert_direccion.php">
            <div class="card-header">
                <h2 class="card-title">Dirección de Envío</h2>
                <p class="card-description">Ingresa los detalles de tu dirección de envío.</p>
            </div>
            <div class="card-content">
                <div class="form">
                    <div class="form-group">
                        <label for="street" class="label">Calle</label>
                        <input id="street" name="street" class="input" placeholder="123 Main St" type="text" required>
                    </div>
                    <div class="form-group-group">
                        <div class="form-group">
                            <label for="city" class="label">Ciudad</label>
                            <input id="city" name="city" class="input" placeholder="San Francisco" type="text" required>
                        </div>
                        <div class="form-group">
                            <label for="state" class="label">Provincia</label>
                            <input id="state" name="state" class="input" placeholder="CA" type="text" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="zip" class="label">Código Postal</label>
                        <input id="zip" name="zip" class="input" placeholder="94101" type="text" required>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="button" type="submit">Enviar</button>
            </div>
        </form>
    </div>
</body>

</html>
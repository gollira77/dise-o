<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }

        header {
            background: #007bff;
            color: white;
            padding: 15px 20px; /* Ajuste del relleno para reducir espacio */
            text-align: center;
            border-bottom: 2px solid #0056b3; /* Línea inferior para mayor definición */
        }

        .container {
            margin: 20px auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10px; /* Ajuste del margen superior para reducir espacio */
        }

        .container h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .container a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .container a:hover {
            text-decoration: underline;
        }

        .container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .container input[type="text"], .container input[type="file"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .container input[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .container .return-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            font-weight: bold;
        }

        .container .return-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <h1>Panel de Control</h1>
    </header>

    <div class="container">
        <?php
        require("conexion.php");

        // Verificar si 'table' está definido en la URL y no está vacío
        $table = $_GET['table'] ?? '';

        if (!empty($table)) {
            echo "<div class='form-container'>";
            echo "<h2>Agregar nuevo registro a " . ucfirst($table) . "</h2>";
            
            // Preparar y ejecutar la consulta para obtener la estructura de la tabla
            $result = $conn->query("DESCRIBE $table");

            if ($result && $result->num_rows > 0) {
                echo "<form method='post' action='guardar.php?action=insert&table=$table' enctype='multipart/form-data'>";
                
                while ($row = $result->fetch_assoc()) {
                    if ($row['Field'] != 'id') {
                        if ($row['Field'] == 'imagen') {
                            echo "<label>" . ucfirst($row['Field']) . ": </label>";
                            echo "<input type='file' name='" . $row['Field'] . "'><br>";
                        } else {
                            echo "<label>" . ucfirst($row['Field']) . ": </label>";
                            echo "<input type='text' name='" . $row['Field'] . "' required><br>";
                        }
                    }
                }
                echo "<input type='submit' value='Agregar'>";
                echo "</form>";
            } else {
                echo "<p>Error al describir la tabla o tabla no encontrada.</p>";
            }
            echo "</div>";
        } else {
            echo "<p>No se especificó una tabla válida.</p>";
        }

        $conn->close();
        ?>

        <a class="return-link" href="abml.php?table=<?php echo htmlspecialchars($table); ?>">Volver</a>
    </div>
</body>
</html>

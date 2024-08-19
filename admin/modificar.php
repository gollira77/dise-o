<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Registro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            background-color: #f5f7fa;
        }

        .container {
            margin: 20px;
            padding: 20px;
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="file"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .image-preview {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .image-preview img {
            max-width: 100%;
            border-radius: 5px;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            padding: 10px 20px;
            border: 2px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .back-button:hover {
            background-color: #007bff;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container">

<?php
require("conexion.php");

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

echo "<h1>Modificar registro en " . ucfirst($table) . "</h1>";

$result = $conn->query("SELECT * FROM $table WHERE id=$id");
$row = $result->fetch_assoc();

echo "<form method='post' action='guardar.php?action=update&table=$table&id=$id' enctype='multipart/form-data'>";
$result = $conn->query("DESCRIBE $table");

while ($field = $result->fetch_assoc()) {
    if ($field['Field'] != 'id') {
        if ($field['Field'] == 'imagen') {
            echo "<label>" . ucfirst($field['Field']) . ": </label>";
            echo "<input type='file' name='" . $field['Field'] . "'><br>";
            if (!empty($row[$field['Field']])) {
                echo "<div class='image-preview'><img src='data:image/jpeg;base64," . base64_encode($row[$field['Field']]) . "' alt='Imagen'></div>";
            }
        } else {
            echo "<label>" . ucfirst($field['Field']) . ": </label>";
            echo "<input type='text' name='" . $field['Field'] . "' value='" . htmlspecialchars($row[$field['Field']]) . "' required><br>";
        }
    }
}

echo "<input type='submit' value='Guardar'>";
echo "</form>";

$conn->close();
?>

<a href="abml.php?table=<?php echo htmlspecialchars($table); ?>" class="back-button">Volver</a>

</div>

</body>
</html>

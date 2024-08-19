<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABML</title>
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
            max-width: 1200px;
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

        .top-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .top-buttons a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            padding: 10px 20px;
            border: 2px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .top-buttons a:hover {
            background-color: #007bff;
            color: #ffffff;
        }

        .container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        .container table, .container th, .container td {
            border: 1px solid #ddd;
        }

        .container th, .container td {
            padding: 16px;
            text-align: left;
        }

        .container th {
            background-color: #f8f9fa;
            color: #333;
        }

        .container tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .container tr:hover {
            background-color: #e9ecef;
        }

        .container img {
            border-radius: 4px;
            max-width: 100%;
        }

        .container .actions a {
            display: inline-block;
            width: 32px;
            height: 32px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .container .actions a img {
            width: 32px;
            height: 32px;
            vertical-align: middle;
        }

        .container .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">

<?php
require("conexion.php");

$table = $_GET['table'] ?? '';

// Mostrar el título de la tabla
echo "<h1>Gestión de " . ucfirst($table) . "</h1>";

// Contenedor para los botones de agregar y volver
echo "<div class='top-buttons'>";
echo "<a href='insertar.php?table=$table' class='add-link'>Agregar nuevo registro</a>";
echo "<a href='principal.php' class='return-link'>Volver</a>";
echo "</div>";

// Mostrar listado de registros
if ($table) {
    echo "<h2>Listado de registros en " . ucfirst($table) . "</h2>";

    echo "<div class='table-container'>";
    
    $result = $conn->query("SELECT * FROM $table");

    if ($result->num_rows > 0) {
        echo "<table><tr>";
        $fields = $result->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . ucfirst($field->name) . "</th>";
        }
        echo "<th>Acciones</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $data) {
                if ($key == 'imagen' && !empty($data)) {
                    $imageData = base64_encode($data);
                    echo "<td><img src='data:image/jpeg;base64,$imageData' alt='Imagen' width='100'></td>";
                } else {
                    echo "<td>" . htmlspecialchars($data) . "</td>";
                }
            }
            echo "<td class='actions'>
                    <a class='modify' href='modificar.php?table=$table&id=" . $row['id'] . "'><img src='https://cdn-icons-png.flaticon.com/128/10008/10008576.png' alt='Modificar'></a> | 
                    <a class='delete' href='guardar.php?action=delete&table=$table&id=" . $row['id'] . "' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\")'><img src='https://cdn-icons-png.flaticon.com/128/8396/8396418.png' alt='Eliminar'></a>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron datos.";
    }

    echo "</div>"; // Cierra .table-container
} else {
    echo "Por favor, selecciona una tabla.";
}

$conn->close();
?>

</div>

</body>
</html>

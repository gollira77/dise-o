
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Registros</title>
</head>
<body>

<?php
require("conexion.php");

$table = $_GET['table'] ?? '';

echo "<h1>Buscar registros en " . ucfirst($table) . "</h1>";
$query = "SELECT * FROM $table";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'><tr>";
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
        echo "<td><a href='modificar.php?table=$table&id=" . $row['id'] . "'>Modificar</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron datos.";
}

$conn->close();
?>

<a href="abml.php">volver</a>

</body>
</html>

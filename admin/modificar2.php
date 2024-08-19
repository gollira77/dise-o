<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Registro</title>
    <link rel="stylesheet" href="css/modif.css">
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

<a href="principal.php" class="back-button">Volver</a>

</div>

</body>
</html>

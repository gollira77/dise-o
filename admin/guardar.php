<?php
require("conexion.php");

$action = $_GET['action'] ?? '';
$table = $_GET['table'] ?? '';
$id = $_GET['id_account'] ?? '';

if ($action == 'insert') {
    $columns = [];
    $values = [];
    foreach ($_POST as $key => $value) {
        $columns[] = $key;
        $values[] = "'" . $conn->real_escape_string($value) . "'";
    }
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['imagen']['tmp_name']);
        $columns[] = 'imagen';
        $values[] = "'" . $conn->real_escape_string($imageData) . "'";
    }

    $columns_str = implode(", ", $columns);
    $values_str = implode(", ", $values);
    $sql = "INSERT INTO $table ($columns_str) VALUES ($values_str)";
    
    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro insertado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'update' && $id) {
    $set_str = "";
    foreach ($_POST as $key => $value) {
        $set_str .= $key . "='" . $conn->real_escape_string($value) . "', ";
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['imagen']['tmp_name']);
        $set_str .= "imagen='" . $conn->real_escape_string($imageData) . "', ";
    }
    
    $set_str = rtrim($set_str, ", ");
    $sql = "UPDATE $table SET $set_str WHERE id_account=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registro actualizado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'delete' && $id) {
    $sql = "DELETE FROM $table WHERE id_account=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registro eliminado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<a href="abml.php">volver</a>

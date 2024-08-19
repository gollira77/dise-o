<?php
// Datos de conexión a la base de datos
$host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor
$username = 'root'; // Reemplaza con tu nombre de usuario de la base de datos
$password = ''; // Reemplaza con tu contraseña de la base de datos
$dbname = 'tiendaonline'; // Reemplaza con el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres
$conn->set_charset("utf8");

?>

<?php
// Configuración de la conexión
$servername = "localhost";
$username = "root"; // Tu nombre de usuario de phpMyAdmin
$password = ""; // Tu contraseña de phpMyAdmin (vacío si no tienes contraseña)
$dbname = "tiendaonline";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
else{
    
}
?>

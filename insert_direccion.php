<?php
// Datos de conexión
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'tiendaonline';

// Crear conexión
$conexion = mysqli_connect($host, $user, $password, $database);

// Verificar conexión
if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $street = $_POST['street']; // Este campo corresponde a 'direccion'
    $city = $_POST['city']; // Este campo corresponde a 'ciudad'
    $state = $_POST['state']; // Este campo corresponde a 'provincia'
    $zip = $_POST['zip']; // Este campo corresponde a 'codigo_postal'

    // Consulta de inserción
    $sql = "INSERT INTO direcciones (direccion, ciudad, provincia, codigo_postal) 
            VALUES ('$street', '$city', '$state', '$zip')";
    
    if (mysqli_query($conexion, $sql)) {
        header('Location: pago/pago.php');
        exit();
    } else {
        header('Location: direccion.php?mensaje=error');
        exit();
    }
}

// Cerrar conexión
mysqli_close($conexion);
?>

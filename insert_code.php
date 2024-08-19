<?php

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'tiendaonline'); // Cambia los datos según tu configuración

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['email']) && isset($_POST['code'])) {
    $email = $_POST['email'];
    $code = $_POST['code'];

    // Verificar código en la base de datos
    $stmt = $conn->prepare("SELECT code FROM verification_codes WHERE email = ? AND code = ?");
    $stmt->bind_param("si", $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Eliminar el código de la base de datos después de la verificación
        $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ? AND code = ?");
        $stmt->bind_param("si", $email, $code);
        $stmt->execute();

        
        header("Location: login.php?email=" . urlencode($email));
        exit();
    } else {
        echo "Código de verificación incorrecto o expirado.";
    }

    $stmt->close();
} else {
    echo "Datos del formulario no están presentes.";
}

$conn->close();
?>

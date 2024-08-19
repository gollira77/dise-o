<?php
session_start();

// Función para validar usuario y contraseña
function validate_user($email, $password) {
    // Conectar a la base de datos
    $db = new mysqli('localhost', 'root', '', 'tiendaonline');
    
    if ($db->connect_error) {
        return ['success' => false, 'errors' => ['Error de conexión a la base de datos: ' . $db->connect_error]];
    }
    
    // Preparar y ejecutar la consulta
    $stmt = $db->prepare('SELECT password FROM account_data WHERE email = ?');
    
    if (!$stmt) {
        return ['success' => false, 'errors' => ['Error al preparar la consulta: ' . $db->error]];
    }
    
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        return ['success' => false, 'errors' => ['Correo electrónico no encontrado.']];
    }
    
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();
    $db->close();
    
    // Comparar la contraseña sin cifrar
    if ($password === $stored_password) {
        // Autenticación exitosa
        $_SESSION['email'] = $email;
        return ['success' => true, 'redirect' => 'index.php'];
    } else {
        // Contraseña incorrecta
        return ['success' => false, 'errors' => ['Contraseña incorrecta.']];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = validate_user($email, $password);
    
    if ($result['success']) {
        header('Location: ' . $result['redirect']);
        exit();
    } else {
        $error_message = implode('<br>', $result['errors']);
        header('Location: login.php?error=' . urlencode($error_message));
        exit();
    }
}
?>

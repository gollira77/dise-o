<?php
session_start();

// Función para verificar el token de ID con la API de Google
function verifyGoogleIdToken($idToken) {
    $clientId = '430057594671-0n66vr24ra65frk4qmso1tg0eipmbvoj.apps.googleusercontent.com';
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token={$idToken}";

    // Obtener la respuesta de la API de Google
    $response = @file_get_contents($url);

    if ($response === FALSE) {
        error_log('Error al conectar con la API de Google.');
        return false; // No se pudo conectar con la API de Google
    }

    // Decodificar la respuesta JSON
    $data = json_decode($response, true);

    // Verificar el ID Token
    if (isset($data['aud']) && $data['aud'] === $clientId && isset($data['sub'])) {
        return $data; // El token es válido
    }

    error_log('Token inválido o datos incompletos.');
    return false; // Token inválido
}

// Obtener el token del ID Token enviado
$idToken = $_POST['id_token'] ?? null;

if (!$idToken) {
    echo json_encode(['success' => false, 'message' => 'Token no proporcionado']);
    exit;
}

$payload = verifyGoogleIdToken($idToken);

if ($payload) {
    $googleId = $payload['sub']; // ID del usuario de Google
    $email = $payload['email'];
    $name = $payload['name'];
    $picture = $payload['picture'];

    // Conectar a la base de datos
    $db = new mysqli('localhost', 'root', '', 'tiendaonline');
    if ($db->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit;
    }

    // Consultar si el usuario ya está registrado
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE google_id = ?");
    $stmt->bind_param("s", $googleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El usuario está registrado, iniciar sesión
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_picture'] = $picture;
        $_SESSION['google_id'] = $googleId;
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
    } else {
        // El usuario no está registrado
        echo json_encode(['success' => false, 'message' => 'Usuario no registrado']);
    }

    $stmt->close();
    $db->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Token inválido']);
}
?>

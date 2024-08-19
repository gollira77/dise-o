<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_token'])) {
    $id_token = $_POST['id_token'];
    $client_id = '430057594671-0n66vr24ra65frk4qmso1tg0eipmbvoj.apps.googleusercontent.com';
    
    // Verificar el ID Token con Google
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token;
    $response = file_get_contents($url);
    $user_info = json_decode($response, true);
    
    if (isset($user_info['aud']) && $user_info['aud'] === $client_id) {
        // Datos del usuario
        $email = $user_info['email'];
        $name = $user_info['name'];
        $picture = $user_info['picture'];
        $google_id = $user_info['sub']; // ID del usuario

        // Conectar a la base de datos
        $conn = new mysqli('localhost', 'root', '', 'tiendaonline');

        // Verificar conexión
        if ($conn->connect_error) {
            die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]));
        }

        // Verificar si el usuario ya está registrado
        $sql = "SELECT * FROM usuarios WHERE google_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $google_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Usuario ya registrado
            session_start();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_picture'] = $picture;
            $_SESSION['google_id'] = $google_id;
            echo json_encode(['success' => true]);
        } else {
            // Insertar nuevo usuario
            $sql = "INSERT INTO usuarios (nombre, email, foto, google_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $name, $email, $picture, $google_id);
            
            if ($stmt->execute()) {
                // Registro exitoso
                session_start();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_picture'] = $picture;
                $_SESSION['google_id'] = $google_id;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar usuario.']);
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Token no válido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se ha recibido un token.']);
}
?>

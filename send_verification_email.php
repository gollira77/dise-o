<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Configuración de PHPMailer
class clsMail {
    private $mail = null;
    
    function __construct() {
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->Port = 587;
        $this->mail->Username = "codeverificacion5@gmail.com";
        $this->mail->Password = "dptm tcxs megd ccsj";
    }

    public function metEnviar(string $titulo, string $nombre, string $correo, string $asunto, string $bodyHTML) {
        $this->mail->setFrom("codeverificacion5@gmail.com", $titulo);
        $this->mail->addAddress($correo, $nombre);
        $this->mail->Subject = $asunto;
        $this->mail->Body = $bodyHTML;
        $this->mail->isHTML(true);
        $this->mail->CharSet = "UTF-8";
        return $this->mail->send();
    }
}

// Verificar el token de reCAPTCHA
$recaptchaSecret = '6LczCycqAAAAAAlV89ZbrDr0bwE7-K6rTjkXdcsE'; // Reemplaza con tu clave secreta
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
$responseKeys = json_decode($response, true);

if (intval($responseKeys["success"]) !== 1) {
    // Redirige al formulario con un mensaje de error
    header("Location: register.php?error=recaptcha");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'tiendaonline'); 

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se han enviado datos del formulario
if (isset($_POST['user_email']) && isset($_POST['user_password'])) {
    $email = $_POST['user_email'];
    $username = $_POST['username'];
    $password = $_POST['user_password'];
    $country_code = $_POST['country_code'];
    $phone_number = $_POST['phone_number'];
    
    // Verificar si el correo electrónico ya está registrado
    $stmt = $conn->prepare("SELECT COUNT(*) FROM account_data WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count > 0) {
        // Redirige al formulario con un mensaje de error
        header("Location: register.php?error=email_exists");
        exit();
    }

    // Generar código de verificación
    $verificationCode = rand(100000, 999999); // Código de 6 dígitos
    
    // Eliminar cualquier código de verificación previo para el mismo correo
    $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    
    // Guardar datos en la tabla account_data
    $stmt = $conn->prepare("INSERT INTO account_data (email, username, password, country_code, phone_number, tipoUsuario) VALUES (?, ?, ?, ?, ?, 'cliente')");
    $stmt->bind_param("sssss", $email, $username, $password, $country_code, $phone_number);
    $stmt->execute();
    $stmt->close();

    // Guardar nuevo código en la base de datos
    $stmt = $conn->prepare("INSERT INTO verification_codes (email, code) VALUES (?, ?)");
    $stmt->bind_param("si", $email, $verificationCode);

    if ($stmt->execute()) {
        // Enviar correo
        $mailSend = new clsMail();
        $bodyHTML = "
            <div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2 style='font-size: 28px; color: #333;'>¡Hola!</h2>
                <p style='font-size: 20px; color: #555;'>Tu código de verificación es:</p>
                <p style='font-size: 24px; font-weight: bold; color: #007bff;'>$verificationCode</p>
                <p style='font-size: 16px; color: #555;'>Por favor, ingrésalo en el formulario de verificación para completar tu registro.</p>
            </div>
        ";
        $enviado = $mailSend->metEnviar("Codigo", "Usuario", $email, "Código de Verificación", $bodyHTML);

        if ($enviado) {
            // Redirigir a verify_code.php con el correo electrónico en la URL
            header("Location: verify_codigo.php?email=" . urlencode($email));
            exit();
        } else {
            echo "No se pudo enviar el correo de verificación.";
        }
    } else {
        echo "Error al guardar el código de verificación en la base de datos.";
    }

    $stmt->close();
} else {
    echo "Datos del formulario no están presentes.";
}

$conn->close();
?>

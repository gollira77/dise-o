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

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'tiendaonline');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se han enviado datos del formulario
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Consultar si el email existe en la tabla account_data
    $stmt = $conn->prepare("SELECT COUNT(*) FROM account_data WHERE email = ?");
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // El email existe en la base de datos, continuar con el envío del correo

        // Generar código de verificación
        $verificationCode = rand(100000, 999999); // Código de 6 dígitos

        // Eliminar cualquier código de verificación previo para el mismo correo
        $stmt = $conn->prepare("DELETE FROM verificar_cambio WHERE email = ?");
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // Guardar nuevo código en la base de datos
        $stmt = $conn->prepare("INSERT INTO verificar_cambio (email, codigo_cambio) VALUES (?, ?)");
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }
        $stmt->bind_param("si", $email, $verificationCode);

        if ($stmt->execute()) {
            // Enviar correo
            $mailSend = new clsMail();
            $bodyHTML = "
                <div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                    <h2 style='font-size: 28px; color: #333;'>¡Hola!</h2>
                    <p style='font-size: 20px; color: #555;'>Tu código de verificación para cambiar tu contraseña es:</p>
                    <p style='font-size: 24px; font-weight: bold; color: #007bff;'>$verificationCode</p>
                    <p style='font-size: 16px; color: #555;'>Por favor, ingrésalo en el formulario de verificación para continuar con el cambio de contraseña.</p>
                </div>
            ";
            $enviado = $mailSend->metEnviar("Cambio de Contraseña", "Usuario", $email, "Código de Verificación", $bodyHTML);

            if ($enviado) {
                // Redirigir a verificar_codigo.php con el email
                header("Location: verificar_codigo.php?email=" . urlencode($email));
                exit();
            } else {
                echo "No se pudo enviar el correo de verificación.";
            }
        } else {
            echo "Error al guardar el código de verificación en la base de datos.";
        }

        $stmt->close();
    } else {
        // Redirigir a cambio_correo.php con mensaje de error
        header("Location: cambio_correo.php?error=email_no_encontrado");
        exit();
    }
} else {
    echo "Datos del formulario no están presentes.";
}

$conn->close();
?>

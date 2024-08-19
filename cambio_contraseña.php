<?php
// Incluir archivo de conexión
include 'conexion.php';

// Inicializar variables para mensajes
$messageType = '';
$messageText = '';

// Manejo de la lógica de cambio de contraseña si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtrar y limpiar los datos recibidos
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $code = filter_input(INPUT_POST, 'verificationCode', FILTER_SANITIZE_STRING);
    $newPassword = trim($_POST['newPassword']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Verificar que el email, código y contraseñas no estén vacíos
    if (empty($email) || empty($code) || empty($newPassword) || empty($confirmPassword)) {
        $messageType = 'error';
        $messageText = 'Todos los campos son obligatorios.';
    } elseif ($newPassword !== $confirmPassword) {
        $messageType = 'error';
        $messageText = 'Las contraseñas no coinciden.';
    } else {
        // Consultar el código de verificación correspondiente al email en la tabla verificar_cambio
        $sql = "SELECT codigo_cambio FROM verificar_cambio WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta: " . $conn->error);
            $messageType = 'error';
            $messageText = 'Error en la consulta de verificación. Por favor, contacta al administrador.';
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($codigo_bd);
            $stmt->fetch();
            $stmt->close();

            // Registros de depuración
            error_log("Código de verificación ingresado: " . $code);
            error_log("Código de verificación almacenado: " . $codigo_bd);

            // Verificar si se encontró el código de verificación en la base de datos
            if ($codigo_bd === null) {
                $messageType = 'error';
                $messageText = 'El email proporcionado no tiene un código de verificación válido.';
            } elseif (trim($code) === trim($codigo_bd)) {
                // Si coinciden, actualizar la contraseña sin encriptar
                $nueva_contrasena = $newPassword;

                // Iniciar una transacción para asegurar la consistencia
                $conn->begin_transaction();
                try {
                    $sql_update = "UPDATE account_data SET password = ? WHERE email = ?";
                    $stmt_update = $conn->prepare($sql_update);

                    if ($stmt_update === false) {
                        throw new Exception("Error en la preparación de la consulta de actualización: " . $conn->error);
                    }

                    $stmt_update->bind_param("ss", $nueva_contrasena, $email);
                    if ($stmt_update->execute()) {
                        // Borrar el código de verificación después de cambiar la contraseña
                        $sql_delete = "DELETE FROM verificar_cambio WHERE email = ?";
                        $stmt_delete = $conn->prepare($sql_delete);

                        if ($stmt_delete === false) {
                            throw new Exception("Error en la preparación de la consulta de eliminación: " . $conn->error);
                        }

                        $stmt_delete->bind_param("s", $email);
                        if ($stmt_delete->execute()) {
                            $conn->commit();
                            $messageType = 'success';
                            $messageText = 'Contraseña cambiada exitosamente.';
                        } else {
                            throw new Exception("Error al eliminar el código de verificación: " . $stmt_delete->error);
                        }

                        $stmt_delete->close();
                    } else {
                        throw new Exception("Error al ejecutar la consulta de actualización: " . $stmt_update->error);
                    }
                    $stmt_update->close();
                } catch (Exception $e) {
                    $conn->rollback();
                    $messageType = 'error';
                    $messageText = 'Error al cambiar la contraseña: ' . $e->getMessage();
                    error_log($e->getMessage());
                }
            } else {
                $messageType = 'error';
                $messageText = 'Código de verificación incorrecto.';
            }
        }
    }
    $conn->close();

    // Redirigir con parámetros de mensaje
    header("Location: verificar_codigo.php?messageType=" . urlencode($messageType) . "&messageText=" . urlencode($messageText));
    exit();
}
?>

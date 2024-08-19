<?php
require('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si se ha enviado la ID para eliminar
    if (isset($_POST['id_account'])) {
        $id = $conn->real_escape_string($_POST['id']);

        // Elimina el registro de la base de datos
        $sql = "DELETE FROM pagina WHERE id_account = $id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registro eliminado exitosamente.'); window.location.href = 'principal.php';</script>";
        } else {
            echo "Error al eliminar el registro: " . $conn->error;
        }
    }
}
$conn->close();
?>

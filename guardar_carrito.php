<?php
session_start(); // Inicia la sesión

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tiendaonline";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del producto desde POST
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

// Verificar si se proporcionó un ID válido para mejores_ofertas
$mejores_ofertas_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_offer = false;

if ($mejores_ofertas_id > 0) {
    // Consulta para obtener los detalles del producto desde mejores_ofertas
    $sql = "SELECT nombre, precio_con_descuento, imagen1 FROM mejores_ofertas WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param("i", $mejores_ofertas_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se encontró el producto en mejores_ofertas
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();
            $is_offer = true;
        } else {
            echo "Producto no encontrado en mejores_ofertas.";
            $producto = null;
        }
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
}

// Si no se encontró en mejores_ofertas o no se proporcionó ID para mejores_ofertas, buscar en productos
if (!$is_offer && $product_id > 0) {
    // Consulta para obtener los detalles del producto desde productos
    $sql = "SELECT nombre_producto, precio, imagen FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se encontró el producto en productos
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();
            $is_offer = false;
        } else {
            echo "Producto no encontrado en productos.";
            $producto = null;
        }
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
}

// Verificar si se ha encontrado el producto
if ($producto) {
    // Preparar la consulta de inserción según el tipo de producto encontrado
    if (isset($_SESSION['email'])) {
        // Caso 1: Usar id_account basado en el email
        $email = $_SESSION['email'];
        
        // Obtener el id_account basado en el email
        $sql = "SELECT id_account FROM account_data WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $account = $result->fetch_assoc();
                $id_account = $account['id_account'];

                // Insertar en la tabla carrito
                $sql = "INSERT INTO carrito (id_account, nombre_producto, imagen, cantidad, precio_unitario) VALUES (?, ?, ?, 1, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    if ($is_offer) {
                        $stmt->bind_param("issd", $id_account, $producto['nombre'], $producto['imagen1'], $producto['precio_con_descuento']);
                    } else {
                        $stmt->bind_param("issd", $id_account, $producto['nombre_producto'], $producto['imagen'], $producto['precio']);
                    }
                    if ($stmt->execute()) {
                        header("Location: carrito.php");
                        exit();
                    } else {
                        echo "Error al guardar en el carrito.";
                    }
                } else {
                    die("Error en la preparación de la consulta: " . $conn->error);
                }
            } else {
                die("No se encontró la cuenta para el email proporcionado.");
            }
        } else {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
    } elseif (isset($_SESSION['google_id'])) {
        // Caso 2: Usar google_id
        $google_id = $_SESSION['google_id'];

        // Insertar en la tabla carrito
        $sql = "INSERT INTO carrito (google_id, nombre_producto, imagen, cantidad, precio_unitario) VALUES (?, ?, ?, 1, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            if ($is_offer) {
                $stmt->bind_param("sssd", $google_id, $producto['nombre'], $producto['imagen1'], $producto['precio_con_descuento']);
            } else {
                $stmt->bind_param("sssd", $google_id, $producto['nombre_producto'], $producto['imagen'], $producto['precio']);
            }
            if ($stmt->execute()) {
                header("Location: carrito.php");
                exit();
            } else {
                echo "Error al guardar en el carrito.";
            }
        } else {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
    } else {
        die("No se encontró una sesión válida.");
    }
} else {
    die("No se encontró el producto.");
}

$conn->close();
?>
<?php
session_start();

// Conectar a la base de datos
$host = 'localhost';
$dbname = 'tiendaonline';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Verificar si el usuario está autenticado por email o google_id
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Obtener el id_account del usuario basado en el email
    $sql = "SELECT id_account FROM account_data WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        die("No se encontró el usuario en la base de datos.");
    }

    $id_account = $account['id_account'];

    // Obtener productos del carrito para el id_account
    $sql = "SELECT id AS id_carrito, nombre_producto, imagen, cantidad, precio_unitario
            FROM carrito
            WHERE id_account = :id_account";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_account', $id_account, PDO::PARAM_INT);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_SESSION['google_id'])) {
    $google_id = $_SESSION['google_id'];

    // Obtener productos del carrito para el google_id usando LIKE
    $sql = "SELECT id AS id_carrito, nombre_producto, imagen, cantidad, precio_unitario
            FROM carrito
            WHERE google_id LIKE :google_id";
    $stmt = $pdo->prepare($sql);

    // Usar % como comodín para permitir coincidencias parciales
    $google_id_like = "%{$google_id}%";
    $stmt->bindParam(':google_id', $google_id_like, PDO::PARAM_STR);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Depurar errores
    if (!$productos) {
        echo "No se encontraron productos para el google_id: " . htmlspecialchars($google_id);
    }
} else {
    // Redirigir a la página de inicio de sesión si no hay email ni google_id en la sesión
    header("Location: login.php");
    exit();
}

// Manejar la actualización de la cantidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_carrito'], $_POST['accion'])) {
    $id_carrito = (int) $_POST['id_carrito'];
    $accion = $_POST['accion'];

    // Obtener la cantidad actual del producto
    $sql = "SELECT cantidad FROM carrito WHERE id = :id_carrito";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        $cantidad = (int) $producto['cantidad'];

        if ($accion === 'incrementar') {
            $cantidad++;
        } elseif ($accion === 'decrementar' && $cantidad > 1) {
            $cantidad--;
        }

        // Actualizar la cantidad en la base de datos
        $updateSql = "UPDATE carrito SET cantidad = :cantidad WHERE id = :id_carrito";
        $stmt = $pdo->prepare($updateSql);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        $stmt->execute();

        // Redirigir para evitar reenvío de formulario
        header("Location: carrito.php");
        exit();
    }
}

// Manejar la eliminación del producto
if (isset($_GET['eliminar'])) {
    $id_carrito = (int) $_GET['eliminar'];
    if ($id_carrito > 0) {
        $deleteSql = "DELETE FROM carrito WHERE id = :id_carrito";

        if (isset($_SESSION['email'])) {
            $deleteSql .= " AND id_account = :id_account";
        } elseif (isset($_SESSION['google_id'])) {
            $deleteSql .= " AND google_id = :google_id";
        }

        $stmt = $pdo->prepare($deleteSql);
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);

        if (isset($_SESSION['email'])) {
            $stmt->bindParam(':id_account', $id_account, PDO::PARAM_INT);
        } elseif (isset($_SESSION['google_id'])) {
            $stmt->bindParam(':google_id', $google_id, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            header("Location: carrito.php"); // Redirige para evitar el reenvío del formulario
            exit;
        } else {
            echo "Error al eliminar el producto.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        a {
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 24px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #555;
        }

        tr {
            border-bottom: 1px solid #ddd;
        }

        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
        }

        .quantity-controls button {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 4px;
            cursor: pointer;
        }

        .quantity-controls span {
            margin: 0 10px;
        }

        .total-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .total-box {
            background-color: #f7f7f7;
            padding: 24px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }

        .total-box div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .total-box div span {
            font-weight: bold;
        }

        .checkout-button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px;
            width: 100%;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 16px;
        }

        .checkout-button:hover {
            background-color: #0056b3;
        }

        .remove-button {
            padding: 5px 5px;
            border-radius: 5px;
            border: 1px solid white;
            background-color: red;
            color: white;
        }

        button {
            border-radius: 3px;
            padding: 7px 7px;
        }

        .back-button {
            display: flex;
            align-content: left;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: black;
        }

        .back-button:hover {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='index.php'"><i
                class="fas fa-arrow-left"></i></button>
        <h1>Carrito de Compra</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Precio Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No hay productos en el carrito.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $total_final = 0; // Variable para almacenar el total final
                        foreach ($productos as $producto):
                            $precio_total_producto = $producto['precio_unitario'] * $producto['cantidad'];
                            $total_final += $precio_total_producto; // Sumar al total final
                            ?>
                            <tr>
                                <td><img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                                        alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"></td>
                                <td><?php echo htmlspecialchars($producto['nombre_producto']); ?></td>
                                <td>
                                    <div class="quantity-controls">
                                        <form method="POST" action="carrito.php">
                                            <input type="hidden" name="id_carrito"
                                                value="<?php echo htmlspecialchars($producto['id_carrito']); ?>">
                                            <input type="hidden" name="accion" value="decrementar">
                                            <button type="submit">-</button>
                                        </form>
                                        <span><?php echo htmlspecialchars($producto['cantidad']); ?></span>
                                        <form method="POST" action="carrito.php">
                                            <input type="hidden" name="id_carrito"
                                                value="<?php echo htmlspecialchars($producto['id_carrito']); ?>">
                                            <input type="hidden" name="accion" value="incrementar">
                                            <button type="submit">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($producto['precio_unitario'], 2); ?></td>
                                <td>$<?php echo number_format($precio_total_producto, 2); ?></td>
                                <td><a href="?eliminar=<?php echo htmlspecialchars($producto['id_carrito']); ?>"
                                        class="remove-button">Eliminar</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($productos)): ?>
            <div class="total-container">
                <div class="total-box">
                    <div>
                        <span>Total</span>
                        <span>$<?php echo number_format($total_final, 2); ?></span>
                    </div>
                    <form action="direccion.php" method="POST">
                        <input type="hidden" name="total_final" value="<?php echo $total_final; ?>">
                        <button type="submit" class="checkout-button">Finalizar Compra</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>
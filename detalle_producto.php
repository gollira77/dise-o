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

// Obtener el ID del producto desde la URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar si se proporcionó un ID válido
if ($product_id > 0) {
    // Consulta para obtener los detalles del producto
    $sql = "SELECT nombre_producto, precio, imagen, descripcion FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se encontró el producto
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();

            // Convertir la imagen a base64
            $imagen_base64 = base64_encode($producto['imagen']);
            $tipo_imagen = "image/png"; // Cambiar si el tipo de imagen es diferente
            $imagen_url = "data:$tipo_imagen;base64,$imagen_base64";
        } else {
            echo "Producto no encontrado.";
            exit;
        }
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
} else {
    echo "ID de producto inválido.";
    exit;
}

// Manejar la inserción en la tabla carrito
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_carrito'])) {
    // Insertar en la tabla carrito
    $sql = "INSERT INTO carrito (id_account, imagen, nombre_producto, cantidad, precio) VALUES (?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Usar un valor predeterminado para id_account si no está disponible
        $id_account = 1; // Puedes cambiar esto según tu lógica
        $stmt->bind_param("isss", $id_account, $producto['imagen'], $producto['nombre_producto'], $producto['precio']);
        if ($stmt->execute()) {
            $mensaje = "Guardado en el carrito exitosamente.";
        } else {
            $mensaje = "Error al guardar en el carrito.";
        }
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- El resto del código del head -->
    <style>
        body {
            font-family: 'Kumbh Sans', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            max-width: 1000px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .product-content {
            flex: 1 1 45%;
            padding: 20px;
            text-align: left;
        }

        .product-banner {
            flex: 1 1 45%;
            text-align: right;
            padding: 20px;
        }

        .product-banner img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .h1.product-title {
            font-size: 28px;
            margin-bottom: 10px;
            color: #000;
        }

        .product-text {
            font-size: 16px;
            color: #000;
            margin-bottom: 20px;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 20px;
            display: block;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .cart-btn {
            background-color: #000;
            color: #fff;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .cart-btn ion-icon {
            margin-right: 5px;
        }

        .cart-btn:hover {
            background-color: #333;
        }

        .back-button {
            display: flex;
            align-content: left;
            top: 20px;
            right: 20px;
            background: none;
            margin-bottom: 10px;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: black;
        }

        .back-button:hover {
            color: #333;
        }

        .success-message {
            color: green;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <main>
        <article>
            <section class="section product" aria-label="product">
                <button class="back-button" onclick="window.location.href='categoria.php'"><i
                        class="fas fa-arrow-left"></i></button>
                <div class="container">
                    <div class="product-banner">
                        <figure>
                            <img src="<?php echo htmlspecialchars($imagen_url); ?>" width="400" height="400"
                                loading="lazy" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                                class="img-cover">
                        </figure>
                    </div>
                    <div class="product-content">
                        <h1 class="h1 product-title"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h1>
                        <p class="product-text"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <span class="price"
                            data-total-price>$<?php echo htmlspecialchars($producto['precio']); ?></span>
                        <div class="btn-group">
                            <form method="post" action="guardar_carrito.php">
                                <input type="hidden" name="product_id"
                                    value="<?php echo htmlspecialchars($product_id); ?>">
                                <button class="cart-btn" type="submit" name="guardar_carrito">
                                    <ion-icon name="save-outline" aria-hidden="true"></ion-icon>
                                    <span class="span">Añadir a carrito</span>
                                </button>
                            </form>

                        </div>
                        <?php if ($mensaje): ?>
                            <p class="success-message"><?php echo $mensaje; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </article>
    </main>
    <script src="descrip_pdts.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
</body>

</html>

    <?php
    // Configura la conexión a la base de datos
    $servername = "localhost";
    $username = "root"; // Cambia según tu configuración
    $password = ""; // Cambia según tu configuración
    $dbname = "tiendaonline"; // Cambia según tu configuración

    // Crea la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Obtén el id de categoría desde la URL
    $category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Consulta para obtener el tipo_actividad de la tabla categorias
    $sql_categoria = "SELECT tipo_actividad FROM categorias WHERE id = ?";
    $stmt_categoria = $conn->prepare($sql_categoria);

    // Verifica si la consulta se preparó correctamente
    if (!$stmt_categoria) {
        die("Error en la preparación de la consulta de categorías: " . $conn->error);
    }

    $stmt_categoria->bind_param("i", $category_id);
    $stmt_categoria->execute();
    $result_categoria = $stmt_categoria->get_result();

    // Almacena el tipo_actividad si se encuentra la categoría
    $tipo_actividad = '';
    if ($result_categoria->num_rows > 0) {
        $row_categoria = $result_categoria->fetch_assoc();
        $tipo_actividad = $row_categoria['tipo_actividad'];
    }

    // Prepara y ejecuta la consulta SQL para obtener los productos
    $sql_productos = "SELECT id, nombre_producto, precio, imagen FROM productos WHERE categoria_id = ?";
    $stmt_productos = $conn->prepare($sql_productos);

    // Verifica si la consulta se preparó correctamente
    if (!$stmt_productos) {
        die("Error en la preparación de la consulta de productos: " . $conn->error);
    }

    $stmt_productos->bind_param("i", $category_id);
    $stmt_productos->execute();
    $result_productos = $stmt_productos->get_result();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f9f9f9;
                color: #333;
                margin: 0;
                padding: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            a {
            text-decoration: none;
            }
            
            h2 {
                margin-bottom: 20px;
                font-size: 1.5rem;
                color: #666;
            }

            .grid {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
            }

            .card {
                background-color: #fff;
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
                width: 250px;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                cursor: pointer;
                text-align: center;
            }

            .card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            }

            .card img {
                width: 100%;
                height: 180px;
                object-fit: cover;
                border-bottom: 1px solid #eaeaea;
            }

            .card-content {
                padding: 20px;
            }

            .card-title {
                margin: 0 0 10px 0;
                font-size: 1.2rem;
                font-weight: bold;
                color: #222;
            }

            .card-price {
                margin: 0 0 15px 0;
                font-size: 1rem;
                color: #555;
            }

            .btn {
                padding: 10px 20px;
                font-size: 1rem;
                font-weight: bold;
                background-color: #000;
                color: #fff;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .btn:hover {
                background-color: #333;
            }

            .btn:active {
                background-color: #555;
            }
        </style>
    </head>
    <body>
    <section class="container">
        <div>
        <?php if ($tipo_actividad): ?>
            <h2>Producto de la categoria: <?php echo htmlspecialchars($tipo_actividad); ?></h2>
            <br>
        <?php endif; ?>
        </div>
        <div class="grid">
        <?php
        if ($result_productos->num_rows > 0) {
            while ($row = $result_productos->fetch_assoc()) {
            // Convertir la imagen binaria a base64
            $imagen_base64 = base64_encode($row['imagen']);
            // Crear el string para el src de la imagen en HTML
            $imagen_src = 'data:image/jpeg;base64,' . $imagen_base64;

            // El id del producto se pasa a la página de detalles
            $producto_id = intval($row['id']);

            echo '<div class="card" onclick="window.location.href=\'detalle_producto.php?id=' . $producto_id . '\'">';
            echo '<img src="' . $imagen_src . '" alt="Product Image">';
            echo '<div class="card-content">';
            echo '<h3 class="card-title">' . htmlspecialchars($row['nombre_producto']) . '</h3>';
            echo '<p class="card-price">$' . htmlspecialchars(number_format($row['precio'], 2)) . '</p>';
            echo '</div>';
            echo '</div>';
            }
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
        </div>
    </section>
    </body>
    </html>

    <?php
    // Cierra las conexiones
    $stmt_categoria->close();
    $stmt_productos->close();
    $conn->close();
    ?>

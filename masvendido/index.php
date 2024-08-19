<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = ""; // Cambia si tienes una contraseña
$dbname = "tiendaonline";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consulta SQL para obtener los productos más vendidos
$sql = "
    SELECT p.id, p.nombre_producto, p.descripcion, p.precio, p.imagen, SUM(dp.cantidad) as total_vendido
    FROM productos p
    JOIN detalles_pedido dp ON p.id = dp.producto_id
    JOIN pedidos pe ON dp.pedido_id = pe.id
    WHERE pe.estado = 'entregado'
    GROUP BY p.id
    ORDER BY total_vendido DESC
    LIMIT 5;
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Más Vendidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .product-card h3 {
            font-size: 1.2em;
            margin: 10px 0;
        }
        .product-card p {
            color: #555;
        }
        .product-card .price {
            font-weight: bold;
            margin-top: 10px;
        }
        .product-card .actions {
            margin-top: 20px;
        }
        .product-card .actions button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .product-card .actions button:hover {
            background-color: #0056b3;
        }
        .back-to-top, .back-to-previous {
            position: fixed;
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 50%;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .back-to-top {
            bottom: 20px;
            right: 20px;
        }
        .back-to-previous {
            bottom: 20px;
            left: 20px;
        }
        .back-to-top svg, .back-to-previous svg {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Productos Más Vendidos</h1>
    <p>Estos son los productos más elegidos por los clientes.</p>

    <div class="product-grid">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $imgData = base64_encode($row['imagen']);
                echo '<div class="product-card">';
                echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="' . htmlspecialchars($row['nombre_producto']) . '">';
                echo '<h3>' . htmlspecialchars($row['nombre_producto']) . '</h3>';
                echo '<p>' . htmlspecialchars($row['descripcion']) . '</p>';
                echo '<p class="price">Precio: $' . number_format($row['precio'], 2) . '</p>';
                echo '<p>Total Vendido: ' . $row['total_vendido'] . '</p>';
                echo '<div class="actions">';
                echo '<button onclick="addToCart(' . $row['id'] . ')">Agregar al Carrito</button>';
                echo '<button onclick="buyNow(' . $row['id'] . ')">Comprar Ahora</button>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No se encontraron productos.</p>';
        }
        ?>
    </div>
</div>

<!-- Botón de "volver arriba" -->
<a href="#" class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
</a>

<!-- Botón de "volver a la página anterior" -->
<a href="javascript:history.back()" class="back-to-previous">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
</a>

<!-- Scripts -->
<script>
    function addToCart(productId) {
        // Redirigir a una página para agregar al carrito, por ejemplo, carrito.php
        window.location.href = 'carrito.php?add=' + productId;
    }

    function buyNow(productId) {
        // Redirigir a una página para comprar ahora, por ejemplo, checkout.php
        window.location.href = 'checkout.php?product=' + productId;
    }
</script>

</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>

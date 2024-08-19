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

// Obtener datos del producto
$id_producto = $_POST['id_producto'];
$query = 'SELECT * FROM productos WHERE id = :id_producto';
$stmt = $pdo->prepare($query);
$stmt->execute(['id_producto' => $id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die('Producto no encontrado.');
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if (!isset($_SESSION['carrito'][$id_producto])) {
    $_SESSION['carrito'][$id_producto] = [
        'nombre' => $producto['nombre'],
        'cantidad' => 1,
        'precio_unitario' => $producto['precio'],
        'precio_total' => $producto['precio']
    ];
} else {
    $_SESSION['carrito'][$id_producto]['cantidad'] += 1;
    $_SESSION['carrito'][$id_producto]['precio_total'] += $producto['precio'];
}

// Redirigir a la página principal
header('Location: index.php');
exit();
?>

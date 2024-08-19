<?php
session_start();

// Variables para almacenar la información del usuario
$user_logged_in = false;
$user_name = '';
$user_picture = '';
$cart_count = 0; // Variable para almacenar el conteo del carrito

// Conectar a la base de datos
$db = new mysqli('localhost', 'root', '', 'tiendaonline');
if ($db->connect_error) {
    die('Error de conexión a la base de datos: ' . htmlspecialchars($db->connect_error));
}

// Verificar si el usuario ha iniciado sesión mediante email
if (isset($_SESSION['email'])) {
    $user_email = htmlspecialchars($_SESSION['email']);

    // Preparar y ejecutar la consulta para obtener id_account, username y tipoUsuario desde account_data
    $stmt = $db->prepare('SELECT id_account, username, tipoUsuario FROM account_data WHERE email = ?');
    if ($stmt) {
        $stmt->bind_param('s', $user_email);
        $stmt->execute();
        $stmt->bind_result($id_account, $retrieved_username, $user_type);
        $stmt->fetch();
        $stmt->close();

        // Si se encontró un username, actualizar variables de sesión
        if ($retrieved_username) {
            $user_name = htmlspecialchars($retrieved_username);
            $_SESSION['user_name'] = $retrieved_username;
            $user_logged_in = true;

            // Verificar si el usuario es administrador
            $_SESSION['is_admin'] = ($user_type === 'administrador');
        }

        // Obtener la cantidad de productos en el carrito usando el id_account
        $stmt = $db->prepare('SELECT COUNT(*) FROM carrito WHERE id_account = ?');
        if ($stmt) {
            $stmt->bind_param('i', $id_account);
            $stmt->execute();
            $stmt->bind_result($cart_count);
            $stmt->fetch();
            $stmt->close();
        }
        $_SESSION['cart_count'] = $cart_count ? $cart_count : 0;
    }

} elseif (isset($_SESSION['google_id'])) {
    // Verificar si el usuario ha iniciado sesión mediante Google ID
    $google_id = htmlspecialchars($_SESSION['google_id']);

    // Preparar y ejecutar la consulta para obtener el nombre y foto desde la tabla usuarios
    $stmt = $db->prepare('SELECT nombre, foto FROM usuarios WHERE google_id = ?');
    if ($stmt) {
        $stmt->bind_param('s', $google_id);
        $stmt->execute();
        $stmt->bind_result($retrieved_name, $image_url);
        $stmt->fetch();
        $stmt->close();

        // Si se encontró un nombre, actualizar variables de sesión
        if ($retrieved_name) {
            $user_name = htmlspecialchars($retrieved_name);
            $_SESSION['user_name'] = $retrieved_name;
            $user_logged_in = true;

            // Usar la URL de la imagen directamente
            if ($image_url) {
                $user_picture = htmlspecialchars($image_url);
            }
        }

        // Obtener la cantidad de productos en el carrito usando el google_id
        $stmt = $db->prepare('SELECT COUNT(*) FROM carrito WHERE google_id = ?');
        if ($stmt) {
            $stmt->bind_param('s', $google_id);
            $stmt->execute();
            $stmt->bind_result($cart_count);
            $stmt->fetch();
            $stmt->close();
        }
        $_SESSION['cart_count'] = $cart_count ? $cart_count : 0;
    }
} else {
    // Si el usuario no ha iniciado sesión
    $_SESSION['cart_count'] = 0;
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .user-info {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .user-picture {
            width: 50px;
            /* Ajustar según sea necesario */
            height: 50px;
            /* Ajustar según sea necesario */
            border-radius: 50%;
            /* Para imagen redonda */
        }

        .cart-button {
            color: black;
        }

        header {
            margin-top: 8px;
        }

        .user-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .user-name {
            font-size: 16px;
            color: #333;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            min-width: 150px;
        }

        .dropdown-menu a {
            display: block;
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
            text-decoration: none;
        }

        .slider{
            height: 435px;
        }
        .search-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 5px;
        }

        .back-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 20px;
        }

        .back-button i {
            font-size: 16px;
        }

        .category-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            /* Ajusta según sea necesario */
        }

        .category-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 200px;
            /* Ajusta el ancho base */
            overflow: hidden;
            transition: transform 0.3s;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-card img {
            width: 100%;
            /* Ajusta la imagen al ancho del contenedor */
            height: auto;
            /* Mantiene la proporción de la imagen */
            object-fit: cover;
            /* Ajusta la imagen para cubrir el contenedor sin deformarse */
        }

        .category-card .category-info {
            padding: 15px;
            text-align: center;
        }

        .category-card .category-info h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        /* Estilos para el pie de página */
        .custom-footer {
            background-color: #e9ecef;
            /* Color de fondo del pie de página */
            padding: 2rem 1rem;
            /* Espaciado interno */
        }

        .custom-container {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .custom-footer-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            flex: 1;
            min-width: 200px;
            /* Ajusta según sea necesario */
        }

        .custom-footer-brand {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .custom-footer-icon {
            background-color: transparent;
        }

        .custom-footer h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .custom-footer p {
            margin: 0;
            color: #6c757d;
        }

        .custom-footer a {
            text-decoration: none;
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .custom-footer a:hover {
            color: #007bff;
        }

        .custom-social-icons {
            display: flex;

            gap: 1rem;
        }

        .custom-social-icons svg {
            width: 24px;
            height: 24px;
            color: #333;
        }

        .custom-contact-info>div {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .custom-contact-info a {
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .custom-contact-info a:hover {
            color: #007bff;
        }

        .custom-footer-icon svg {
            width: 24px;
            height: 24px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userInfo = document.querySelector('.user-info');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (userInfo && dropdownMenu) {
                userInfo.addEventListener('click', function () {
                    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                });

                document.addEventListener('click', function (event) {
                    if (!userInfo.contains(event.target) && !dropdownMenu.contains(event.target)) {
                        dropdownMenu.style.display = 'none';
                    }
                });
            }
        });
    </script>
    <!-- Incluir Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Incluir jQuery (necesario para Owl Carousel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Incluir Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

</head>

<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                </svg>
            </div>
            <div class="search-container">
                <div class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="M21 21l-4.3-4.3"></path>
                    </svg>
                </div>
                <input type="search" placeholder="Search products..." />
            </div>
            <div class="nav-links">
                <?php if ($user_logged_in): ?>
                    <div class="user-info">
                        <?php if (!empty($user_picture)): ?>
                            <img src="<?php echo $user_picture; ?>" alt="User Picture" class="user-picture">
                        <?php endif; ?>
                        <div class="user-name"><?php echo $user_name; ?></div>
                        <div class="dropdown-menu">
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <a href="admin/principal.php">Administrar</a>
                            <?php endif; ?>
                            <a href="logout.php">Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Accede</a>
                    <a href="register.php" class="nav-link">Regístrate</a>
                <?php endif; ?>

                <a href="carrito.php" class="cart-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
                        </path>
                    </svg>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </a>
            </div>
        </div>
    </header>

    <!-- New Navigation Section -->
    <nav class="secondary-nav">
        <ul class="secondary-nav-links">
            <li><a href="categoria.php" class="secondary-nav-link"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>Categorías</a></li>
            <li><a href="masvendido\index.php" class="secondary-nav-link">Más Vendidos</a></li>
            <li><a href="como\index.html" class="secondary-nav-link">Cómo Comprar</a></li>
            <li><a href="nosotros.html" class="secondary-nav-link">Sobre Nosotros</a></li>
        </ul>
    </nav>

    <!-- Carousel Section -->

    <?php
    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'tiendaonline');
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Función para obtener el tipo MIME de la imagen desde los datos binarios
    function get_image_type($image_data)
    {
        $image_info = getimagesizefromstring($image_data);
        return $image_info['mime'];
    }

    // Consultar la base de datos
    $query = "SELECT imagen FROM pagina WHERE tipoImagen = 'carrucel'";
    $result = $conn->query($query);
    ?>

    <div class="slider">
        <div class="list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $imagen_data = $row['imagen'];
                $tipoImagen = get_image_type($imagen_data);
                $imagenBase64 = base64_encode($imagen_data);
                ?>
                <div class="item">
                    <img src="data:<?php echo $tipoImagen; ?>;base64,<?php echo $imagenBase64; ?>">
                </div>
            <?php endwhile; ?>
        </div>

        <div class="buttons">
            <button id="prev">&lt;</button>
            <button id="next">&gt;</button>
        </div>
        <ul class="dots">
        </ul>
    </div>

    <?php
    $conn->close();
    ?>
    <script src="carrusel_oferta.js"></script>

    <!-- Consejo Informacion -->
    <div class="info-boxes">
        <div class="info-box">
            <div class="info-icon">
                <!-- Reemplaza con una imagen de tarjeta de crédito -->
                <img src="icons/tarjeta-de-credito.png" alt="Tarjeta de Crédito" width="24" height="24">
            </div>
            <div class="info-text">
                <p>Paga con Débito y Crédito</p>
            </div>
        </div>
        <div class="info-box">
            <div class="info-icon">
                <!-- Reemplaza con una imagen de caja de envío -->
                <img src="icons/entrega.png" alt="Caja de Envío" width="24" height="24">
            </div>
            <div class="info-text">
                <p>Hacemos envíos a todo el país</p>
            </div>
        </div>
        <div class="info-box">
            <div class="info-icon">
                <!-- Reemplaza con una imagen de escudo de garantía -->
                <img src="icons/seguro-de-calidad.png" alt="Escudo de Garantía" width="24" height="24">
            </div>
            <div class="info-text">
                <p>Garantía 100% asegurada</p>
            </div>
        </div>
    </div>

<!-- Carrusel Ofertas -->
<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'tiendaonline');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener los productos con sus respectivas imágenes en binario
$sql = "SELECT id, nombre, precio_anterior, precio_con_descuento, imagen1, imagen2 FROM mejores_ofertas";
$result = $conn->query($sql);
?>

<div class="container">
    <div class="row">
        <h2>Las Mejores Ofertas</h2>
        <div id="news-slider" class="owl-carousel">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];  // Obtener el ID del producto
                    $nombre = $row['nombre'];
                    $precio_anterior = $row['precio_anterior'];
                    $precio_con_descuento = $row['precio_con_descuento'];

                    // Convertir las imágenes binarias en base64
                    $imagen1 = base64_encode($row['imagen1']);
                    $imagen2 = base64_encode($row['imagen2']);
                    ?>

                    <!-- Producto -->
                    <div class="product">
                        <div class="product-image">
                            <a href="" class="image">
                                <img src="data:image/jpeg;base64,<?php echo $imagen1; ?>" class="pic-1">
                                <img src="data:image/jpeg;base64,<?php echo $imagen2; ?>" class="pic-2">
                            </a>
                        </div>
                        <div class="content">
                            <h3 class="title"><a href=""><?php echo $nombre; ?></a></h3>
                            <div class="price">
                                <span class="old-price">Antes: $<?php echo $precio_anterior; ?></span>
                                <span class="current-price">Ahora: $<?php echo $precio_con_descuento; ?></span>
                            </div>
                            <!-- Modificación: Enlace al carrito con ID del producto -->
                            <a href="guardar_carrito.php?id=<?php echo $id; ?>" class="cart">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="8" cy="21" r="1"></circle>
                                    <circle cx="19" cy="21" r="1"></circle>
                                    <path
                                        d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <?php
                }
            } else {
                echo "<p>No hay ofertas disponibles en este momento.</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php
$conn->close();
?>


    <script>
        $(document).ready(function () {
            $("#news-slider").owlCarousel({
                items: 3, // Muestra 3 productos por vez
                loop: true, // Ciclo infinito
                margin: 10, // Margen entre los productos
                autoplay: true, // Reproducción automática
                autoplayTimeout: 3000, // 3 segundos de espera antes de cambiar
                autoplayHoverPause: true, // Pausa al pasar el ratón por encima
                nav: true, // Muestra botones de navegación
                responsive: {
                    0: {
                        items: 1 // Muestra 1 producto en pantallas pequeñas
                    },
                    600: {
                        items: 2 // Muestra 2 productos en pantallas medianas
                    },
                    1000: {
                        items: 3 // Muestra 3 productos en pantallas grandes
                    }
                }
            });
        });
    </script>



    <!-- PIE DE PAGINA -->
    <footer class="custom-footer">
        <div class="custom-container">
            <div class="custom-footer-section">
                <div class="custom-footer-brand">
                    <div class="custom-footer-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m8 3 4 8 5-5 5 15H2L8 3z" />
                        </svg>
                    </div>
                    <h3>Outdoor Adventure</h3>
                    <p>Somos una empresa dedicada a la venta de artículos deportivos para actividades al aire libre.
                        Ofrecemos una amplia variedad de productos de alta calidad para que disfrutes de tus aventuras.
                    </p>
                </div>
            </div>
            <div class="custom-footer-section">
                <h3>Redes Sociales</h3>
                <div class="custom-social-icons">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                        </svg>
                    </a>
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                        </svg>
                    </a>
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-twitter-x" viewBox="0 0 16 16">
                            <path
                                d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                        </svg>
                    </a>
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-github" viewBox="0 0 16 16">
                            <path
                                d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="custom-footer-section">
                <h3>Contacto</h3>
                <div class="custom-contact-info">
                    <div>
                        <div class="custom-footer-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-envelope-at" viewBox="0 0 16 16">
                                <path
                                    d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z" />
                                <path
                                    d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648m-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z" />
                            </svg>
                        </div>
                        <a href="mailto:info@outdooradventure.com">info@outdooradventure.com</a>
                    </div>
                    <div>
                        <div class="custom-footer-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="black"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>
                        <a href="tel:+1234567890">+1 (234) 567-890</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
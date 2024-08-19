<?php
session_start();

// Variables para manejar la sesión
$user_logged_in = false;
$user_name = '';
$user_picture = '';

// Verificar si el usuario ha iniciado sesión mediante Google
if (isset($_SESSION['user_name'])) {
    $user_logged_in = true;
    $user_name = htmlspecialchars($_SESSION['user_name']);
    $user_picture = isset($_SESSION['user_picture']) ? htmlspecialchars($_SESSION['user_picture']) : '';
} elseif (isset($_SESSION['email'])) {
    // Si no ha iniciado sesión mediante Google, verificar si ha iniciado sesión con email
    $user_email = htmlspecialchars($_SESSION['email']);

    // Conectar a la base de datos
    $db = new mysqli('localhost', 'root', '', 'tiendaonline');

    if ($db->connect_error) {
        die('Error de conexión a la base de datos: ' . htmlspecialchars($db->connect_error));
    }

    // Preparar y ejecutar la consulta para obtener el username
    $stmt = $db->prepare('SELECT username FROM account_data WHERE email = ?');
    if ($stmt) {
        $stmt->bind_param('s', $user_email);
        $stmt->execute();
        $stmt->bind_result($retrieved_username);
        $stmt->fetch();
        $stmt->close();
    } else {
        die('Error al preparar la consulta: ' . htmlspecialchars($db->error));
    }

    $db->close();

    // Guardar el username en la sesión
    if ($retrieved_username) {
        $user_name = htmlspecialchars($retrieved_username);
        $_SESSION['user_name'] = $retrieved_username; // Actualizar la sesión con el username
        $user_logged_in = true; // Usuario está conectado
    } else {
        $user_name = 'Usuario desconocido';
    }
}

// Si el usuario no ha iniciado sesión, redirigirlo al login
if (!$user_logged_in) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Categorías</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .search-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* Espacia los elementos dentro del contenedor */
            margin-bottom: 20px;
            width: 100%;
            max-width: 600px;
        }

        .search-bar input {
            width: 100%;
            /* Mantiene el ancho completo del contenedor */
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 5px;
        }

        .category-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .category-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 200px;
            overflow: hidden;
            transition: transform 0.3s;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
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
    </style>
</head>

<body>
    <h1>Categorías</h1>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Buscar categorías...">
    </div>
    <div class="category-container" id="categoryContainer">
        <?php
        // Configuración de la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "tiendaonline";

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Comprobar conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Consultar categorías
        $sql = "SELECT id, tipo_actividad, imagen FROM categorias";
        $result = $conn->query($sql);

        // Verificar si la consulta fue exitosa
        if (!$result) {
            die("Error en la consulta: " . $conn->error);
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $imagen = base64_encode($row['imagen']);  // Codificar la imagen en base64
                echo '<div class="category-card" data-id="' . htmlspecialchars($row["id"]) . '" data-name="' . htmlspecialchars($row["tipo_actividad"]) . '">';
                echo '<img src="data:image/jpeg;base64,' . $imagen . '" alt="' . htmlspecialchars($row["tipo_actividad"]) . '">';  // Mostrar la imagen desde la base de datos
                echo '<div class="category-info">';
                echo '<h3>' . htmlspecialchars($row["tipo_actividad"]) . '</h3>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "No hay categorías disponibles.";
        }


        $conn->close();
        ?>

    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const categoryContainer = document.getElementById('categoryContainer');
        const categoryCards = categoryContainer.getElementsByClassName('category-card');

        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            for (let i = 0; i < categoryCards.length; i++) {
                let categoryName = categoryCards[i].getAttribute('data-name').toLowerCase();
                if (categoryName.includes(filter)) {
                    categoryCards[i].style.display = "";
                } else {
                    categoryCards[i].style.display = "none";
                }
            }
        });

        // Añadir evento de clic a cada tarjeta de categoría
        Array.from(categoryCards).forEach(card => {
            card.addEventListener('click', function () {
                const categoryId = this.getAttribute('data-id');
                window.location.href = `producto_categoria.php?id=${categoryId}`;
            });
        });
    </script>

</body>

</html>
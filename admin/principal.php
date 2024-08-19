<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Lateral con Acordeón hacia la Derecha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/barraBusqueda.css">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 280px;
            background-color: #000000;
            padding: 20px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .sidebar a.active {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }

        .sidebar .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .sidebar .user-info img {
            border-radius: 50%;
            margin-right: 10px;
        }

        .accordion {
            cursor: pointer;
            padding: 18px;
            width: 100%;
            text-align: left;
            border: none;
            outline: none;
            transition: 0.4s;
            margin-bottom: 10px;
            background-color: #333; /* Mismo fondo que el menú lateral */
            color: #fff; /* Texto blanco */
            border-radius: 5px; /* Bordes redondeados */
        }

        .accordion.active, .accordion:hover {
            background-color: #007bff; /* Mismo color que los botones del menú lateral */
        }

        .panel {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            width: 200px;
            background-color: #000000; /* Fondo negro para el panel */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
        }

        .panel a {
            display: block;
            padding: 8px 10px;
            text-decoration: none;
            color: #fff; /* Texto blanco */
            border-radius: 3px;
        }

        .panel a:hover {
            background-color: #007bff; /* Color de fondo azul al pasar el ratón */
        }

        .main-content h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .main-content {
            margin-left: 300px;
            padding: 20px;
            width: calc(100% - 300px);
        }

        .image-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .image-container img {
            max-width: 100px;
            margin-right: 20px;
        }

        .image-container p {
            margin: 0;
            flex-grow: 1;
        }

        .abml-buttons {
            display: flex;
            gap: 10px;
        }

        .abml-buttons form {
            display: inline;
        }

        .abml-buttons button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .abml-buttons button:hover {
            background-color: #0056b3;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-bar button {
            padding: 10px;
            background-color: #007bff; /* Color de fondo azul */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        .insert-button {
            margin-bottom: 20px;
            display: inline-block;
        }

        .insert-button a {
            padding: 12px 20px; /* Mayor tamaño para un botón más destacado */
            background-color: #007bff; /* Color de fondo principal */
            color: #fff; /* Color del texto */
            text-decoration: none; /* Sin subrayado */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor a mano al pasar sobre el botón */
            transition: background-color 0.3s, box-shadow 0.3s; /* Transición suave */
            font-weight: bold; /* Texto en negrita */
            text-align: center; /* Centrar texto */
            display: inline-block; /* Asegura que el botón se comporte como un bloque en línea */
        }

        .insert-button a:hover {
            background-color: #0056b3; /* Color de fondo al pasar el ratón */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Efecto de sombra para resaltar */
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            justify-items: center;
            margin-top: 20px;
        }

        .image-card {
            width: 300px;
            height: 400px; /* Aumentado para acomodar los botones */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between; /* Cambiado de center para distribuir espacio */
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        .image-card img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px; /* Añadido para separar la imagen del texto */
        }

        .image-card p {
            margin: 10px 0 0;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .buttons button,
        .buttons a {
            padding: 10px 15px; /* Tamaño de botón mayor para más visibilidad */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            color: #fff;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }

        .buttons .delete-button {
            background-color: #d32f2f; /* Rojo fuerte */
        }

        .buttons .delete-button:hover {
            background-color: #b71c1c; /* Rojo más oscuro */
        }

        .buttons .modify-button {
            background-color: #388e3c; /* Verde fuerte */
        }

        .buttons .modify-button:hover {
            background-color: #2c6c2f; /* Verde más oscuro */
        }

        .menu a {
            color: #ffffff; /* Texto blanco */
        }

        .menu i {
            color: #ffffff; /* Texto blanco */
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="user-info">
            <img src="imagen/logo.jpeg" alt="" width="100px">
        </div>
        <a href="../index.php" class="active">⬅ Atras</a>
        <button class="accordion">Datos de Empresa</button>
        <div class="panel">
            <?php
            require("conexion.php");

            // Obtener todas las tablas de la base de datos
            $result = $conn->query("SHOW TABLES");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
                    $table = $row[0];
                    echo "<a href='abml.php?table=$table'>" . ucfirst($table) . "</a>";
                }
            } else {
                echo "No se encontraron tablas en la base de datos.";
            }
            ?>
        </div>
        <div class="menu">
            <a href="#"><i class="fas fa-box"></i> Orders</a>
            <a href="#"><i class="fas fa-tags"></i> Products</a>
            <a href="#"><i class="fas fa-users"></i> Customers</a>
            <a href="reportes.php"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="#"><i class="fas fa-cogs"></i> Integrations</a>
           
        </div>
    </div>

    <div class="main-content">
        <center>
            <h1>Configuración de Página</h1>

            <!-- Formulario para buscar imágenes por nombre -->
            <form method="POST" action="" class="search-bar">
                <input type="text" name="search_name" placeholder="Buscar por nombre de imagen">
                <button type="submit">Buscar</button>
            </form>

            <!-- Botón Insertar con nuevo diseño -->
            <div class="insert-button">
                <a href="insertar2.php?table=pagina">Insertar</a>
            </div>

            <?php
            require('conexion.php');

            // Consulta base
            $sql = "SELECT id, nombre_imagen, descripcion, imagen FROM pagina";

            // Si se ha buscado un nombre específico, agregar condición a la consulta
            if (isset($_POST['search_name']) && !empty($_POST['search_name'])) {
                $search_name = $conn->real_escape_string($_POST['search_name']);
                $sql .= " WHERE nombre_imagen LIKE '%$search_name%'";
            }

            $result = $conn->query($sql);
            echo "<div class='image-grid'>";
            if ($result->num_rows > 0) {
                // Salida de cada fila
                while($row = $result->fetch_assoc()) {
                    echo '<div class="image-card">';
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row["imagen"]) . '" alt="' . htmlspecialchars($row["nombre_imagen"]) . '">';
                    echo '<p><strong>' . htmlspecialchars($row["nombre_imagen"]) . '</strong></p>';
                    echo '<p>' . htmlspecialchars($row["descripcion"]) . '</p>';
                    echo '<div class="buttons">';
                    
                    // Botón para eliminar
                    echo "<form method='POST' action='eliminar.php' onsubmit=\"return confirm('¿Estás seguro de que deseas eliminar este registro?');\">";
                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='delete-button'>Eliminar</button>";
                    echo "</form>";

                    // Botón para modificar
                    echo "<a href='modificar2.php?table=pagina&id=" . $row['id'] . "' class='modify-button'>Modificar</a>";
                    echo '</div>'; // Cierra .buttons
                    echo '</div>'; // Cierra .image-card
                }
            } else {
                echo "No hay imágenes.";
            }
            echo '</div>'; // Cierra .image-grid

            $conn->close();
            ?>
        </center>
    </div>

    <script>
        // Script para manejar el acordeón
        var acc = document.getElementsByClassName("accordion");
        for (var i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }
    </script>

</body>
</html>
<!doctype html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte · Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
      /* Estilos personalizados */
      .sidebar {
        background-color: #f8f9fa;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        padding-top: 20px;
      }
      .main-content {
        margin-left: 250px;
        padding: 20px;
      }

      .nav-item {
        color: black;
      }
    </style>
  </head>
  <body>
    <!-- Barra Lateral -->
    <div class="sidebar">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link active" href="principal.php">
            <i class="bi bi-house-door"></i> Inicio
          </a>
        </li>
        <!-- Añadir más elementos de navegación según sea necesario -->
      </ul>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
      <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reporte de Ventas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Compartir</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
          </div>
        </div>
      </header>

      <div class="container">
        <canvas id="salesChart" width="900" height="380"></canvas>

        <h2>Detalles del Reporte</h2>
        <div class="table-responsive">
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Ganancia Total</th>
                <th>Fecha de Compra</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Configuración de la base de datos
              $servername = "localhost";
              $username = "root";
              $password = "";
              $dbname = "tiendaonline"; // Nombre de tu base de datos

              // Crear conexión
              $conn = new mysqli($servername, $username, $password, $dbname);

              // Comprobar la conexión
              if ($conn->connect_error) {
                  die("Conexión fallida: " . $conn->connect_error);
              }

              // Consultar ganancias totales por producto
              $sql = "
                  SELECT 
                      p.id, 
                      p.nombre_producto, 
                      SUM(v.cantidad) AS total_vendido, 
                      (p.precio * SUM(v.cantidad)) AS ganancia_total, 
                      DATE(v.fecha_compra) AS fecha_compra 
                  FROM productos p
                  JOIN ventas v ON p.id = v.producto_id
                  GROUP BY p.id, p.nombre_producto, p.precio, DATE(v.fecha_compra)
                  ORDER BY DATE(v.fecha_compra)
              ";

              // Ejecutar consulta
              $result = $conn->query($sql);

              // Inicializar arrays para el gráfico
              $fechas = [];
              $ganancias = [];

              // Verificar si la consulta fue exitosa
              if ($result) {
                  // Procesar resultados para la tabla
                  if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          echo "<tr>
                              <td>{$row['id']}</td>
                              <td>{$row['nombre_producto']}</td>
                              <td>\${$row['ganancia_total']}</td>
                              <td>{$row['fecha_compra']}</td>
                            </tr>";

                          // Agregar datos al gráfico
                          $fechas[] = $row["fecha_compra"];
                          $ganancias[] = $row["ganancia_total"];
                      }
                  } else {
                      echo "<tr><td colspan='4'>No hay datos disponibles</td></tr>";
                  }
              } else {
                  echo "<tr><td colspan='4'>Error en la consulta: " . $conn->error . "</td></tr>";
              }

              $conn->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js"></script>
    <script>
      // Configuración del gráfico de ventas
      const ctx = document.getElementById('salesChart').getContext('2d');
      const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?php echo json_encode($fechas); ?>,
          datasets: [{
            label: 'Ganancia Total',
            data: <?php echo json_encode($ganancias); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Ganancia Total ($)'
              }
            },
            x: {
              title: {
                display: true,
                text: 'Fecha de Compra'
              },
              ticks: {
                autoSkip: true,
                maxTicksLimit: 10
              }
            }
          }
        }
      });
    </script>
  </body>
</html>

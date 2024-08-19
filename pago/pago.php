<?php

// Verificar si se ha recibido el total_final mediante POST
if (isset($_POST['total_final'])) {
    $total_final = $_POST['total_final'];
} else {
    // En caso de que no se reciba, puedes manejar el error o asignar un valor por defecto
    $total_final = 0.00;
}

// Incluir las dependencias de Mercado Pago
require 'vendor/autoload.php';

// Configuración de Mercado Pago
MercadoPago\SDK::setAccessToken('APP_USR-809949882908972-081403-8b9804566703cf9dfd2e416b98f3483f-1406623712');

$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = '0001';
$item->title = 'Producto de prueba';
$item->quantity = 1;
$item->unit_price = (float) $total_final; // Usar la variable total_final
$item->currency_id = "ARS"; // Cambiado a pesos argentinos

$preference->items = array($item);
$preference->save(); // Esto genera el ID de la preferencia

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con PayPal y Mercado Pago</title>

    <!-- SDK de PayPal -->
    <script
        src="https://www.paypal.com/sdk/js?client-id=Ae3lizaLvBxUZ0-Nua17vfbXIEBLdbOd-GfK907Li8oClZeVZhZ3hqRjiV_oxkrKQVyL--BRECq9Xqg6&currency=USD"></script>

    <!-- SDK de Mercado Pago -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>

    <!-- Incluyendo FontAwesome para el ícono -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #00aaff;
            /* Color celeste */
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #0088cc;
            /* Cambio de color al pasar el mouse */
        }

        .card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
            margin: 10px;
        }

        h3 {
            color: #333;
            margin-bottom: 20px;
        }

        .checkout-btn,
        #paypal-button-container {
            margin-bottom: 20px;
            width: 100%;
        }

        .checkout-btn button {
            width: 100%;
            height: 50px;
            font-size: 16px;
            background-color: #009ee3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }

        .card h3 {
            font-size: 1.5em;
            margin-bottom: 1em;
        }
    </style>
</head>

<body>

    <!-- Botón de regreso -->
    <a href="../carrito.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="card">
        <h3>Pagar con PayPal</h3>
        <!-- Contenedor para el botón de PayPal -->
        <div id="paypal-button-container"></div>

        <h3>Pagar con Mercado Pago</h3>
        <!-- Contenedor para el botón de Mercado Pago -->
        <div class="checkout-btn"></div>
    </div>

    <script>
        // Configuración del botón de PayPal
        paypal.Buttons({
            style: {
                color: 'gold',
                shape: 'rect',
                layout: 'vertical',
                label: 'pay'
            },

            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($total_final, 2); ?>' // Usar total_final en PayPal
                        }
                    }]
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    window.location.href = "completado.html";
                    alert('Transaction completed by ' + details.payer.name.given_name);

                    fetch('/path/to/your/server/endpoint', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            orderID: data.orderID,
                            payerID: data.payerID,
                            amount: '<?php echo number_format($total_final, 2); ?>' // Usar total_final en el envío de datos
                        })
                    }).then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                });
            },

            onError: function (err) {
                console.error('PayPal error:', err);
            }
        }).render('#paypal-button-container');

        // Configuración del botón de Mercado Pago
        const mp = new MercadoPago('APP_USR-9cae6f86-61f9-48e6-8c03-daa7884b0d80', {
            locale: 'es-AR'
        });

        mp.checkout({
            preference: {
                id: '<?php echo $preference->id; ?>'
            },
            render: {
                container: '.checkout-btn',
                label: 'Pagar con Mercado Pago'
            }
        });
    </script>

</body>

</html>
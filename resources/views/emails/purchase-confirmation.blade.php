<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .timeline {
            margin: 20px 0;
            padding: 0;
            list-style-type: none;
            display: flex;
            justify-content: space-between;
        }
        .timeline li {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .timeline li:before {
            content: '';
            width: 20px;
            height: 20px;
            background-color: #4CAF50;
            border-radius: 50%;
            display: block;
            margin: 0 auto 5px;
        }
        .timeline li:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #4CAF50;
            top: 9px;
            left: -50%;
            z-index: -1;
        }
        .timeline li:first-child:after {
            content: none;
        }
        .timeline .active {
            font-weight: bold;
        }
        .timeline .inactive {
            color: #999;
        }
        .timeline .inactive:before {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<div class="container">
    <header style="background: linear-gradient(135deg, #4CAF50, #45a049); color: white; text-align: center; padding: 30px 20px;">
        <img src="https://staging.chupaprecios.com.mx/chupaprecioslogo-bLx.svg" alt="Chupa Precios Logo" style="max-width: 200px; margin-bottom: 15px;">
        <h1 style="margin: 0; font-size: 28px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">¡Compra Exitosa!</h1>
    </header>

    <main style="padding: 20px;">
        <p style="font-size: 18px;">Estimado(a) Juan Pérez,</p>

        <p>Gracias por tu compra. Tu pedido ha sido procesado exitosamente con PayPal.</p>

        <div style="background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
            <h2 style="margin-top: 0; color: #4CAF50;">Detalles del Pedido</h2>
            <p><strong>Número de Orden:</strong> CP-12345</p>
            <p><strong>Fecha:</strong> 30 de diciembre, 2024</p>
            <p><strong>Método de Pago:</strong> PayPal <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" alt="PayPal Logo" style="vertical-align: middle; margin-left: 5px;"></p>
        </div>

        <div style="margin-top: 20px;">
            <h3>Estado del Pedido:</h3>
            <ul class="timeline">
                <li class="active">
                    <div>Pedido Recibido</div>
                </li>
                <li class="active">
                    <div>Pago Confirmado</div>
                </li>
                <li class="active">
                    <div>Procesando</div>
                </li>
                <li class="inactive">
                    <div>Enviado</div>
                </li>
                <li class="inactive">
                    <div>Entregado</div>
                </li>
            </ul>
        </div>

        <h3 style="color: #4CAF50;">Artículos Comprados:</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="background-color: #4CAF50; color: white;">
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Producto</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Cantidad</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Precio</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Smartphone XYZ</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">1</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">$599.99</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Auriculares Bluetooth</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">1</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">$79.99</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;">Funda Protectora</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">1</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">$19.99</td>
            </tr>
            </tbody>
            <tfoot>
            <tr style="background-color: #f2f2f2;">
                <td colspan="2" style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>Total:</strong></td>
                <td style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>$699.97</strong></td>
            </tr>
            </tfoot>
        </table>

        <p style="margin-top: 20px;">Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos.</p>

        <p style="font-weight: bold; color: #4CAF50;">¡Gracias por comprar con nosotros!</p>
    </main>

    <footer style="background: linear-gradient(135deg, #333, #222); color: white; text-align: center; padding: 20px; font-size: 14px;">
        <p style="margin-bottom: 10px;">&copy; 2024 Chupa Precios. Todos los derechos reservados.</p>
        <p>
            <a href="#" style="color: #4CAF50; text-decoration: none; margin: 0 10px;">Términos y Condiciones</a> |
            <a href="#" style="color: #4CAF50; text-decoration: none; margin: 0 10px;">Política de Privacidad</a>
        </p>
        <div style="margin-top: 15px;">
            <a href="#" style="display: inline-block; margin: 0 5px;"><img src="https://example.com/facebook-icon.png" alt="Facebook" style="width: 24px; height: 24px;"></a>
            <a href="#" style="display: inline-block; margin: 0 5px;"><img src="https://example.com/twitter-icon.png" alt="Twitter" style="width: 24px; height: 24px;"></a>
            <a href="#" style="display: inline-block; margin: 0 5px;"><img src="https://example.com/instagram-icon.png" alt="Instagram" style="width: 24px; height: 24px;"></a>
        </div>
    </footer>
</div>
</body>
</html>


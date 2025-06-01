<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmación de compra - Webcolsoluciones</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .header {
      text-align: center;
      padding-bottom: 10px;
    }
    .header h1 {
      color: #25D366; /* Color estilo WhatsApp */
    }
    .content {
      margin-top: 20px;
    }
    .footer {
      margin-top: 30px;
      text-align: center;
      font-size: 12px;
      color: #777;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      background-color: #25D366;
      color: #fff;
      padding: 12px 20px;
      text-decoration: none;
      border-radius: 5px;
    }
    .highlight {
      color: #333;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>¡Gracias por tu compra!</h1>
      <p><strong>Webcolsoluciones</strong></p>
    </div>
    <div class="content">
      <p>Hola <strong><?=$client?></strong>,</p>
      <p>Hemos recibido tu pago exitosamente. A continuación, los detalles de tu servicio:</p>
      <ul>
        <li><span class="highlight">Servicio:</span> <?=$service_name?> </li>
        <li><span class="highlight">Duración:</span> <?=$duration?></li>
        <li><span class="highlight">Precio:</span> $<?=$price?></li>
        <li><span class="highlight">Estado de activación:</span> <?=$activacion?></li>
      </ul>
      <p>Tu servicio estará activo dentro de las próximas 24 horas. Te notificaremos por este medio cuando todo esté listo.</p>

      <a href="https://wa.me/573208269050" class="btn">Contactar soporte</a>
    </div>
    <div class="footer">
      <p>Webcolsoluciones · Bucaramanga, Colombia</p>
      <p>Gracias por confiar en nosotros.</p>
    </div>
  </div>
</body>
</html>

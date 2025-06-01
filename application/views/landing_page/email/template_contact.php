<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo mensaje de contacto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      padding: 20px;
      color: #212529;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    h2 {
      color: #0a58ca;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    td {
      padding: 10px;
      border-bottom: 1px solid #dee2e6;
    }
    .footer {
      font-size: 12px;
      color: #6c757d;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📬 Nuevo mensaje de contacto</h2>
    <table>
      <tr>
        <td><strong>Nombre:</strong></td>
        <td><?= htmlspecialchars($name) ?></td>
      </tr>
      <tr>
        <td><strong>Email:</strong></td>
        <td><?= htmlspecialchars($email) ?></td>
      </tr>
      <tr>
        <td><strong>Mensaje:</strong></td>
        <td><?= nl2br(htmlspecialchars($message)) ?></td>
      </tr>
    </table>

    <div class="footer">
      Este mensaje fue generado automáticamente desde tu sitio web.
    </div>
  </div>
</body>
</html>

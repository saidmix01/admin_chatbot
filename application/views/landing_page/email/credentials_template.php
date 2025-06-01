<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Credenciales de Acceso</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* En emails reales conviene inlinear CSS, pero aquí usamos estilos Bootstrap */
    body {
      background-color: #f4f9f5;
      padding: 20px;
    }
    .card {
      max-width: 600px;
      margin: auto;
    }
  </style>
</head>
<body>
  <div class="card shadow-sm border-primary">
    <div class="card-body text-center">
      <h2 class="text-primary mb-4">
        <i class="fas fa-user-circle fa-2x"></i><br>
        ¡Bienvenido a WebColSoluciones!
      </h2>
      <p class="lead">Aquí tienes tus credenciales de acceso:</p>

      <div class="text-left mx-auto" style="max-width: 400px;">
        <p><strong>Usuario:</strong> <code><?= $user ?></code></p>
        <p><strong>Contraseña:</strong> <code><?= $password ?></code></p>
      </div>

      <a href="<?= base_url() ?>" class="btn btn-primary mt-4">
        Iniciar sesión
      </a>

      <hr>

      <p class="small text-muted">
        Por seguridad te recomendamos cambiar tu contraseña tras el primer inicio de sesión.
      </p>
    </div>
  </div>

  <!-- FontAwesome (opcional) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página no encontrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 50px;
        }
        h1 {
            font-size: 36px;
            color: #333;
        }
        p {
            font-size: 18px;
            color: #666;
        }
        .emoji {
            font-size: 48px;
        }
    </style>
</head>
<body>
    <h1>Error 404</h1>
    <p>¡Ups! Parece que te has perdido.</p>
    <p>Vuelve a <a href="<?=base_url()?>Home">la página de inicio</a> o intenta buscar lo que necesitas.</p>
    <p class="emoji">😕</p>
</body>
</html>

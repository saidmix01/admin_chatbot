<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $business_name ?> | Wapi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
            color: #1f2937;
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            padding: 2rem 1.5rem;
            text-align: center;
        }
        .header-logo {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        .header h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .header p { font-size: 0.875rem; opacity: 0.9; }
        .section { padding: 1.5rem; }
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #111827;
        }
        .product-card-min {
            background: #fff;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .product-card-min img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            background: #f3f4f6;
        }
        .product-info { flex: 1; }
        .product-info h4 { font-size: 0.9375rem; margin-bottom: 0.25rem; }
        .product-info .price { font-weight: 700; color: #6366f1; }
        .product-info .desc { font-size: 0.8125rem; color: #6b7280; margin-top: 0.25rem; }
        .whatsapp-float {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 56px;
            height: 56px;
            background: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(37,211,102,0.3);
            cursor: pointer;
            border: none;
            z-index: 100;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo">
            <i class="fas fa-store"></i>
        </div>
        <h1><?= $business_name ?></h1>
        <p><?= $description ?></p>
    </div>

    <?php if(!empty($products)): ?>
    <div class="section">
        <div class="section-title">Productos</div>
        <?php foreach($products as $p): ?>
        <div class="product-card-min">
            <div style="width: 80px; height: 80px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #d1d5db;">
                <?php if(!empty($p->ser_imagen)): ?>
                    <img src="<?= $p->ser_imagen ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                <?php else: ?>
                    <i class="fas fa-image"></i>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h4><?= $p->ser_name ?></h4>
                <div class="price">$<?= number_format($p->ser_price ?? 0, 0) ?></div>
                <div class="desc"><?= substr($p->ser_description ?? '', 0, 60) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
        <p>No hay productos disponibles aún</p>
    </div>
    <?php endif; ?>

    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
</body>
</html>
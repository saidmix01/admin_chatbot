<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $business_name ?> | Wapi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #f9fafb;
            color: #1f2937;
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
        }
        .header {
            position: relative;
            color: #fff;
            text-align: center;
            padding: 3rem 1.5rem 2rem;
            min-height: 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            overflow: hidden;
        }
        .header.no-cover {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }
        .header.has-cover {
            background-size: cover;
            background-position: center;
        }
        .header.has-cover::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.5) 60%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }
        .header > * {
            position: relative;
            z-index: 2;
        }
        .header-logo-cover {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 2rem;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(4px);
            border: 3px solid rgba(255,255,255,0.5);
        }
        .header-logo-no-cover {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 2rem;
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
        .header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .header p { font-size: 0.875rem; opacity: 0.9; }
        .section { padding: 1.5rem; }
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #111827;
        }

        /* Product card */
        .product-card-min {
            background: #fff;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .product-card-min:active {
            transform: scale(0.98);
        }
        .product-card-min img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            background: #f3f4f6;
            flex-shrink: 0;
        }
        .product-info { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
        .product-info h4 { font-size: 0.9375rem; margin-bottom: 0.25rem; }
        .product-info .price { font-weight: 700; color: #6366f1; font-size: 1.0625rem; }
        .product-info .desc { font-size: 0.8125rem; color: #6b7280; margin-top: 0.25rem; line-height: 1.4; }
        .product-info .type-tag {
            display: inline-block;
            background: #eef2ff;
            color: #6366f1;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 999px;
            align-self: flex-start;
            margin-top: 0.375rem;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            animation: fadeIn 0.2s;
        }
        .modal-overlay.open { display: block; }
        .modal-content {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            border-radius: 20px 20px 0 0;
            z-index: 1000;
            max-height: 85vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
        }
        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            border-radius: 20px 20px 0 0;
        }
        .modal-header h3 { font-size: 1rem; font-weight: 600; }
        .modal-close {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: #f3f4f6;
            font-size: 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }
        .modal-body { padding: 1.5rem; }
        .modal-img {
            width: 100%;
            max-height: 50vh;
            object-fit: contain;
            border-radius: 12px;
            background: #f3f4f6;
            margin-bottom: 1rem;
            display: block;
        }
        .modal-body h2 { font-size: 1.25rem; margin-bottom: 0.25rem; }
        .modal-body .price-lg { font-size: 1.5rem; font-weight: 700; color: #6366f1; margin-bottom: 1rem; }
        .modal-body .desc-lg { font-size: 0.9375rem; color: #4b5563; line-height: 1.6; margin-bottom: 1.5rem; }
        .btn-order {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.875rem;
            background: #25D366;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .btn-order:active { opacity: 0.8; }
        .btn-order i { font-size: 1.25rem; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }

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
    <?php if(!empty($cover)): ?>
    <div class="header has-cover" style="background-image: url('<?php echo base_url($cover); ?>');">
        <div class="header-logo-cover"><i class="fas fa-store"></i></div>
    <?php else: ?>
    <div class="header no-cover">
        <div class="header-logo-no-cover"><i class="fas fa-store"></i></div>
    <?php endif; ?>
        <h1><?= $business_name ?></h1>
        <p><?= $description ?></p>
    </div>

    <?php if(!empty($products)): ?>
    <div class="section">
        <div class="section-title">Productos</div>
        <?php foreach($products as $p): ?>
        <div class="product-card-min" onclick="openModal(<?= htmlspecialchars(json_encode([
            'name' => $p->ser_name,
            'price' => number_format($p->ser_price ?? 0, 0),
            'description' => $p->ser_description ?? '',
            'image' => $p->ser_imagen ?? '',
            'type' => $p->ser_type ?? 'producto'
        ]), ENT_QUOTES, 'UTF-8') ?>)">
            <div style="width: 80px; height: 80px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #d1d5db; flex-shrink: 0;">
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
                <span class="type-tag"><?= $p->ser_type ?? 'producto' ?></span>
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

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>
    <div class="modal-content" id="modalContent" style="display: none;">
        <div class="modal-header">
            <h3 id="modalType">Producto</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <img class="modal-img" id="modalImage" src="" alt="" style="display: none;">
            <h2 id="modalName"></h2>
            <div class="price-lg" id="modalPrice"></div>
            <div class="desc-lg" id="modalDesc"></div>
            <a class="btn-order" id="modalOrderBtn" target="_blank">
                <i class="fab fa-whatsapp"></i> Pedir por WhatsApp
            </a>
        </div>
    </div>

    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

<script>
var currentProduct = null;

function openModal(product) {
    currentProduct = product;
    document.getElementById('modalOverlay').classList.add('open');
    document.getElementById('modalContent').style.display = 'block';
    document.getElementById('modalType').textContent = product.type.charAt(0).toUpperCase() + product.type.slice(1);
    document.getElementById('modalName').textContent = product.name;
    document.getElementById('modalPrice').textContent = '$' + product.price;
    document.getElementById('modalDesc').textContent = product.description || 'Sin descripción';

    var img = document.getElementById('modalImage');
    if (product.image) {
        img.src = product.image;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }

    var msg = encodeURIComponent('Hola! Quiero informacion sobre: ' + product.name + ' ($' + product.price + ')');
    document.getElementById('modalOrderBtn').href = 'https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>?text=' + msg;

    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.getElementById('modalContent').style.display = 'none';
    document.body.style.overflow = '';
}
</script>

</body>
</html>
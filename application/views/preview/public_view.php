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
        html { background: #0b1220; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #f5f7fb;
            color: #0f172a;
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            padding-bottom: 96px;
            padding-top: env(safe-area-inset-top);
        }

        :root {
            --bg: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: rgba(15, 23, 42, 0.10);
            --card: #ffffff;
            --shadow: 0 18px 45px rgba(2, 6, 23, 0.12);
            --shadow-sm: 0 10px 28px rgba(2, 6, 23, 0.08);
            --focus: rgba(15, 23, 42, 0.16);
            --wa: #25D366;
            --wa2: #16a34a;
        }

        .hero-card {
            background: transparent;
            padding: 0;
        }
        .saas-hero {
            position: relative;
            border-radius: 0 0 28px 28px;
            overflow: hidden;
            background: radial-gradient(120% 160% at 10% 15%, rgba(15, 23, 42, 0.65), rgba(2, 6, 23, 0.95)) , #020617;
            aspect-ratio: 16 / 9;
            min-height: 210px;
            border: none;
            box-shadow: var(--shadow);
        }
        .saas-hero.no-cover::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(180px 180px at 18% 20%, rgba(255,255,255,0.14), transparent 60%),
                radial-gradient(240px 240px at 82% 30%, rgba(37,211,102,0.18), transparent 60%),
                radial-gradient(260px 260px at 60% 90%, rgba(255,255,255,0.10), transparent 62%);
            z-index: 0;
            opacity: 0.9;
        }
        .saas-hero__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            z-index: 0;
        }
        .saas-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(120% 120% at 10% 12%, rgba(255,255,255,0.10), transparent 55%),
                linear-gradient(180deg, rgba(2,6,23,0.12), rgba(2,6,23,0.55));
            z-index: 1;
        }
        .saas-hero__top {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: none;
        }
        .saas-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: 14px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.18);
            color: #fff;
            backdrop-filter: blur(10px);
        }
        .saas-badge i { font-size: 1.15rem; }
        .saas-avatar {
            position: absolute;
            left: 50%;
            bottom: 16px;
            transform: translate(-50%, 0);
            width: 96px;
            height: 96px;
            border-radius: 999px;
            background: #e2e8f0;
            border: 4px solid #ffffff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(15,23,42,0.30);
            z-index: 2;
            box-shadow: 0 16px 42px rgba(2, 6, 23, 0.26);
        }
        .saas-avatar.no-img {
            background: radial-gradient(120% 120% at 30% 20%, rgba(255,255,255,0.55), rgba(226,232,240,0.90));
            color: rgba(15,23,42,0.75);
        }
        .saas-avatar.no-img::before {
            content: attr(data-initials);
            font-weight: 800;
            letter-spacing: -0.02em;
            font-size: 1.6rem;
            color: rgba(15,23,42,0.86);
        }
        .saas-avatar.no-img i { display: none; }
        .saas-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .saas-avatar::after {
            content: "";
            position: absolute;
            inset: -10px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(15, 23, 42, 0.10), transparent 60%);
            z-index: -1;
        }
        .hero-spacer { height: 18px; }

        .biz-card {
            margin: 0 1rem 1rem;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(15,23,42,0.08);
            box-shadow: var(--shadow-sm);
            padding: 1rem 1rem 1.05rem;
            text-align: center;
        }
        .biz-title {
            font-size: 1.55rem;
            line-height: 1.15;
            font-weight: 750;
            letter-spacing: -0.03em;
            color: var(--text);
            margin-bottom: 0.35rem;
        }
        .biz-desc {
            display: none;
        }
        .biz-actions {
            display: none;
        }
        .btn {
            appearance: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 0.75rem 0.95rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, opacity 0.18s ease;
            user-select: none;
        }
        .btn:active { transform: translateY(1px); }
        .btn-wa {
            background: linear-gradient(135deg, var(--wa), var(--wa2));
            color: #fff;
            box-shadow: 0 12px 26px rgba(37,211,102,0.22);
        }
        .btn-wa:hover { opacity: 0.95; }
        .btn-ghost {
            background: rgba(15,23,42,0.03);
            color: var(--text);
            border: 1px solid rgba(15,23,42,0.10);
        }

        .section {
            padding: 1rem;
            margin: 0 1rem 1rem;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(15,23,42,0.08);
            box-shadow: var(--shadow-sm);
        }
        .section-title {
            font-size: 1.05rem;
            font-weight: 650;
            margin-bottom: 0.9rem;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .filters {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
        }
        .filters input, .filters select {
            width: 100%;
            padding: 0.8rem 0.9rem;
            border: 1px solid rgba(15,23,42,0.10);
            border-radius: 16px;
            background: #ffffff;
            font-size: 0.9rem;
            outline: none;
            transition: box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }
        .filters input:focus, .filters select:focus {
            border-color: rgba(15,23,42,0.28);
            box-shadow: 0 0 0 4px rgba(15,23,42,0.10);
            background: #fff;
        }
        .filters input { flex: 1; min-width: 0; }
        .filters select { appearance: none; }
        #filterCount { margin-bottom: 1rem !important; color: var(--muted) !important; }

        .price-filter {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem;
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.10);
            background: rgba(2,6,23,0.03);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.06);
            flex: 0 0 auto;
        }
        .pf-btn {
            appearance: none;
            border: none;
            background: transparent;
            color: rgba(15,23,42,0.70);
            font-weight: 650;
            font-size: 0.85rem;
            padding: 0.6rem 0.75rem;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease, color 0.18s ease;
            user-select: none;
            white-space: nowrap;
        }
        .pf-btn:active { transform: translateY(1px); }
        .pf-btn.is-active {
            background: #ffffff;
            color: rgba(15,23,42,0.92);
            box-shadow: 0 10px 22px rgba(2, 6, 23, 0.10);
        }
        .pf-btn:focus-visible { outline: none; box-shadow: 0 0 0 4px rgba(15,23,42,0.10), 0 10px 22px rgba(2, 6, 23, 0.10); }

        /* Product card */
        .product-card-min {
            background: #fff;
            border-radius: 18px;
            padding: 1rem;
            margin-bottom: 0.9rem;
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 0.95rem;
            border: 1px solid rgba(15,23,42,0.08);
            box-shadow: 0 10px 26px rgba(2, 6, 23, 0.07);
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            position: relative;
        }
        .product-card-min:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 50px rgba(2, 6, 23, 0.14);
            border-color: rgba(15,23,42,0.14);
        }
        .product-card-min:active {
            transform: translateY(0);
        }
        .product-thumb {
            width: 92px;
            height: 92px;
            border-radius: 16px;
            background: rgba(2,6,23,0.03);
            border: 1px solid rgba(15,23,42,0.10);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(15,23,42,0.18);
            flex-shrink: 0;
            overflow: hidden;
        }
        .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .product-info {
            min-width: 0;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.35rem 0.75rem;
            align-content: start;
            padding-top: 0.05rem;
        }
        .product-info h4 {
            grid-column: 1;
            font-size: 1rem;
            font-weight: 650;
            letter-spacing: -0.015em;
            color: var(--text);
            line-height: 1.25;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .product-info .price {
            grid-column: 2;
            font-weight: 750;
            color: var(--text);
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .product-info .desc { display: none; }
        .product-info .type-tag {
            display: inline-block;
            grid-column: 1 / -1;
            background: rgba(2,6,23,0.04);
            color: rgba(15,23,42,0.70);
            font-size: 0.65rem;
            font-weight: 650;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 999px;
            align-self: flex-start;
            margin-top: 0.25rem;
        }
        .product-actions {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.55rem;
        }
        .product-wa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.58rem 0.85rem;
            border-radius: 999px;
            background: rgba(37,211,102,0.10);
            color: rgba(15,23,42,0.90);
            border: 1px solid rgba(15,23,42,0.12);
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease;
        }
        .product-wa:hover { background: rgba(37,211,102,0.14); border-color: rgba(37,211,102,0.22); }
        .product-wa:active { transform: translateY(1px); }
        .product-wa:focus-visible { outline: none; box-shadow: 0 0 0 4px rgba(37,211,102,0.18); }

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
            background: linear-gradient(135deg, var(--wa), var(--wa2));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 16px 30px rgba(37,211,102,0.26);
            cursor: pointer;
            border: none;
            z-index: 100;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .whatsapp-float:active { transform: translateY(1px); }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }
        @media (max-width: 420px) {
            .filters { flex-direction: column; align-items: stretch; }
            .price-filter { width: 100%; justify-content: space-between; }
            .pf-btn { flex: 1; }
        }
        @media (max-width: 360px) {
            .product-card-min { grid-template-columns: 84px 1fr; padding: 0.9rem; }
            .product-thumb { width: 84px; height: 84px; border-radius: 15px; }
            .saas-avatar { width: 88px; height: 88px; }
            .hero-spacer { height: 16px; }
        }
    </style>
</head>
<body>
    <?php
        $cover = '';
        if (!empty($cover_image)) {
            $cover = preg_match('~^(data:|https?://)~', $cover_image) ? $cover_image : ($base_url . $cover_image);
        }
        $profile = '';
        if (!empty($profile_image)) {
            $profile = preg_match('~^(data:|https?://)~', $profile_image) ? $profile_image : ($base_url . $profile_image);
        }
        $initials = 'W';
        $bn = trim((string)($business_name ?? ''));
        if ($bn !== '') {
            $parts = preg_split('/\s+/u', $bn, -1, PREG_SPLIT_NO_EMPTY);
            $first = $parts[0] ?? '';
            $second = $parts[1] ?? '';
            $i1 = $first !== '' ? mb_substr($first, 0, 1, 'UTF-8') : '';
            $i2 = $second !== '' ? mb_substr($second, 0, 1, 'UTF-8') : '';
            $initials = trim(mb_strtoupper($i1 . $i2, 'UTF-8')) ?: mb_strtoupper(mb_substr($bn, 0, 1, 'UTF-8'), 'UTF-8');
        }
        $use_legacy_header = (!$cover && !$profile);
    ?>
    <div class="hero-card">
        <div class="saas-hero <?= $cover ? 'has-cover' : 'no-cover' ?>">
            <?php if($cover): ?>
                <img class="saas-hero__img" src="<?= htmlspecialchars($cover, ENT_QUOTES, 'UTF-8') ?>" alt="">
            <?php endif; ?>
            <div class="saas-hero__top">
                <div class="saas-badge">
                    <i class="fab fa-whatsapp"></i>
                </div>
            </div>
            <div class="saas-avatar <?= $profile ? 'has-img' : 'no-img' ?>" data-initials="<?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>">
                <?php if($profile): ?>
                    <img src="<?= htmlspecialchars($profile, ENT_QUOTES, 'UTF-8') ?>" alt="">
                <?php else: ?>
                    <i class="fas fa-store" style="font-size: 2rem;"></i>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-spacer"></div>
    </div>

    <div class="biz-card">
        <div class="biz-title"><?= $business_name ?></div>
        <div class="biz-desc"><?= $description ?></div>
        <div class="biz-actions">
            <a class="btn btn-wa" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp_number) ?>" target="_blank">
                <i class="fab fa-whatsapp"></i>
                Hablar por WhatsApp
            </a>
            <?php if(!empty($store_slug)): ?>
            <a class="btn btn-ghost" href="<?= htmlspecialchars($base_url . 'preview/store/' . $store_slug, ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                <i class="fa-solid fa-link"></i>
                Compartir
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(!empty($products)): ?>
    <div class="section">
        <div class="section-title">Productos</div>
        <div class="filters">
            <input id="filterSearch" type="text" placeholder="Buscar producto...">
            <div class="price-filter" id="priceFilter">
                <button type="button" class="pf-btn is-active" data-order="price_asc">Precio ↑</button>
                <button type="button" class="pf-btn" data-order="price_desc">Precio ↓</button>
            </div>
        </div>
        <div id="filterCount" style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.75rem;"></div>
        <?php foreach($products as $p): ?>
        <?php
            $p_img = '';
            if (!empty($p->ser_imagen)) {
                $p_img = preg_match('~^https?://~', $p->ser_imagen) ? $p->ser_imagen : ($base_url . $p->ser_imagen);
            }
            $p_price = (float)($p->ser_price ?? 0);
            $wa_msg = 'Hola! Quiero informacion sobre: ' . ($p->ser_name ?? '') . ' ($' . number_format($p_price, 0) . ')';
            $wa_href = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp_number) . '?text=' . rawurlencode($wa_msg);
        ?>
        <div class="product-card-min product-card" data-name="<?= htmlspecialchars(mb_strtolower($p->ser_name ?? ''), ENT_QUOTES, 'UTF-8') ?>" data-price="<?= $p_price ?>" onclick="openModal(<?= htmlspecialchars(json_encode([
            'name' => $p->ser_name,
            'price' => number_format($p_price, 0),
            'description' => $p->ser_description ?? '',
            'image' => $p_img,
            'type' => $p->ser_type ?? 'producto'
        ]), ENT_QUOTES, 'UTF-8') ?>)">
            <div class="product-thumb">
                <?php if(!empty($p_img)): ?>
                    <img src="<?= htmlspecialchars($p_img, ENT_QUOTES, 'UTF-8') ?>" alt="">
                <?php else: ?>
                    <i class="fas fa-image"></i>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h4><?= $p->ser_name ?></h4>
                <div class="price">$<?= number_format($p->ser_price ?? 0, 0) ?></div>
                <div class="desc"><?= substr($p->ser_description ?? '', 0, 60) ?></div>
                <span class="type-tag"><?= $p->ser_type ?? 'producto' ?></span>
                <div class="product-actions">
                    <a class="product-wa" href="<?= htmlspecialchars($wa_href, ENT_QUOTES, 'UTF-8') ?>" target="_blank" onclick="event.stopPropagation();">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="empty-state">
            <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
            <p>No hay productos disponibles aún</p>
        </div>
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
var currentOrder = 'price_asc';

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

function applyFilters() {
    var q = (document.getElementById('filterSearch').value || '').trim().toLowerCase();
    var order = currentOrder || 'price_asc';
    var cards = Array.prototype.slice.call(document.querySelectorAll('.product-card'));

    cards.forEach(function(c) { c.style.display = ''; });
    if (q) {
        cards.forEach(function(c) {
            var name = c.getAttribute('data-name') || '';
            if (name.indexOf(q) === -1) c.style.display = 'none';
        });
    }

    var visible = cards.filter(function(c) { return c.style.display !== 'none'; });
    visible.sort(function(a, b) {
        var pa = parseFloat(a.getAttribute('data-price') || '0');
        var pb = parseFloat(b.getAttribute('data-price') || '0');
        return order === 'price_desc' ? (pb - pa) : (pa - pb);
    });
    visible.forEach(function(c) { c.parentNode.appendChild(c); });
    document.getElementById('filterCount').textContent = visible.length + ' producto(s)';
}

document.addEventListener('DOMContentLoaded', function() {
    var hero = document.querySelector('.saas-hero');
    var heroImg = document.querySelector('.saas-hero__img');
    if (hero && heroImg) {
        heroImg.addEventListener('error', function() {
            heroImg.style.display = 'none';
            hero.classList.remove('has-cover');
            hero.classList.add('no-cover');
        });
    }
    var avatar = document.querySelector('.saas-avatar');
    var avatarImg = avatar ? avatar.querySelector('img') : null;
    if (avatar && avatarImg) {
        avatarImg.addEventListener('error', function() {
            avatarImg.style.display = 'none';
            avatar.classList.remove('has-img');
            avatar.classList.add('no-img');
        });
    }
    var s = document.getElementById('filterSearch');
    if (s) s.addEventListener('input', applyFilters);
    Array.prototype.slice.call(document.querySelectorAll('.pf-btn')).forEach(function(btn) {
        btn.addEventListener('click', function() {
            var order = btn.getAttribute('data-order') || 'price_asc';
            currentOrder = order;
            Array.prototype.slice.call(document.querySelectorAll('.pf-btn')).forEach(function(b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            applyFilters();
        });
    });
    applyFilters();
});
</script>

</body>
</html>

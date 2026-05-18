<?php
$status_columns = [
    'nuevo' => ['title' => 'Pedidos nuevos', 'tone' => 'indigo'],
    'en_proceso' => ['title' => 'En proceso', 'tone' => 'amber'],
    'completado' => ['title' => 'Finalizados', 'tone' => 'green'],
    'cancelado' => ['title' => 'Cancelados', 'tone' => 'red'],
];

$grouped = [
    'nuevo' => [],
    'en_proceso' => [],
    'completado' => [],
    'cancelado' => [],
];

$orders_payload = [];
if (!empty($orders)) {
    foreach ($orders as $o) {
        $status = $o->bo_status ?: 'nuevo';
        if (!isset($grouped[$status])) $status = 'nuevo';
        $grouped[$status][] = $o;

        $orders_payload[(int)$o->bo_id] = [
            'bo_id' => (int)$o->bo_id,
            'bo_customer_name' => (string)($o->bo_customer_name ?? ''),
            'bo_customer_phone' => (string)($o->bo_customer_phone ?? ''),
            'bo_customer_phone_digits' => preg_replace('/[^0-9]/', '', (string)($o->bo_customer_phone ?? '')),
            'bo_product_name' => (string)($o->bo_product_name ?? ''),
            'bo_quantity' => (int)($o->bo_quantity ?? 1),
            'bo_message' => (string)($o->bo_message ?? ''),
            'bo_status' => (string)$status,
            'created_at' => (string)($o->created_at ?? ''),
        ];
    }
}
?>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header" style="display:flex; align-items:flex-end; justify-content:space-between; gap: 1rem;">
        <div>
            <h4 style="margin-bottom: 0.25rem;">Pedidos</h4>
            <p style="margin: 0; color: var(--saas-gray-500);">Gestión rápida de pedidos recibidos por WhatsApp</p>
        </div>
        <div class="d-none d-md-flex" style="gap: 0.5rem; align-items:center;">
            <span class="wapi-orders-pill"><i class="feather icon-clock"></i> Últimos 50</span>
        </div>
    </div>

    <?php if(!empty($orders)): ?>
        <div class="wapi-orders-tabs" role="tablist" aria-label="Estados de pedidos">
            <?php foreach ($status_columns as $key => $meta): ?>
                <button type="button"
                        class="wapi-tab"
                        role="tab"
                        data-status="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        aria-selected="<?= $key === 'nuevo' ? 'true' : 'false' ?>">
                    <span class="wapi-tab__dot wapi-tone-<?= htmlspecialchars($meta['tone'], ENT_QUOTES, 'UTF-8') ?>"></span>
                    <span class="wapi-tab__label"><?= htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="wapi-tab__count" data-count-for="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"><?= count($grouped[$key]) ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="wapi-orders-board" data-active-status="nuevo">
            <?php foreach ($status_columns as $key => $meta): ?>
                <section class="wapi-kanban-col"
                         data-status="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                         aria-label="<?= htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="wapi-kanban-col__header">
                        <div class="wapi-kanban-col__title">
                            <span class="wapi-col-dot wapi-tone-<?= htmlspecialchars($meta['tone'], ENT_QUOTES, 'UTF-8') ?>"></span>
                            <span><?= htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="wapi-kanban-col__count" data-count-for="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"><?= count($grouped[$key]) ?></div>
                    </div>

                    <div class="wapi-kanban-list" id="wapi-col-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" data-status="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (!empty($grouped[$key])): ?>
                            <?php foreach ($grouped[$key] as $o): ?>
                                <?php
                                    $customer_name = trim((string)($o->bo_customer_name ?? '')) ?: 'Sin nombre';
                                    $phone = trim((string)($o->bo_customer_phone ?? '')) ?: '—';
                                    $phone_digits = preg_replace('/[^0-9]/', '', (string)($o->bo_customer_phone ?? ''));
                                    $product = trim((string)($o->bo_product_name ?? '')) ?: '—';
                                    $qty = (int)($o->bo_quantity ?? 1);
                                    $msg = trim((string)($o->bo_message ?? ''));
                                    $created = $o->created_at ? date('d/m H:i', strtotime($o->created_at)) : '—';
                                    $status_value = $o->bo_status ?: 'nuevo';
                                ?>
                                <article class="wapi-order-card"
                                         tabindex="0"
                                         role="button"
                                         data-bo-id="<?= (int)$o->bo_id ?>"
                                         data-status="<?= htmlspecialchars($status_value, ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="wapi-order-card__top">
                                        <div class="wapi-order-card__title">
                                            <span class="wapi-order-card__name"><?= htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="wapi-status-pill" data-status-pill data-status="<?= htmlspecialchars($status_value, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($status_columns[$key]['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="wapi-order-card__tools">
                                            <button type="button" class="wapi-icon-btn wapi-order-card__drag" aria-label="Arrastrar">
                                                <i class="feather icon-move"></i>
                                            </button>
                                            <a class="wapi-icon-btn wapi-icon-btn--wa"
                                               href="https://wa.me/<?= htmlspecialchars($phone_digits, ENT_QUOTES, 'UTF-8') ?>"
                                               target="_blank"
                                               rel="noopener"
                                               aria-label="Abrir WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="wapi-order-card__meta">
                                        <div class="wapi-order-card__line">
                                            <span class="wapi-muted"><i class="feather icon-phone"></i> <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="wapi-order-card__line">
                                            <span class="wapi-strong"><?= htmlspecialchars($product, ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="wapi-qty">x<?= (int)$qty ?></span>
                                        </div>
                                    </div>

                                    <div class="wapi-order-card__msg"><?= htmlspecialchars($msg ?: '—', ENT_QUOTES, 'UTF-8') ?></div>

                                    <div class="wapi-order-card__footer">
                                        <span class="wapi-muted"><i class="feather icon-calendar"></i> <?= htmlspecialchars($created, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="wapi-order-card__id">#<?= (int)$o->bo_id ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="wapi-empty-col">
                                <i class="feather icon-inbox"></i>
                                <div class="wapi-empty-col__title">Sin pedidos</div>
                                <div class="wapi-empty-col__desc">No hay pedidos en este estado</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="wapi-drawer" id="wapiOrderDrawer" aria-hidden="true">
            <div class="wapi-drawer__backdrop" data-action="close"></div>
            <div class="wapi-drawer__panel" role="dialog" aria-modal="true" aria-label="Detalle del pedido">
                <div class="wapi-drawer__header">
                    <div>
                        <div class="wapi-drawer__title" id="wapiDrawerTitle">Pedido</div>
                        <div class="wapi-drawer__subtitle" id="wapiDrawerSubtitle"></div>
                    </div>
                    <button type="button" class="wapi-icon-btn" data-action="close" aria-label="Cerrar">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="wapi-drawer__body">
                    <div class="wapi-drawer__section">
                        <div class="wapi-drawer__section-title">Cliente</div>
                        <div class="wapi-kv">
                            <div class="wapi-kv__row"><div class="wapi-kv__k">Nombre</div><div class="wapi-kv__v" id="wapiD_customer"></div></div>
                            <div class="wapi-kv__row"><div class="wapi-kv__k">WhatsApp</div><div class="wapi-kv__v" id="wapiD_phone"></div></div>
                        </div>
                    </div>

                    <div class="wapi-drawer__section">
                        <div class="wapi-drawer__section-title">Pedido</div>
                        <div class="wapi-kv">
                            <div class="wapi-kv__row"><div class="wapi-kv__k">Producto</div><div class="wapi-kv__v" id="wapiD_product"></div></div>
                            <div class="wapi-kv__row"><div class="wapi-kv__k">Cantidad</div><div class="wapi-kv__v" id="wapiD_qty"></div></div>
                            <div class="wapi-kv__row"><div class="wapi-kv__k">Fecha</div><div class="wapi-kv__v" id="wapiD_date"></div></div>
                            <div class="wapi-kv__row"><div class="wapi-kv__k">Estado</div><div class="wapi-kv__v"><span class="wapi-status-pill" id="wapiD_status"></span></div></div>
                        </div>
                    </div>

                    <div class="wapi-drawer__section">
                        <div class="wapi-drawer__section-title">Mensaje</div>
                        <div class="wapi-message" id="wapiD_msg"></div>
                    </div>
                </div>

                <div class="wapi-drawer__footer">
                    <button type="button" class="wapi-btn wapi-btn--secondary" id="wapiBtnWa">
                        <i class="fab fa-whatsapp"></i> Abrir WhatsApp
                    </button>
                    <button type="button" class="wapi-btn wapi-btn--ghost" id="wapiBtnProcess">
                        <i class="feather icon-play"></i> En proceso
                    </button>
                    <button type="button" class="wapi-btn wapi-btn--primary" id="wapiBtnDone">
                        <i class="feather icon-check"></i> Finalizar
                    </button>
                    <button type="button" class="wapi-btn wapi-btn--danger" id="wapiBtnCancel">
                        <i class="feather icon-slash"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 3rem;">
                <i class="feather icon-shopping-bag" style="font-size: 3rem; color: var(--saas-gray-300);"></i>
                <h5 style="margin-top: 1rem; color: var(--saas-gray-500);">No hay pedidos aún</h5>
                <p style="color: var(--saas-gray-400); font-size: 0.875rem;">Cuando el bot reciba pedidos de WhatsApp, aparecerán aquí</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.wapi-orders-pill{
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.45rem .65rem; border:1px solid var(--saas-gray-200);
    border-radius:999px; font-size:.8125rem; color:var(--saas-gray-600);
    background: #fff;
}
.wapi-orders-tabs{
    display:flex; gap:.5rem; overflow:auto; padding:.5rem .25rem .75rem;
    -webkit-overflow-scrolling: touch;
}
@media (min-width: 992px){
    .wapi-orders-tabs{ display:none; }
}
.wapi-tab{
    appearance:none; border:1px solid var(--saas-gray-200);
    background:#fff; border-radius:999px;
    padding:.5rem .75rem; display:flex; align-items:center; gap:.5rem;
    color:var(--saas-gray-700); font-size:.875rem; white-space:nowrap;
    box-shadow: 0 1px 0 rgba(16,24,40,.04);
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
}
.wapi-tab:active{ transform: translateY(1px); }
.wapi-tab[aria-selected="true"]{
    border-color: rgba(99,102,241,.35);
    box-shadow: 0 10px 30px rgba(99,102,241,.12);
}
.wapi-tab__dot{ width:10px; height:10px; border-radius:999px; display:inline-block; }
.wapi-tab__count{
    margin-left:.15rem;
    padding:.15rem .45rem; border-radius:999px;
    font-size:.75rem; color:var(--saas-gray-600); background:var(--saas-gray-100);
    border:1px solid var(--saas-gray-200);
}

.wapi-orders-board{
    display:grid;
    grid-template-columns: repeat(4, minmax(260px, 1fr));
    gap: 1rem;
    align-items:start;
}
@media (max-width: 991.98px){
    .wapi-orders-board{ grid-template-columns: 1fr; }
    .wapi-kanban-col{ display:none; }
    .wapi-orders-board[data-active-status="nuevo"] .wapi-kanban-col[data-status="nuevo"],
    .wapi-orders-board[data-active-status="en_proceso"] .wapi-kanban-col[data-status="en_proceso"],
    .wapi-orders-board[data-active-status="completado"] .wapi-kanban-col[data-status="completado"],
    .wapi-orders-board[data-active-status="cancelado"] .wapi-kanban-col[data-status="cancelado"]{ display:block; }
}

.wapi-kanban-col{
    background: linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.8));
    border: 1px solid var(--saas-gray-200);
    border-radius: 16px;
    box-shadow: 0 1px 0 rgba(16,24,40,.04);
    overflow:hidden;
}
.wapi-kanban-col__header{
    position: sticky; top: 0;
    background: rgba(255,255,255,.85);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--saas-gray-200);
    display:flex; align-items:center; justify-content:space-between;
    padding: .75rem .85rem;
    z-index: 1;
}
.wapi-kanban-col__title{ display:flex; align-items:center; gap:.5rem; font-weight: 600; color: var(--saas-gray-800); }
.wapi-kanban-col__count{
    font-size:.75rem; color: var(--saas-gray-600);
    padding:.15rem .45rem; border-radius:999px;
    background: var(--saas-gray-100); border:1px solid var(--saas-gray-200);
}
.wapi-kanban-list{
    padding: .75rem;
    display:flex; flex-direction:column; gap:.65rem;
    min-height: 180px;
}

.wapi-col-dot{ width:10px; height:10px; border-radius:999px; display:inline-block; }
.wapi-tone-indigo{ background: rgba(99,102,241,.85); box-shadow: 0 0 0 4px rgba(99,102,241,.12); }
.wapi-tone-amber{ background: rgba(245,158,11,.85); box-shadow: 0 0 0 4px rgba(245,158,11,.12); }
.wapi-tone-green{ background: rgba(16,185,129,.85); box-shadow: 0 0 0 4px rgba(16,185,129,.12); }
.wapi-tone-red{ background: rgba(239,68,68,.85); box-shadow: 0 0 0 4px rgba(239,68,68,.12); }

.wapi-order-card{
    border: 1px solid var(--saas-gray-200);
    border-radius: 14px;
    background: #fff;
    padding: .75rem .75rem .65rem;
    cursor: pointer;
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    box-shadow: 0 1px 0 rgba(16,24,40,.04);
    outline: none;
}
.wapi-order-card:hover{
    transform: translateY(-1px);
    border-color: rgba(99,102,241,.25);
    box-shadow: 0 18px 40px rgba(16,24,40,.08);
}
.wapi-order-card:focus-visible{
    box-shadow: 0 0 0 4px rgba(99,102,241,.18), 0 18px 40px rgba(16,24,40,.08);
    border-color: rgba(99,102,241,.45);
}
.wapi-order-card__top{
    display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem;
    margin-bottom: .5rem;
}
.wapi-order-card__title{ display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.wapi-order-card__name{ font-weight: 700; color: var(--saas-gray-900); font-size: .95rem; }
.wapi-order-card__tools{ display:flex; gap:.35rem; align-items:center; flex-shrink: 0; }
.wapi-icon-btn{
    display:inline-flex; align-items:center; justify-content:center;
    width: 34px; height: 34px;
    border-radius: 10px;
    border: 1px solid var(--saas-gray-200);
    background: #fff;
    color: var(--saas-gray-700);
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    text-decoration:none;
}
.wapi-icon-btn:hover{ border-color: var(--saas-gray-300); box-shadow: 0 10px 22px rgba(16,24,40,.08); transform: translateY(-1px); }
.wapi-icon-btn:active{ transform: translateY(0); }
.wapi-icon-btn--wa{ color: #16a34a; }

.wapi-status-pill{
    display:inline-flex; align-items:center;
    padding:.18rem .5rem;
    border-radius: 999px;
    border: 1px solid var(--saas-gray-200);
    background: var(--saas-gray-50);
    color: var(--saas-gray-700);
    font-size: .75rem;
    font-weight: 600;
}
.wapi-status-pill[data-status="nuevo"]{ background: rgba(99,102,241,.10); border-color: rgba(99,102,241,.22); color: rgba(67,56,202,1); }
.wapi-status-pill[data-status="en_proceso"]{ background: rgba(245,158,11,.12); border-color: rgba(245,158,11,.25); color: rgba(146,64,14,1); }
.wapi-status-pill[data-status="completado"]{ background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.22); color: rgba(4,120,87,1); }
.wapi-status-pill[data-status="cancelado"]{ background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.22); color: rgba(185,28,28,1); }
.wapi-order-card__meta{ display:flex; flex-direction:column; gap:.35rem; }
.wapi-order-card__line{ display:flex; align-items:center; justify-content:space-between; gap:.5rem; }
.wapi-muted{ color: var(--saas-gray-600); font-size: .8125rem; display:inline-flex; align-items:center; gap:.35rem; }
.wapi-strong{ color: var(--saas-gray-900); font-weight: 600; font-size: .875rem; }
.wapi-qty{
    color: var(--saas-gray-700);
    font-size:.75rem; font-weight:600;
    padding:.12rem .4rem;
    border-radius:999px;
    border: 1px solid var(--saas-gray-200);
    background: var(--saas-gray-50);
}
.wapi-order-card__msg{
    margin-top: .55rem;
    color: var(--saas-gray-700);
    font-size: .8125rem;
    line-height: 1.35;
    display:-webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow:hidden;
    min-height: 2.2em;
}
.wapi-order-card__footer{
    margin-top: .65rem;
    display:flex; align-items:center; justify-content:space-between; gap:.5rem;
}
.wapi-order-card__id{
    color: var(--saas-gray-500);
    font-size:.75rem;
    font-weight: 600;
}

.wapi-empty-col{
    border: 1px dashed var(--saas-gray-200);
    border-radius: 14px;
    padding: 1.25rem;
    text-align:center;
    color: var(--saas-gray-600);
    background: rgba(255,255,255,.6);
}
.wapi-empty-col i{ font-size: 1.5rem; color: var(--saas-gray-400); }
.wapi-empty-col__title{ margin-top:.5rem; font-weight: 700; color: var(--saas-gray-700); }
.wapi-empty-col__desc{ margin-top:.15rem; font-size:.8125rem; color: var(--saas-gray-500); }

.wapi-drawer{
    position: fixed; inset: 0;
    display:none;
    z-index: 10000;
}
.wapi-drawer[aria-hidden="false"]{ display:block; }
.wapi-drawer__backdrop{
    position:absolute; inset:0;
    background: rgba(15,23,42,.45);
    backdrop-filter: blur(4px);
}
.wapi-drawer__panel{
    position:absolute; top:0; right:0; height:100%;
    width: min(520px, 100%);
    background: #fff;
    border-left: 1px solid var(--saas-gray-200);
    box-shadow: -20px 0 60px rgba(15,23,42,.18);
    display:flex; flex-direction:column;
    transform: translateX(8px);
    animation: wapiDrawerIn .16s ease forwards;
}
@keyframes wapiDrawerIn { to { transform: translateX(0); } }
.wapi-drawer__header{
    display:flex; align-items:flex-start; justify-content:space-between; gap:1rem;
    padding: 1rem 1rem .75rem;
    border-bottom: 1px solid var(--saas-gray-200);
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(8px);
}
.wapi-drawer__title{ font-weight: 800; color: var(--saas-gray-900); font-size: 1.05rem; }
.wapi-drawer__subtitle{ color: var(--saas-gray-600); font-size: .8125rem; margin-top:.15rem; }
.wapi-drawer__body{
    padding: 1rem;
    overflow:auto;
    -webkit-overflow-scrolling: touch;
    display:flex; flex-direction:column; gap: .9rem;
}
.wapi-drawer__section{
    border: 1px solid var(--saas-gray-200);
    border-radius: 14px;
    background: var(--saas-gray-0, #fff);
    padding: .9rem;
}
.wapi-drawer__section-title{
    font-weight: 800;
    color: var(--saas-gray-800);
    font-size: .875rem;
    margin-bottom: .65rem;
}
.wapi-kv{ display:flex; flex-direction:column; gap:.5rem; }
.wapi-kv__row{ display:flex; justify-content:space-between; gap:1rem; align-items:flex-start; }
.wapi-kv__k{ color: var(--saas-gray-500); font-size:.8125rem; }
.wapi-kv__v{ color: var(--saas-gray-900); font-weight: 600; font-size:.875rem; text-align:right; }
.wapi-message{
    white-space: pre-wrap;
    color: var(--saas-gray-800);
    font-size: .875rem;
    line-height: 1.45;
    background: var(--saas-gray-50);
    border: 1px solid var(--saas-gray-200);
    border-radius: 12px;
    padding: .75rem;
}
.wapi-drawer__footer{
    padding: .75rem 1rem 1rem;
    border-top: 1px solid var(--saas-gray-200);
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(8px);
    display:flex; gap:.5rem; flex-wrap:wrap;
}
.wapi-btn{
    appearance:none; border:1px solid transparent;
    border-radius: 12px;
    padding: .6rem .8rem;
    font-weight: 700;
    font-size: .875rem;
    display:inline-flex; align-items:center; gap:.5rem;
    transition: transform .12s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;
}
.wapi-btn:active{ transform: translateY(1px); }
.wapi-btn--primary{ background: rgba(99,102,241,1); color:#fff; box-shadow: 0 14px 28px rgba(99,102,241,.22); }
.wapi-btn--primary:hover{ box-shadow: 0 18px 36px rgba(99,102,241,.28); }
.wapi-btn--secondary{ background:#16a34a; color:#fff; box-shadow: 0 14px 28px rgba(22,163,74,.22); }
.wapi-btn--secondary:hover{ box-shadow: 0 18px 36px rgba(22,163,74,.28); }
.wapi-btn--danger{ background: rgba(239,68,68,1); color:#fff; box-shadow: 0 14px 28px rgba(239,68,68,.18); }
.wapi-btn--danger:hover{ box-shadow: 0 18px 36px rgba(239,68,68,.22); }
.wapi-btn--ghost{ background:#fff; color: var(--saas-gray-800); border-color: var(--saas-gray-200); }
.wapi-btn--ghost:hover{ border-color: var(--saas-gray-300); box-shadow: 0 14px 28px rgba(16,24,40,.08); }

.sortable-ghost{ opacity: .6; }
.sortable-chosen{ box-shadow: 0 20px 40px rgba(16,24,40,.12); }
</style>

<script>
(function(){
    var ordersById = <?php echo json_encode($orders_payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> || {};
    var statusMeta = {
        nuevo: { label: 'Pedidos nuevos' },
        en_proceso: { label: 'En proceso' },
        completado: { label: 'Finalizados' },
        cancelado: { label: 'Cancelados' }
    };

    var board = document.querySelector('.wapi-orders-board');
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.wapi-tab'));
    var drawer = document.getElementById('wapiOrderDrawer');

    function setActiveStatus(status){
        if (!board) return;
        board.setAttribute('data-active-status', status);
        tabs.forEach(function(btn){
            btn.setAttribute('aria-selected', btn.getAttribute('data-status') === status ? 'true' : 'false');
        });
    }

    tabs.forEach(function(btn){
        btn.addEventListener('click', function(){
            setActiveStatus(btn.getAttribute('data-status'));
        });
    });

    function setLoading(on){
        var el = document.querySelector('.loading');
        if (!el) return;
        el.style.display = on ? 'flex' : 'none';
    }

    function apiUpdateStatus(bo_id, status){
        return fetch(base_url + 'Orders/update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bo_id: bo_id, status: status })
        }).then(function(r){ return r.json(); });
    }

    function syncCounts(){
        Object.keys(statusMeta).forEach(function(status){
            var count = document.querySelectorAll('.wapi-order-card[data-status="' + status + '"]').length;
            Array.prototype.slice.call(document.querySelectorAll('[data-count-for="' + status + '"]')).forEach(function(el){
                el.textContent = String(count);
            });
        });
    }

    function ensureEmptyStates(){
        Array.prototype.slice.call(document.querySelectorAll('.wapi-kanban-list')).forEach(function(list){
            var status = list.getAttribute('data-status');
            var hasCards = list.querySelector('.wapi-order-card');
            var empty = list.querySelector('.wapi-empty-col');
            if (hasCards && empty) empty.remove();
            if (!hasCards && !empty) {
                var wrapper = document.createElement('div');
                wrapper.className = 'wapi-empty-col';
                wrapper.innerHTML = '<i class="feather icon-inbox"></i><div class="wapi-empty-col__title">Sin pedidos</div><div class="wapi-empty-col__desc">No hay pedidos en este estado</div>';
                list.appendChild(wrapper);
            }
        });
    }

    function getOrder(bo_id){
        return ordersById[String(bo_id)] || null;
    }

    function formatCreatedAt(created_at){
        if (!created_at) return '—';
        var d = new Date(created_at.replace(' ', 'T'));
        if (isNaN(d.getTime())) return created_at;
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var hh = String(d.getHours()).padStart(2, '0');
        var mi = String(d.getMinutes()).padStart(2, '0');
        return dd + '/' + mm + ' ' + hh + ':' + mi;
    }

    function closeDrawer(){
        if (!drawer) return;
        drawer.setAttribute('aria-hidden', 'true');
        drawer.removeAttribute('data-bo-id');
        document.body.style.overflow = '';
    }

    function openDrawer(bo_id){
        var o = getOrder(bo_id);
        if (!o || !drawer) return;

        drawer.setAttribute('aria-hidden', 'false');
        drawer.setAttribute('data-bo-id', String(bo_id));
        document.body.style.overflow = 'hidden';

        document.getElementById('wapiDrawerTitle').textContent = 'Pedido #' + String(o.bo_id);
        document.getElementById('wapiDrawerSubtitle').textContent = statusMeta[o.bo_status] ? statusMeta[o.bo_status].label : o.bo_status;

        document.getElementById('wapiD_customer').textContent = o.bo_customer_name || 'Sin nombre';
        document.getElementById('wapiD_phone').textContent = o.bo_customer_phone || '—';
        document.getElementById('wapiD_product').textContent = o.bo_product_name || '—';
        document.getElementById('wapiD_qty').textContent = String(o.bo_quantity || 1);
        document.getElementById('wapiD_date').textContent = formatCreatedAt(o.created_at);
        document.getElementById('wapiD_msg').textContent = o.bo_message || '—';

        var statusEl = document.getElementById('wapiD_status');
        statusEl.textContent = statusMeta[o.bo_status] ? statusMeta[o.bo_status].label : o.bo_status;
        statusEl.setAttribute('data-status', o.bo_status);

        var waBtn = document.getElementById('wapiBtnWa');
        waBtn.disabled = !o.bo_customer_phone_digits;
        waBtn.onclick = function(){
            if (!o.bo_customer_phone_digits) return;
            window.open('https://wa.me/' + o.bo_customer_phone_digits, '_blank', 'noopener');
        };

        var btnProcess = document.getElementById('wapiBtnProcess');
        var btnDone = document.getElementById('wapiBtnDone');
        var btnCancel = document.getElementById('wapiBtnCancel');

        btnProcess.disabled = o.bo_status === 'en_proceso' || o.bo_status === 'completado' || o.bo_status === 'cancelado';
        btnDone.disabled = o.bo_status === 'completado' || o.bo_status === 'cancelado';
        btnCancel.disabled = o.bo_status === 'cancelado';

        btnProcess.onclick = function(){ updateOrderStatus(o.bo_id, 'en_proceso'); };
        btnDone.onclick = function(){ updateOrderStatus(o.bo_id, 'completado'); };
        btnCancel.onclick = function(){ updateOrderStatus(o.bo_id, 'cancelado'); };
    }

    if (drawer) {
        Array.prototype.slice.call(drawer.querySelectorAll('[data-action="close"]')).forEach(function(el){
            el.addEventListener('click', closeDrawer);
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') closeDrawer();
        });
    }

    function moveCardToStatus(bo_id, status){
        var card = document.querySelector('.wapi-order-card[data-bo-id="' + String(bo_id) + '"]');
        if (!card) return;
        var targetList = document.querySelector('.wapi-kanban-list[data-status="' + status + '"]');
        if (!targetList) return;

        card.setAttribute('data-status', status);
        var pill = card.querySelector('[data-status-pill]');
        if (pill) {
            pill.setAttribute('data-status', status);
            pill.textContent = statusMeta[status] ? statusMeta[status].label : status;
        }
        targetList.insertBefore(card, targetList.firstChild);

        var o = getOrder(bo_id);
        if (o) o.bo_status = status;

        syncCounts();
        ensureEmptyStates();
        openDrawer(bo_id);
    }

    window.updateOrderStatus = function(bo_id, status){
        setLoading(true);
        return apiUpdateStatus(bo_id, status)
            .then(function(r){
                setLoading(false);
                if (!r || !r.status) {
                    Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: (r && r.message) ? r.message : 'Intenta de nuevo' });
                    return false;
                }
                Swal.fire({ icon: 'success', title: 'Actualizado', timer: 900, showConfirmButton: false });
                moveCardToStatus(bo_id, status);
                return true;
            })
            .catch(function(){
                setLoading(false);
                Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar con el servidor' });
                return false;
            });
    };

    Array.prototype.slice.call(document.querySelectorAll('.wapi-order-card')).forEach(function(card){
        card.addEventListener('click', function(e){
            var isAction = e.target.closest && (e.target.closest('a') || e.target.closest('button'));
            if (isAction) return;
            openDrawer(card.getAttribute('data-bo-id'));
        });
        card.addEventListener('keydown', function(e){
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openDrawer(card.getAttribute('data-bo-id'));
            }
        });
        Array.prototype.slice.call(card.querySelectorAll('a')).forEach(function(a){
            a.addEventListener('click', function(e){ e.stopPropagation(); });
        });
        Array.prototype.slice.call(card.querySelectorAll('button')).forEach(function(b){
            b.addEventListener('click', function(e){ e.stopPropagation(); });
        });
    });

    Array.prototype.slice.call(document.querySelectorAll('.wapi-kanban-list')).forEach(function(list){
        if (typeof Sortable === 'undefined') return;
        Sortable.create(list, {
            group: 'wapi-orders',
            animation: 150,
            draggable: '.wapi-order-card',
            handle: '.wapi-order-card__drag',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt){
                if (evt.from === evt.to) return;
                var card = evt.item;
                var bo_id = card.getAttribute('data-bo-id');
                var newStatus = evt.to.getAttribute('data-status');
                var prevStatus = evt.from.getAttribute('data-status');

                card.setAttribute('data-status', newStatus);
                var pill = card.querySelector('[data-status-pill]');
                if (pill) {
                    pill.setAttribute('data-status', newStatus);
                    pill.textContent = statusMeta[newStatus] ? statusMeta[newStatus].label : newStatus;
                }
                syncCounts();
                ensureEmptyStates();

                setLoading(true);
                apiUpdateStatus(bo_id, newStatus)
                    .then(function(r){
                        setLoading(false);
                        if (!r || !r.status) {
                            evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                            card.setAttribute('data-status', prevStatus);
                            var pill = card.querySelector('[data-status-pill]');
                            if (pill) {
                                pill.setAttribute('data-status', prevStatus);
                                pill.textContent = statusMeta[prevStatus] ? statusMeta[prevStatus].label : prevStatus;
                            }
                            syncCounts();
                            ensureEmptyStates();
                            Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: (r && r.message) ? r.message : 'Intenta de nuevo' });
                            return;
                        }
                        var o = getOrder(bo_id);
                        if (o) o.bo_status = newStatus;
                        ensureEmptyStates();
                        Swal.fire({ icon: 'success', title: 'Actualizado', timer: 700, showConfirmButton: false });
                    })
                    .catch(function(){
                        setLoading(false);
                        evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                        card.setAttribute('data-status', prevStatus);
                        var pill = card.querySelector('[data-status-pill]');
                        if (pill) {
                            pill.setAttribute('data-status', prevStatus);
                            pill.textContent = statusMeta[prevStatus] ? statusMeta[prevStatus].label : prevStatus;
                        }
                        syncCounts();
                        ensureEmptyStates();
                        Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar con el servidor' });
                    });
            }
        });
    });

    syncCounts();
    ensureEmptyStates();
    setActiveStatus('nuevo');
})();
</script>

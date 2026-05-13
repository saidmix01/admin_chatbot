<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Pedidos</h4>
        <p>Pedidos recibidos desde WhatsApp</p>
    </div>

    <?php if(!empty($orders)): ?>
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive-wrap">
                <table class="table-saas table-saas-card-mode">
                    <thead>
                        <tr>
                            <th>#</th><th>Cliente</th><th>Teléfono</th><th>Producto</th><th>Cant.</th><th>Mensaje</th><th>Estado</th><th>Fecha</th><th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td data-label="#"><?= $o->bo_id ?></td>
                            <td data-label="Cliente"><?= $o->bo_customer_name ?: '—' ?></td>
                            <td data-label="Teléfono"><?= $o->bo_customer_phone ?: '—' ?></td>
                            <td data-label="Producto"><?= $o->bo_product_name ?: '—' ?></td>
                            <td data-label="Cant."><?= $o->bo_quantity ?></td>
                            <td data-label="Mensaje" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $o->bo_message ?: '—' ?></td>
                            <td data-label="Estado">
                                <select class="form-saas" style="font-size: 0.75rem; padding: 0.2rem 0.4rem; width: auto;" onchange="updateOrderStatus(<?= $o->bo_id ?>, this.value)">
                                    <option value="nuevo" <?= $o->bo_status == 'nuevo' ? 'selected' : '' ?>>Nuevo</option>
                                    <option value="en_proceso" <?= $o->bo_status == 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                                    <option value="completado" <?= $o->bo_status == 'completado' ? 'selected' : '' ?>>Completado</option>
                                    <option value="cancelado" <?= $o->bo_status == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </td>
                            <td data-label="Fecha"><?= date('d/m H:i', strtotime($o->created_at)) ?></td>
                            <td data-label="Acción">
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $o->bo_customer_phone) ?>" target="_blank" class="btn-saas btn-saas-success btn-saas-sm">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

<script>
function updateOrderStatus(id, status) {
    document.querySelector('.loading').style.display = 'flex';
    fetch(base_url + 'Orders/update_status', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bo_id: id, status: status })
    })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            Swal.fire({ icon: 'success', title: 'Actualizado', timer: 1000 });
        }
    })
    .catch(function() {
        document.querySelector('.loading').style.display = 'none';
    });
}
</script>
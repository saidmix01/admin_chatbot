<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Productos / Servicios</h4>
        <p>Administra tu catálogo de productos</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div style="font-size: 0.875rem; color: var(--saas-gray-500);">
            <?= count($products) ?> producto(s) registrados
        </div>
        <button class="btn-saas btn-saas-primary" onclick="openProductModal()">
            <i class="feather icon-plus"></i> Nuevo producto
        </button>
    </div>

    <!-- Products Grid -->
    <?php if(!empty($products)): ?>
    <div class="product-grid">
        <?php foreach($products as $p): ?>
        <div class="product-card">
            <div class="product-card-img" style="display: flex; align-items: center; justify-content: center; color: var(--saas-gray-300); font-size: 2rem; background: var(--saas-gray-50);">
                <?php if(!empty($p->ser_imagen)): ?>
                    <img src="<?= $p->ser_imagen ?>" alt="<?= $p->ser_name ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="feather icon-image"></i>
                <?php endif; ?>
            </div>
            <div class="product-card-body">
                <div class="product-card-title"><?= $p->ser_name ?></div>
                <div class="product-card-price">$<?= number_format($p->ser_price ?? 0, 0) ?></div>
                <div class="product-card-desc"><?= substr($p->ser_description ?? '', 0, 80) ?></div>
                <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                    <span class="status-badge <?= ($p->ser_status ?? 1) == 1 ? 'connected' : 'disconnected' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                        <?= ($p->ser_status ?? 1) == 1 ? 'Disponible' : 'No disponible' ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 3rem;">
            <i class="feather icon-package" style="font-size: 3rem; color: var(--saas-gray-300);"></i>
            <h5 style="margin-top: 1rem; color: var(--saas-gray-500);">No hay productos aún</h5>
            <p style="color: var(--saas-gray-400); font-size: 0.875rem;">Agrega tu primer producto para mostrarlo en tu página pública</p>
            <button class="btn-saas btn-saas-primary" onclick="openProductModal()" style="margin-top: 0.5rem;">
                <i class="feather icon-plus"></i> Agregar producto
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--saas-radius); border: none;">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100); padding: 1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600;">Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <form id="form_product">
                    <div class="form-saas-group">
                        <label class="form-saas-label">Nombre del producto</label>
                        <input type="text" class="form-saas" name="name" required>
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Precio</label>
                        <input type="number" class="form-saas" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Descripción</label>
                        <textarea class="form-saas" name="description" rows="2"></textarea>
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Imagen URL</label>
                        <input type="text" class="form-saas" name="image" placeholder="https://...">
                    </div>
                    <div class="form-saas-group">
                        <label class="toggle-switch" style="display: flex; align-items: center; gap: 0.75rem; width: auto;">
                            <input type="checkbox" name="available" checked>
                            <span class="toggle-slider" style="position: relative; display: inline-block;"></span>
                            <span style="font-size: 0.875rem; color: var(--saas-gray-700);">Disponible</span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--saas-gray-100); padding: 1rem 1.5rem;">
                <button class="btn-saas btn-saas-outline" data-dismiss="modal">Cancelar</button>
                <button class="btn-saas btn-saas-primary" onclick="saveProduct()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
function openProductModal() {
    $('#productModal').modal('show');
}

function saveProduct() {
    // TODO: Save product via API
    Swal.fire({
        icon: 'success',
        title: 'Producto guardado',
        timer: 1500
    }).then(() => {
        $('#productModal').modal('hide');
        location.reload();
    });
}
</script>
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Productos / Servicios</h4>
        <p>Administra tu catálogo</p>
    </div>

    <div class="action-bar">
        <div style="font-size: 0.875rem; color: var(--saas-gray-500);">
            <?= count($products) ?> producto(s)
        </div>
        <button class="btn-saas btn-saas-primary" onclick="openModal()">
            <i class="feather icon-plus"></i> Nuevo
        </button>
    </div>

    <!-- Filters -->
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem; align-items: center;">
        <input type="text" id="filterSearch" placeholder="Buscar producto..." 
            style="flex: 1; min-width: 180px; padding: 0.5rem 0.75rem; border: 1px solid var(--saas-gray-300); border-radius: 8px; font-size: 0.875rem; outline: none;"
            onkeyup="filterProducts()"
            onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e5e7eb'">
        
        <select id="filterType" onchange="filterProducts()"
            style="padding: 0.5rem 0.75rem; border: 1px solid var(--saas-gray-300); border-radius: 8px; font-size: 0.875rem; outline: none; background: #fff; color: var(--saas-gray-700);">
            <option value="">Todos</option>
            <option value="producto">Productos</option>
            <option value="servicio">Servicios</option>
        </select>
        
        <select id="filterStatus" onchange="filterProducts()"
            style="padding: 0.5rem 0.75rem; border: 1px solid var(--saas-gray-300); border-radius: 8px; font-size: 0.875rem; outline: none; background: #fff; color: var(--saas-gray-700);">
            <option value="">Todos</option>
            <option value="1">Disponible</option>
            <option value="0">No disponible</option>
        </select>
        
        <span id="filterCount" style="font-size: 0.8125rem; color: var(--saas-gray-400);"></span>
    </div>
    
    <div class="product-grid" id="productGrid">
        <?php if(!empty($products)): ?>
        <?php foreach($products as $p): ?>
        <div class="product-card" onclick="editProduct(<?= $p->ser_id ?>)" style="cursor: pointer;">
            <div class="product-card-img" style="overflow: hidden; background: var(--saas-gray-50); position: relative;">
                <?php if(!empty($p->ser_imagen)): ?>
                    <img src="<?= base_url() . $p->ser_imagen ?>" alt="<?= $p->ser_name ?>" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                <?php else: ?>
                    <div style="height: 180px; display: flex; align-items: center; justify-content: center; color: var(--saas-gray-300); font-size: 2rem;">
                        <i class="feather icon-image"></i>
                    </div>
                <?php endif; ?>
                <span class="product-type-badge" style="position: absolute; top: 8px; right: 8px; background: var(--saas-primary); color: #fff; padding: 2px 8px; border-radius: 999px; font-size: 0.65rem; font-weight: 600; text-transform: uppercase;">
                    <?= $p->ser_type ?? 'producto' ?>
                </span>
            </div>
            <div class="product-card-body">
                <div class="product-card-title"><?= $p->ser_name ?></div>
                <div class="product-card-price">$<?= number_format($p->ser_price ?? 0, 0) ?></div>
                <div class="product-card-desc"><?= substr($p->ser_description ?? '', 0, 80) ?></div>
                <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; align-items: center;">
                    <span class="status-badge <?= ($p->ser_status ?? 1) == 1 ? 'connected' : 'disconnected' ?>" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <?= ($p->ser_status ?? 1) == 1 ? 'Disponible' : 'No disponible' ?>
                    </span>
                    <?php if(!empty($p->ser_category)): ?>
                    <span style="font-size: 0.7rem; color: var(--saas-gray-400);"><?= $p->ser_category ?></span>
                    <?php endif; ?>
                    <button class="btn-saas btn-saas-danger btn-saas-sm" onclick="event.stopPropagation(); deleteProduct(<?= $p->ser_id ?>, '<?= addslashes($p->ser_name) ?>')" style="margin-left: auto; padding: 0.2rem 0.5rem; font-size: 0.7rem;">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="card" style="grid-column: 1 / -1;">
            <div class="card-body" style="text-align: center; padding: 3rem;">
                <i class="feather icon-package" style="font-size: 3rem; color: var(--saas-gray-300);"></i>
                <h5 style="margin-top: 1rem; color: var(--saas-gray-500);">No hay productos aún</h5>
                <p style="color: var(--saas-gray-400); font-size: 0.875rem;">Agrega tu primer producto</p>
                <button class="btn-saas btn-saas-primary" onclick="openModal()">
                    <i class="feather icon-plus"></i> Agregar
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--saas-radius); border: none;">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100); padding: 1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600;" id="modalTitle">Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <form id="form_product" enctype="multipart/form-data">
                    <input type="hidden" name="ser_id" id="edit_ser_id" value="">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-saas-group">
                                <label class="form-saas-label">Nombre *</label>
                                <input type="text" class="form-saas" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-saas-group">
                                <label class="form-saas-label">Precio</label>
                                <input type="number" class="form-saas" name="price" id="edit_price" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-saas-group">
                                <label class="form-saas-label">Tipo</label>
                                <select class="form-saas" name="type" id="edit_type">
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-saas-group">
                                <label class="form-saas-label">Categoría</label>
                                <input type="text" class="form-saas" name="category" id="edit_category" placeholder="Ej: Bebidas, Cortes">
                            </div>
                        </div>
                    </div>

                    <div class="form-saas-group">
                        <label class="form-saas-label">Descripción</label>
                        <textarea class="form-saas" name="description" id="edit_description" rows="2"></textarea>
                    </div>

                    <div class="form-saas-group">
                        <label class="form-saas-label">Imagen</label>
                        <input type="file" class="form-saas" name="image" accept="image/jpeg,image/png,image/gif,image/webp" style="padding: 0.375rem;" id="edit_image">
                        <small style="color: var(--saas-gray-400);">JPG, PNG, WebP. Máx 2MB</small>
                        <div id="imagePreview" style="display: none; margin-top: 0.5rem; max-width: 150px; border-radius: 8px; overflow: hidden;">
                            <img src="" alt="" style="width: 100%; height: auto; display: block;">
                        </div>
                    </div>

                    <div class="form-saas-group">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="available" id="edit_available" checked style="width: 16px; height: 16px;">
                            <span style="font-size: 0.875rem; color: var(--saas-gray-700);">Disponible</span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--saas-gray-100); padding: 1rem 1.5rem;">
                <button class="btn-saas btn-saas-outline" data-dismiss="modal">Cancelar</button>
                <button class="btn-saas btn-saas-primary" id="modalSaveBtn" onclick="saveProduct()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
var editingId = null;

function openModal() {
    editingId = null;
    document.getElementById('form_product').reset();
    document.getElementById('edit_ser_id').value = '';
    document.getElementById('modalTitle').textContent = 'Nuevo Producto';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('edit_type').value = 'producto';
    document.getElementById('edit_available').checked = true;
    $('#productModal').modal('show');
}

function editProduct(id) {
    editingId = id;
    document.querySelector('.loading').style.display = 'flex';
    
    fetch(base_url + 'Products/get', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ser_id: id })
    })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (!r.status || !r.data) { Swal.fire('Error', 'No se pudo cargar el producto', 'error'); return; }
        
        var p = r.data;
        document.getElementById('modalTitle').textContent = 'Editar ' + p.ser_name;
        document.getElementById('edit_ser_id').value = p.ser_id;
        document.getElementById('edit_name').value = p.ser_name;
        document.getElementById('edit_price').value = p.ser_price;
        document.getElementById('edit_description').value = p.ser_description || '';
        document.getElementById('edit_type').value = p.ser_type || 'producto';
        document.getElementById('edit_category').value = p.ser_category || '';
        document.getElementById('edit_available').checked = p.ser_status == 1;
        
        if (p.ser_imagen) {
            var preview = document.getElementById('imagePreview');
            preview.querySelector('img').src = base_url + p.ser_imagen;
            preview.style.display = 'block';
        }
        
        $('#productModal').modal('show');
    })
    .catch(function() {
        document.querySelector('.loading').style.display = 'none';
        Swal.fire('Error', 'Error del servidor', 'error');
    });
}

function filterProducts() {
    var search = document.getElementById('filterSearch').value.toLowerCase();
    var type = document.getElementById('filterType').value;
    var status = document.getElementById('filterStatus').value;
    var cards = document.querySelectorAll('.product-card');
    var count = 0;
    cards.forEach(function(card) {
        var name = (card.querySelector('.product-card-title')?.textContent || '').toLowerCase();
        var cardType = (card.querySelector('.product-type-badge')?.textContent || '').trim().toLowerCase();
        var statusEl = card.querySelector('.product-card-body .status-badge');
        var cardStatus = statusEl ? (statusEl.className.includes('connected') ? '1' : '0') : '1';
        var show = name.includes(search);
        if (type && cardType !== type) show = false;
        if (status !== '' && cardStatus !== status) show = false;
        card.style.display = show ? '' : 'none';
        if (show) count++;
    });
    document.getElementById('filterCount').textContent = count + ' mostrado(s)';
}

function deleteProduct(id, name) {
    Swal.fire({
        title: 'Eliminar ' + name + '?',
        text: 'Esta accion no se puede deshacer',
        icon: 'warning', showCancelButton: true, confirmButtonText: 'Si, eliminar', cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.querySelector('.loading').style.display = 'flex';
            fetch(base_url + 'Products/delete', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ser_id: id })
            })
            .then(function(r){ return r.json(); })
            .then(function(r) {
                document.querySelector('.loading').style.display = 'none';
                if (r.status) {
                    Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500 });
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: r.message });
                }
            })
            .catch(function() {
                document.querySelector('.loading').style.display = 'none';
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error del servidor' });
            });
        }
    });
}

function saveProduct() {
    var form = document.getElementById('form_product');
    var formData = new FormData(form);
    
    if (!formData.get('name').trim()) {
        Swal.fire('Error', 'El nombre es requerido', 'error');
        return;
    }

    var url = editingId ? base_url + 'Products/update' : base_url + 'Products/save';
    document.querySelector('.loading').style.display = 'flex';
    
    fetch(url, { method: 'POST', body: formData })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            Swal.fire({ icon: 'success', title: editingId ? 'Actualizado' : 'Creado', timer: 1500 });
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: r.message });
        }
    })
    .catch(function() {
        document.querySelector('.loading').style.display = 'none';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error del servidor' });
    });
}

// Image preview
document.getElementById('edit_image').addEventListener('change', function(e) {
    var preview = document.getElementById('imagePreview');
    var img = preview.querySelector('img');
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { img.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(this.files[0]);
    } else if (!editingId) {
        preview.style.display = 'none';
    }
});
</script>
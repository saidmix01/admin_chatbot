<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Clientes</h4>
        <p>Gestiona los clientes de la plataforma</p>
    </div>

    <div class="action-bar">
        <div style="font-size: 0.875rem; color: var(--saas-gray-500);"><?= count($clients) ?> cliente(s)</div>
        <button class="btn-saas btn-saas-primary" onclick="openCreateModal()">
            <i class="feather icon-plus"></i> Nuevo Cliente
        </button>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive-wrap">
                <table class="table-saas table-saas-card-mode">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Negocio</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($clients)): ?>
                        <?php foreach($clients as $c): ?>
                        <tr>
                            <td data-label="ID">#<?= $c->us_id ?></td>
                            <td data-label="Nombre" style="font-weight: 500;"><?= $c->us_name ?></td>
                            <td data-label="Email"><?= $c->us_email ?></td>
                            <td data-label="Estado">
                                <span class="status-badge <?= $c->us_status == 1 ? 'connected' : 'disconnected' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <?= $c->us_status == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td data-label="Negocio"><?= $c->store_name ?? '—' ?></td>
                            <td data-label="Acciones">
                                <div style="display: flex; gap: 0.375rem;">
                                    <button class="btn-saas btn-saas-outline btn-saas-sm" onclick="openEditModal(<?= $c->us_id ?>, '<?= addslashes($c->us_name) ?>', '<?= $c->us_email ?>', <?= $c->us_status ?>)">
                                        <i class="feather icon-edit"></i>
                                    </button>
                                    <button class="btn-saas btn-saas-danger btn-saas-sm" onclick="deleteClient(<?= $c->us_id ?>)">
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 3rem; color: var(--saas-gray-400);">No hay clientes registrados</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--saas-radius); border: none;">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100); padding: 1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600;">Nuevo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <form id="form_create">
                    <div class="form-saas-group">
                        <label class="form-saas-label">Nombre del negocio / cliente</label>
                        <input type="text" class="form-saas" id="create_name" required placeholder="Ej: Café Colombia">
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Email de acceso</label>
                        <input type="email" class="form-saas" id="create_email" required placeholder="cliente@ejemplo.com">
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Contraseña</label>
                        <input type="password" class="form-saas" id="create_password" required placeholder="••••••••">
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--saas-gray-100); padding: 1rem 1.5rem;">
                <button class="btn-saas btn-saas-outline" data-dismiss="modal">Cancelar</button>
                <button class="btn-saas btn-saas-primary" onclick="createClient()">Crear Cliente</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--saas-radius); border: none;">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100); padding: 1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600;">Editar Cliente</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <input type="hidden" id="edit_us_id">
                <form id="form_edit">
                    <div class="form-saas-group">
                        <label class="form-saas-label">Nombre</label>
                        <input type="text" class="form-saas" id="edit_name" required>
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Email</label>
                        <input type="email" class="form-saas" id="edit_email" required>
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Nueva contraseña <small style="color: var(--saas-gray-400);">(dejar vacío para no cambiar)</small></label>
                        <input type="password" class="form-saas" id="edit_password" placeholder="••••••••">
                    </div>
                    <div class="form-saas-group">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" id="edit_status" checked style="width: 16px; height: 16px;">
                            <span style="font-size: 0.875rem; color: var(--saas-gray-700);">Activo</span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--saas-gray-100); padding: 1rem 1.5rem;">
                <button class="btn-saas btn-saas-outline" data-dismiss="modal">Cancelar</button>
                <button class="btn-saas btn-saas-primary" onclick="updateClient()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() { $('#createModal').modal('show'); }

function createClient() {
    var data = {
        name: document.getElementById('create_name').value,
        email: document.getElementById('create_email').value,
        password: document.getElementById('create_password').value
    };
    if (!data.name || !data.email || !data.password) {
        Swal.fire('Error', 'Todos los campos son requeridos', 'error'); return;
    }
    document.querySelector('.loading').style.display = 'flex';
    fetch(base_url + 'Clients/create', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(function(r){ return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            Swal.fire({ icon: 'success', title: 'Creado', text: r.message, timer: 1500 });
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

function openEditModal(id, name, email, status) {
    document.getElementById('edit_us_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_status').checked = status == 1;
    $('#editModal').modal('show');
}

function updateClient() {
    var data = {
        us_id: document.getElementById('edit_us_id').value,
        name: document.getElementById('edit_name').value,
        email: document.getElementById('edit_email').value,
        status: document.getElementById('edit_status').checked ? 1 : 0
    };
    var pwd = document.getElementById('edit_password').value;
    if (pwd) data.password = pwd;

    document.querySelector('.loading').style.display = 'flex';
    fetch(base_url + 'Clients/update', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(function(r){ return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            Swal.fire({ icon: 'success', title: 'Actualizado', timer: 1500 });
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

function deleteClient(id) {
    Swal.fire({
        title: '¿Eliminar cliente?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.querySelector('.loading').style.display = 'flex';
            fetch(base_url + 'Clients/delete', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ us_id: id })
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
            });
        }
    });
}
</script>
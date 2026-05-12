<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Clientes</h4>
        <p>Gestiona los clientes de la plataforma</p>
    </div>

    <div class="action-bar">
        <div style="font-size: 0.875rem; color: var(--saas-gray-500);">
            <?= count($clients) ?> cliente(s) registrados
        </div>
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
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Negocio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($clients)): ?>
                        <?php foreach($clients as $c): ?>
                        <tr>
                            <td data-label="ID">#<?= $c->us_id ?></td>
                            <td data-label="Nombre" style="font-weight: 500;"><?= $c->us_name ?></td>
                            <td data-label="Email"><?= $c->us_email ?></td>
                            <td data-label="Perfil"><span class="status-badge connected" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><?= $c->pro_description ?></span></td>
                            <td data-label="Negocio"><?= $c->store_name ?? '—' ?></td>
                            <td data-label="Estado">
                                <span class="status-badge <?= $c->us_status == 1 ? 'connected' : 'disconnected' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <?= $c->us_status == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td data-label="Acciones">
                                <a href="<?=base_url()?>Login/logout" class="btn-saas btn-saas-outline btn-saas-sm">
                                    <i class="feather icon-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--saas-gray-400);">
                                No hay clientes registrados aún
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--saas-radius); border: none;">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100); padding: 1.25rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600;">Nuevo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <form id="form_client">
                    <div class="form-saas-group">
                        <label class="form-saas-label">Nombre del negocio / cliente</label>
                        <input type="text" class="form-saas" id="client_name" required placeholder="Ej: Café Colombia">
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Email de acceso</label>
                        <input type="email" class="form-saas" id="client_email" required placeholder="cliente@ejemplo.com">
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Contraseña</label>
                        <input type="password" class="form-saas" id="client_password" required placeholder="••••••••">
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

<script>
function openCreateModal() {
    $('#createClientModal').modal('show');
}

function createClient() {
    const data = {
        name: document.getElementById('client_name').value,
        email: document.getElementById('client_email').value,
        password: document.getElementById('client_password').value
    };
    
    if (!data.name || !data.email || !data.password) {
        Swal.fire('Error', 'Todos los campos son requeridos', 'error');
        return;
    }

    document.querySelector('.loading').style.display = 'flex';
    fetch('<?=base_url()?>Clients/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(r => {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            Swal.fire('Creado', r.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', r.message, 'error');
        }
    })
    .catch(e => {
        document.querySelector('.loading').style.display = 'none';
        Swal.fire('Error', 'Error del servidor', 'error');
    });
}
</script>
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Nuevo flujo</h4>
        <p>Crea una nueva opcion que vera el cliente en el menu del bot</p>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="form_create">
                <div class="form-saas-group">
                    <label class="form-saas-label">Nombre del flujo</label>
                    <input type="text" name="name" class="form-saas" placeholder="Ej: Ver productos, Mis pedidos, Contactar" required>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Descripcion (opcional, se muestra como texto de ayuda)</label>
                    <textarea name="description" class="form-saas" rows="2" placeholder="Lo que el cliente vera sobre esta opcion"></textarea>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Trigger (palabra clave para activarlo)</label>
                    <input type="text" name="trigger_value" class="form-saas" placeholder="Ej: productos, pedidos, contacto">
                    <small class="text-muted">Cuando el cliente escriba esta palabra, se activa este flujo.</small>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Flujo padre (opcional)</label>
                    <select name="parent_flow_id" class="form-saas">
                        <option value="">-- Ninguno (flujo principal) --</option>
                        <?php foreach ($parent_flows ?? [] as $pf): ?>
                        <option value="<?= $pf->id ?>" <?= ($parent_id ?? 0) == $pf->id ? 'selected' : '' ?>><?= htmlspecialchars($pf->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Si es un subflujo, selecciona a cual flujo principal pertenece.</small>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Producto/Servicio asociado (opcional)</label>
                    <select name="service_id" class="form-saas">
                        <option value="">-- Ninguno --</option>
                        <?php foreach ($services ?? [] as $sv): ?>
                        <option value="<?= $sv->ser_id ?>"><?= htmlspecialchars($sv->ser_name) ?> (<?= $sv->ser_type ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Si el flujo es para comprar un producto o contratar un servicio especifico.</small>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Orden de visualizacion</label>
                    <input type="number" name="display_order" class="form-saas" value="0" min="0" style="width:100px">
                </div>
                <hr>
                <button type="submit" class="btn-saas btn-saas-primary">Crear flujo</button>
                <a href="<?= base_url() ?>FlowBuilder" class="btn-saas btn-saas-outline">Cancelar</a>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('form_create').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true; btn.innerHTML = 'Creando...';
    fetch('<?= base_url() ?>FlowBuilder/create', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: new URLSearchParams(new FormData(this))
    }).then(r => r.json()).then(d => {
        if (d.status) window.location = '<?= base_url() ?>FlowBuilder/edit/' + d.id;
        else alert(d.message);
        btn.disabled = false; btn.innerHTML = 'Crear flujo';
    }).catch(() => {
        alert('Error del servidor');
        btn.disabled = false; btn.innerHTML = 'Crear flujo';
    });
});
</script>

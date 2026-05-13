<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Nuevo flujo</h4>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="form_create">
                <div class="form-saas-group">
                    <label class="form-saas-label">Nombre del flujo</label>
                    <input type="text" name="name" class="form-saas" placeholder="Ej: Ventas, Soporte, Agendamiento" required>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Descripción</label>
                    <textarea name="description" class="form-saas" rows="2" placeholder="¿Para qué sirve este flujo?"></textarea>
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Trigger (palabra clave para activarlo)</label>
                    <input type="text" name="trigger_value" class="form-saas" placeholder="Ej: menu, ventas, hola">
                    <small class="text-muted">Cuando el cliente escriba esta palabra, se inicia el flujo.</small>
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
        method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(new FormData(this))
    }).then(r => r.json()).then(d => {
        if (d.status) window.location = '<?= base_url() ?>FlowBuilder/edit/' + d.id;
        else alert(d.message);
        btn.disabled = false; btn.innerHTML = 'Crear flujo';
    }).catch(() => { btn.disabled = false; btn.innerHTML = 'Crear flujo'; });
});
</script>

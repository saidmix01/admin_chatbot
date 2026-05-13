<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Configuración del Bot</h4>
        <p>Personaliza los mensajes automáticos de tu WhatsApp</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form_bot_config">
                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje de bienvenida</label>
                    <textarea class="form-saas" name="welcome_msg" rows="3" placeholder="¡Hola! Bienvenido a [nombre del negocio]. ¿En qué podemos ayudarte?"><?= $store->sto_wellcome_message ?? '¡Hola! Bienvenido a [nombre del negocio]. ¿En qué podemos ayudarte?' ?></textarea>
                    <small style="color: var(--saas-gray-400);">Se envía cuando un cliente escribe por primera vez</small>
                </div>

                <hr style="border-color: var(--saas-gray-100); margin: 1.5rem 0;">

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn-saas btn-saas-primary">
                        <i class="feather icon-save"></i> Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('form_bot_config').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i> Guardando...';

    fetch(base_url + 'BotConfig/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ welcome_msg: document.querySelector('textarea[name="welcome_msg"]').value })
    })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        btn.disabled = false;
        btn.innerHTML = orig;
        if (r.status) {
            Swal.fire({ icon: 'success', title: 'Guardado', text: r.message, timer: 2000 });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: r.message });
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = orig;
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error del servidor' });
    });
});
</script>
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

                <div class="form-saas-group">
                    <label class="form-saas-label">Frases de inicio del bot</label>
                    <div style="font-size:13px;color:var(--saas-gray-400);margin-bottom:12px">
                        Estas frases se mostraran al cliente cuando inicie una conversacion. El bot elegira una al azar.
                    </div>
                    <div id="starters-container">
                        <div class="input-group mb-2" style="display:none" id="starter-template">
                            <input type="text" class="form-saas starter-input" placeholder="Ej: Hola! En que puedo ayudarte?" style="max-width:500px" />
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-danger" onclick="this.closest('.input-group').remove()" style="height:38px">X</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-saas btn-saas-outline mt-1" onclick="addStarter()" style="font-size:13px">+ Agregar frase</button>
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

async function loadStarters() {
    try {
        const r = await fetch('<?= base_url() ?>Chat_configuration/get_starters');
        const d = await r.json();
        if (d.status && d.data) { d.data.forEach(s => addStarterInput(s)); }
    } catch(e) {}
}
function getStarters() {
    const inputs = document.querySelectorAll('.starter-input');
    const starters = [];
    inputs.forEach(i => { if (i.value.trim()) starters.push(i.value.trim()); });
    return starters;
}
function addStarterInput(value) {
    const tpl = document.getElementById('starter-template');
    const clone = tpl.cloneNode(true);
    clone.style.display = 'flex';
    const inp = clone.querySelector('.starter-input');
    if (value) inp.value = value;
    document.getElementById('starters-container').appendChild(clone);
}
function addStarter() { addStarterInput(''); }

document.getElementById('form_bot_config').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i> Guardando...';

    fetch(base_url + 'BotConfig/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
        welcome_msg: document.querySelector('textarea[name="welcome_msg"]').value,
        starters: getStarters()
    })
    })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        btn.disabled = false;
        btn.innerHTML = orig;
        if (r.status) {
            Swal.fire({ icon: 'success', title: 'Guardado', text: r.message, timer: 2000 }); loadStarters();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: r.message }); loadStarters();
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = orig;
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error del servidor' }); loadStarters();
    }); loadStarters();
}); loadStarters();
</script>
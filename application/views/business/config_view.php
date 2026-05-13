<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Mi Negocio</h4>
        <p>Configura la información de tu negocio</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form_business">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Nombre del negocio</label>
                            <input type="text" class="form-saas" name="business_name" value="<?= $store->sto_name ?? '' ?>" placeholder="Ej: Café Colombia">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Número WhatsApp</label>
                            <input type="text" class="form-saas" name="whatsapp" value="<?= $store->sto_phone ?? '' ?>" placeholder="Ej: +57 300 123 4567">
                        </div>
                    </div>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Descripción corta</label>
                    <textarea class="form-saas" name="description" rows="2" placeholder="Describe tu negocio en pocas palabras"><?= $store->sto_wellcome_message ?? '' ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Dirección</label>
                            <input type="text" class="form-saas" name="address" value="<?= $store->sto_direction ?? '' ?>" placeholder="Dirección del negocio">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">URL pública</label>
                            <input type="text" class="form-saas" name="public_url" value="<?= base_url() ?>preview/<?= (isset($store) && $store) ? $store->sto_id : '' ?>" readonly>
                            <small style="color: var(--saas-gray-400);">Compartí este link con tus clientes</small>
                        </div>
                    </div>
                </div>

                <hr style="border-color: var(--saas-gray-100); margin: 1.5rem 0;">

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn-saas btn-saas-primary">
                        <i class="feather icon-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('form_business').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i> Guardando...';

    fetch(base_url + 'Business/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            business_name: document.querySelector('input[name="business_name"]').value,
            whatsapp: document.querySelector('input[name="whatsapp"]').value,
            description: document.querySelector('textarea[name="description"]').value,
            address: document.querySelector('input[name="address"]').value
        })
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
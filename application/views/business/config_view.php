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
                            <label class="form-saas-label">URL pública de tu tienda</label>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="text" class="form-saas" name="slug" id="slug_input" value="<?= $store->sto_slug ?? '' ?>" placeholder="mi-tienda" style="flex: 1;">
                                <button type="button" class="btn-saas btn-saas-outline" onclick="copyPublicUrl()" title="Copiar link" style="padding: 0.5rem 0.75rem;">
                                    <i class="feather icon-copy"></i>
                                </button>
                            </div>
                            <small style="color: var(--saas-gray-400);">
                                Tus clientes te encuentran en: 
                                <a href="https://wapiapp.cloud/t/<?= $store->sto_slug ?? 'slug' ?>" target="_blank" id="public_url_label">
                                    wapiapp.cloud/t/<strong><?= $store->sto_slug ?? 'slug' ?></strong>
                                </a>
                            </small>
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

    var payload = {
        business_name: document.querySelector('input[name="business_name"]').value,
        whatsapp: document.querySelector('input[name="whatsapp"]').value,
        description: document.querySelector('textarea[name="description"]').value,
        address: document.querySelector('input[name="address"]').value,
        slug: document.querySelector('input[name="slug"]').value
    };

    fetch(base_url + 'Business/save', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        btn.disabled = false;
        btn.innerHTML = orig;
        if (r.status) {
            // Update slug in case it was adjusted for uniqueness
            if (r.slug) {
                document.querySelector('input[name="slug"]').value = r.slug;
                updatePublicUrl(r.slug);
            }
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

function updatePublicUrl(slug) {
    var label = document.getElementById('public_url_label');
    if (label) {
        label.innerHTML = 'wapiapp.cloud/t/<strong>' + slug + '</strong>';
        label.href = 'https://wapiapp.cloud/t/' + slug;
    }
}

function copyPublicUrl() {
    var slug = document.querySelector('input[name="slug"]').value;
    if (!slug) {
        Swal.fire({ icon: 'warning', title: 'Sin URL', text: 'Guardá el formulario primero para generar la URL' });
        return;
    }
    var url = 'https://wapiapp.cloud/t/' + slug;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            Swal.fire({ icon: 'success', title: 'Copiado', text: 'URL copiada: ' + url, timer: 2000 });
        });
    } else {
        // Fallback
        var input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        Swal.fire({ icon: 'success', title: 'Copiado', text: 'URL copiada: ' + url, timer: 2000 });
    }
}
</script>

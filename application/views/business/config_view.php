<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Mi Negocio</h4>
        <p>Configura la información de tu negocio</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form_business">

                <div class="form-saas-group">
                    <label class="form-saas-label">Foto de perfil (logo)</label>
                    <input type="hidden" name="logo" id="logo_input" value=<?= $store->sto_logo ?? "" ?>>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div id="logo_preview_area" style="
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            border: 2px dashed var(--saas-gray-200);
                            overflow: hidden;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            cursor: pointer;
                            background: var(--saas-gray-50);
                            flex-shrink: 0;
                            transition: border-color 0.2s;
                        ">
                            <?php if(!empty($store->sto_logo)): ?>
                            <img src="<?= base_url($store->sto_logo) ?>" id="logo_preview" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                            <i class="feather icon-user" style="font-size: 1.5rem; color: var(--saas-gray-300);" id="logo_placeholder"></i>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <button type="button" class="btn-saas btn-saas-outline" onclick="document.getElementById(logo_file_input).click();" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">
                                <i class="feather icon-upload"></i> Subir logo
                            </button>
                            <?php if(!empty($store->sto_logo)): ?>
                            <button type="button" class="btn-saas btn-saas-outline" onclick="removeLogo()" style="padding: 0.5rem 1rem; font-size: 0.8125rem; margin-left: 0.5rem; color: #ef4444; border-color: #fecaca;">
                                <i class="feather icon-trash-2"></i>
                            </button>
                            <?php endif; ?>
                            <div style="color: var(--saas-gray-300); font-size: 0.75rem; margin-top: 0.25rem;">JPG, PNG, WebP · Max 2MB</div>
                        </div>
                    </div>
                    <input type="file" id="logo_file_input" accept="image/jpeg,image/png,image/webp" style="display: none;">
                </div>

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

                <div class="form-saas-group">
                    <label class="form-saas-label">Foto de portada</label>
                    <input type="hidden" name="cover" id="cover_input" value="<?= $store->sto_cover ?? '' ?>">
                    <div id="cover_upload_area" style="
                        border: 2px dashed var(--saas-gray-200);
                        border-radius: 12px;
                        padding: 2rem;
                        text-align: center;
                        cursor: pointer;
                        transition: all 0.2s;
                        background: <?= !empty($store->sto_cover) ? 'transparent' : 'var(--saas-gray-50)' ?>;
                        min-height: 180px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        position: relative;
                        overflow: hidden;
                    ">
                        <?php if (!empty($store->sto_cover)): ?>
                        <img src="<?= base_url($store->sto_cover) ?>" id="cover_preview" style="
                            max-height: 200px;
                            border-radius: 8px;
                            width: 100%;
                            object-fit: cover;
                        ">
                        <button type="button" id="remove_cover_btn" style="
                            position: absolute;
                            top: 0.5rem;
                            right: 0.5rem;
                            background: rgba(0,0,0,0.6);
                            color: #fff;
                            border: none;
                            border-radius: 50%;
                            width: 32px;
                            height: 32px;
                            cursor: pointer;
                            font-size: 1rem;
                        "><i class="feather icon-x"></i></button>
                        <?php else: ?>
                        <div id="cover_placeholder">
                            <i class="feather icon-image" style="font-size: 2.5rem; color: var(--saas-gray-300); display: block; margin-bottom: 0.5rem;"></i>
                            <div style="color: var(--saas-gray-400); font-size: 0.875rem;">Hacé clic para subir una foto de portada</div>
                            <div style="color: var(--saas-gray-300); font-size: 0.75rem; margin-top: 0.25rem;">JPG, PNG, WebP · Max 5MB</div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" id="cover_file_input" accept="image/jpeg,image/png,image/webp" style="display: none;">
                    <small style="color: var(--saas-gray-400);">Se muestra en la parte superior de tu tienda pública</small>
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

<style>
#cover_upload_area:hover {
    border-color: var(--saas-primary, #6366f1);
    background: var(--saas-gray-50);
}
#cover_upload_area.dragover {
    border-color: var(--saas-primary, #6366f1);
    background: #eef2ff;
}
</style>

<script>
// ——— Cover image upload ———
(function() {
    var uploadArea = document.getElementById('cover_upload_area');
    var fileInput = document.getElementById('cover_file_input');
    var coverInput = document.getElementById('cover_input');

    uploadArea.addEventListener('click', function(e) {
        if (e.target.closest('#remove_cover_btn')) return;
        fileInput.click();
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('dragover');
    });
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            uploadCover(fileInput.files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            uploadCover(this.files[0]);
        }
    });

    function uploadCover(file) {
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Muy grande', text: 'La imagen no debe superar 5MB' });
            return;
        }

        var fd = new FormData();
        fd.append('cover_image', file);

        uploadArea.innerHTML = '<i class="feather icon-loader" style="font-size: 2rem; animation: spin 1s linear infinite; color: var(--saas-primary);"></i><div style="margin-top: 0.5rem; color: var(--saas-gray-400);">Subiendo...</div>';

        fetch(base_url + 'Business/upload_cover', {
            method: 'POST',
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(function(r) {
            if (r.status && r.url) {
                coverInput.value = r.url;
                uploadArea.innerHTML =
                    '<img src="' + base_url + r.url + '" id="cover_preview" style="max-height: 200px; border-radius: 8px; width: 100%; object-fit: cover;">' +
                    '<button type="button" id="remove_cover_btn" style="position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-size: 1rem;"><i class="feather icon-x"></i></button>';
                attachRemoveHandler();
            } else {
                uploadArea.innerHTML = '<div style="color: #ef4444;">Error: ' + (r.message || 'No se pudo subir') + '</div>';
            }
        })
        .catch(function() {
            uploadArea.innerHTML = '<div style="color: #ef4444;">Error de conexión</div>';
        });
    }

    function attachRemoveHandler() {
        var removeBtn = document.getElementById('remove_cover_btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                coverInput.value = '';
                uploadArea.innerHTML =
                    '<div id="cover_placeholder">' +
                    '<i class="feather icon-image" style="font-size: 2.5rem; color: var(--saas-gray-300); display: block; margin-bottom: 0.5rem;"></i>' +
                    '<div style="color: var(--saas-gray-400); font-size: 0.875rem;">Hacé clic para subir una foto de portada</div>' +
                    '<div style="color: var(--saas-gray-300); font-size: 0.75rem; margin-top: 0.25rem;">JPG, PNG, WebP · Max 5MB</div>' +
                    '</div>';
                attachUploadClick();
            });
        }
    }

    function attachUploadClick() {
        // Remove old listeners by replacing the click logic
    }

    // Initial attach for remove button if exists
    attachRemoveHandler();

    // --- Logo upload ---
    var logoInput = document.getElementById('logo_file_input');
    var logoHidden = document.getElementById('logo_input');

    logoInput.addEventListener('change', function() {
        if (this.files.length) {
            uploadLogo(this.files[0]);
        }
    });

    function uploadLogo(file) {
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Muy grande', text: 'El logo no debe superar 2MB' });
            return;
        }

        var fd = new FormData();
        fd.append('logo_image', file);

        var preview = document.getElementById('logo_preview_area');
        preview.innerHTML = '<i class="feather icon-loader" style="font-size: 1.5rem; animation: spin 1s linear infinite; color: var(--saas-primary);"></i>';

        fetch(base_url + 'Business/upload_logo', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(r) {
            if (r.status && r.url) {
                logoHidden.value = r.url;
                preview.innerHTML = '<img src="' + base_url + r.url + '" style="width:100%;height:100%;object-fit:cover;">';
                var rmBtn = document.querySelector('button#remove_logo_btn');
                if (!rmBtn) {
                    var uploadBtn = document.querySelector('button[onclick*="logo_file_input"]');
                    if (uploadBtn) {
                        rmBtn = document.createElement('button');
                        rmBtn.id = 'remove_logo_btn';
                        rmBtn.type = 'button';
                        rmBtn.className = 'btn-saas btn-saas-outline';
                        rmBtn.style.cssText = 'padding: 0.5rem 1rem; font-size: 0.8125rem; margin-left: 0.5rem; color: #ef4444; border-color: #fecaca;';
                        rmBtn.innerHTML = '<i class="feather icon-trash-2"></i>';
                        rmBtn.onclick = removeLogo;
                        uploadBtn.parentNode.insertBefore(rmBtn, uploadBtn.nextSibling);
                    }
                }
            } else {
                preview.innerHTML = '<i class="feather icon-user" style="font-size: 1.5rem; color: var(--saas-gray-300);"></i>';
            }
        })
        .catch(function() {
            preview.innerHTML = '<i class="feather icon-user" style="font-size: 1.5rem; color: var(--saas-gray-300);"></i>';
        });
    }
})();

function removeLogo() {
    document.getElementById('logo_input').value = '';
    document.getElementById('logo_preview_area').innerHTML = '<i class="feather icon-user" style="font-size: 1.5rem; color: var(--saas-gray-300);"></i>';
    var rmBtn = document.getElementById('remove_logo_btn');
    if (rmBtn) rmBtn.remove();
}


// ——— Form submit ———
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
        slug: document.querySelector('input[name="slug"]').value,
        cover: document.getElementById('cover_input').value,
        logo: document.getElementById('logo_input').value
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

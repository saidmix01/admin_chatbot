<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Mi Negocio</h4>
        <p>Configura la información de tu negocio</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form_business">
                <div class="row" style="margin-bottom: 1rem;">
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Foto de perfil</label>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div style="width:64px;height:64px;border-radius:999px;overflow:hidden;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#9ca3af;flex:0 0 auto;">
                                    <?php
                                        $profileSrc = '';
                                        if (isset($store->sto_logo) && !empty($store->sto_logo)) {
                                            $profileSrc = (preg_match('~^(data:|https?://)~', $store->sto_logo)) ? $store->sto_logo : (base_url() . $store->sto_logo);
                                        }
                                    ?>
                                    <?php if(!empty($profileSrc)): ?>
                                        <img id="profilePreview" src="<?= $profileSrc ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                                    <?php else: ?>
                                        <i class="feather icon-user" style="font-size:1.5rem;"></i>
                                        <img id="profilePreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-saas" name="profile_image" id="profile_image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Foto de portada</label>
                            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                                <div style="width:100%;height:96px;border-radius:12px;overflow:hidden;background:#e5e7eb;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                                    <?php
                                        $coverSrc = '';
                                        if (isset($store->sto_cover) && !empty($store->sto_cover)) {
                                            $coverSrc = (preg_match('~^(data:|https?://)~', $store->sto_cover)) ? $store->sto_cover : (base_url() . $store->sto_cover);
                                        }
                                    ?>
                                    <?php if(!empty($coverSrc)): ?>
                                        <img id="coverPreview" src="<?= $coverSrc ?>" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                                    <?php else: ?>
                                        <i class="feather icon-image" style="font-size:1.5rem;"></i>
                                        <img id="coverPreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-saas" name="cover_image" id="cover_image" accept="image/*">
                            </div>
                        </div>
                    </div>
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
                    <textarea class="form-saas" name="description" rows="2" placeholder="Describe tu negocio en pocas palabras"><?= $store->sto_description ?? $store->sto_wellcome_message ?? '' ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Dirección</label>
                            <input type="text" class="form-saas" name="address" value="<?= $store->sto_direction ?? '' ?>" placeholder="Dirección del negocio">
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
function previewImage(input, imgId, iconSelector) {
    var file = input.files && input.files[0] ? input.files[0] : null;
    var img = document.getElementById(imgId);
    if (!img) return;
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
        img.style.display = 'block';
        if (iconSelector) {
            var icon = img.parentElement.querySelector(iconSelector);
            if (icon) icon.style.display = 'none';
        }
    };
    reader.readAsDataURL(file);
}

document.getElementById('profile_image').addEventListener('change', function() {
    previewImage(this, 'profilePreview', 'i');
});
document.getElementById('cover_image').addEventListener('change', function() {
    previewImage(this, 'coverPreview', 'i');
});

document.getElementById('form_business').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i> Guardando...';

    var fd = new FormData();
    fd.append('business_name', document.querySelector('input[name="business_name"]').value);
    fd.append('whatsapp', document.querySelector('input[name="whatsapp"]').value);
    fd.append('description', document.querySelector('textarea[name="description"]').value);
    fd.append('address', document.querySelector('input[name="address"]').value);
    var p = document.getElementById('profile_image');
    var c = document.getElementById('cover_image');
    if (p && p.files && p.files[0]) fd.append('profile_image', p.files[0]);
    if (c && c.files && c.files[0]) fd.append('cover_image', c.files[0]);

    fetch(base_url + 'Business/save', { method: 'POST', body: fd })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        btn.disabled = false;
        btn.innerHTML = orig;
        if (r.status) {
            Swal.fire({ icon: 'success', title: 'Guardado', text: r.message, timer: 2000 });
            setTimeout(function() { location.reload(); }, 900);
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

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
                            <input type="text" class="form-saas" name="business_name" placeholder="Ej: Café Colombia">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Número WhatsApp</label>
                            <input type="text" class="form-saas" name="whatsapp" placeholder="Ej: +57 300 123 4567">
                        </div>
                    </div>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Descripción corta</label>
                    <textarea class="form-saas" name="description" rows="2" placeholder="Describe tu negocio en pocas palabras"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Logo</label>
                            <input type="file" class="form-saas" name="logo" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Color principal</label>
                            <input type="color" class="form-saas" name="primary_color" value="#6366f1" style="height: 42px; padding: 4px;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Dirección</label>
                            <input type="text" class="form-saas" name="address" placeholder="Dirección del negocio">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-saas-group">
                            <label class="form-saas-label">URL pública</label>
                            <input type="text" class="form-saas" name="public_url" placeholder="https://admin.wapiapp.cloud/mi-negocio">
                        </div>
                    </div>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Horario de atención</label>
                    <div class="row">
                        <div class="col-6">
                            <input type="time" class="form-saas" name="open_time" value="08:00">
                            <small style="color: var(--saas-gray-400);">Apertura</small>
                        </div>
                        <div class="col-6">
                            <input type="time" class="form-saas" name="close_time" value="18:00">
                            <small style="color: var(--saas-gray-400);">Cierre</small>
                        </div>
                    </div>
                </div>

                <hr style="border-color: var(--saas-gray-100); margin: 1.5rem 0;">

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn-saas btn-saas-primary">
                        <i class="feather icon-save"></i> Guardar cambios
                    </button>
                    <button type="reset" class="btn-saas btn-saas-outline">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#form_business').on('submit', function(e) {
    e.preventDefault();
    // TODO: Save business config
    Swal.fire({
        icon: 'success',
        title: 'Guardado',
        text: 'Configuración del negocio actualizada',
        timer: 2000
    });
});
</script>
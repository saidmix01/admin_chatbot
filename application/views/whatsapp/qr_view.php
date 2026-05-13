<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Conexión WhatsApp</h4>
        <p>Escanea el código QR para conectar tu número de WhatsApp</p>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Código QR</div>
                <div class="card-body">
                    <div class="qr-container" id="qr_container">
                        <?php if($qr_status === 'connected'): ?>
                            <div style="text-align: center;">
                                <i class="fab fa-whatsapp" style="font-size: 4rem; color: var(--saas-success);"></i>
                                <h5 style="margin-top: 1rem; font-weight: 600;">WhatsApp Conectado</h5>
                                <p style="color: var(--saas-gray-500);"><?= $whatsapp_number ?></p>
                                <small style="color: var(--saas-gray-400);">El bot está funcionando</small>
                            </div>
                        <?php elseif($qr_base64): ?>
                            <div style="text-align: center;">
                                <img src="data:image/png;base64,<?= $qr_base64 ?>" alt="QR WhatsApp" style="max-width: 280px; border-radius: var(--saas-radius-sm);">
                                <h5 style="margin-top: 1rem; font-weight: 600;">Escanea con WhatsApp</h5>
                                <p style="color: var(--saas-gray-500);">Abre WhatsApp → Menú → WhatsApp Web</p>
                                <div id="qr_status_text">
                                    <span class="status-badge reconnecting">Esperando escaneo...</span>
                                </div>
                            </div>
                        <?php elseif($qr_status === 'expired'): ?>
                            <div style="text-align: center;">
                                <i class="feather icon-alert-circle" style="font-size: 4rem; color: var(--saas-danger);"></i>
                                <h5 style="margin-top: 1rem; font-weight: 600;">Sesión Expirada</h5>
                                <p style="color: var(--saas-gray-500);">El código QR ha expirado</p>
                                <button class="btn-saas btn-saas-primary" onclick="refreshQR()">
                                    <i class="feather icon-refresh-cw"></i> Generar nuevo QR
                                </button>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center;">
                                <i class="feather icon-smartphone" style="font-size: 4rem; color: var(--saas-gray-300);"></i>
                                <h5 style="margin-top: 1rem; font-weight: 600; color: var(--saas-gray-600);">Sin conexión</h5>
                                <p style="color: var(--saas-gray-500);">El bot no ha generado un QR aún</p>
                                <p style="font-size: 0.8125rem; color: var(--saas-gray-400);">Asegúrate de que el bot service esté corriendo</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Estado de conexión</div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div class="stat-card" style="border: none; box-shadow: none; padding: 0;">
                            <div class="stat-card-icon <?= $qr_status === 'connected' ? 'green' : ($qr_status === 'expired' ? 'red' : 'yellow') ?>">
                                <i class="feather icon-wifi"></i>
                            </div>
                            <div class="stat-card-content">
                                <div class="stat-card-label">Estado</div>
                                <div class="stat-card-value">
                                    <span class="status-badge <?= $qr_status === 'connected' ? 'connected' : ($qr_status === 'expired' ? 'disconnected' : ($qr_base64 ? 'reconnecting' : 'disconnected')) ?>">
                                        <?= $qr_status === 'connected' ? 'Conectado' : ($qr_status === 'expired' ? 'Expirado' : ($qr_base64 ? 'Esperando escaneo' : 'Desconectado')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if($whatsapp_number): ?>
                        <div class="stat-card" style="border: none; box-shadow: none; padding: 0;">
                            <div class="stat-card-icon blue"><i class="feather icon-phone"></i></div>
                            <div class="stat-card-content">
                                <div class="stat-card-label">Número</div>
                                <div class="stat-card-value"><?= $whatsapp_number ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <hr style="border-color: var(--saas-gray-100); margin: 0.5rem 0;">

                        <div style="font-size: 0.875rem; color: var(--saas-gray-500);">
                            <p><strong>Instrucciones:</strong></p>
                            <ol style="padding-left: 1.25rem; line-height: 1.8;">
                                <li>Abre WhatsApp en tu teléfono</li>
                                <li>Ve a <strong>Menú → WhatsApp Web</strong></li>
                                <li>Escanea el código QR</li>
                                <li>¡Listo! Tu bot estará conectado</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Poll for QR every 5 seconds when waiting
<?php if($qr_status !== 'connected'): ?>
setInterval(function() {
    fetch(base_url + 'BotApi/get_qr/<?= $this->session->userdata('us_id') ?>')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.status && data.qr && data.qr !== '<?= $qr_base64 ?>') {
            location.reload();
        }
    })
    .catch(function() {});
}, 5000);
<?php endif; ?>

function refreshQR() {
    document.querySelector('.loading').style.display = 'flex';
    fetch(base_url + 'BotApi/refresh_qr/<?= $this->session->userdata('us_id') ?>', { method: 'POST' })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            location.reload();
        } else {
            Swal.fire('Error', 'No se pudo generar el QR. ¿El bot service está corriendo?', 'error');
        }
    })
    .catch(function() {
        document.querySelector('.loading').style.display = 'none';
    });
}
</script>
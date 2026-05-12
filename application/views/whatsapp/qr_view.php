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
                            </div>
                        <?php elseif($qr_status === 'expired'): ?>
                            <div style="text-align: center;">
                                <i class="feather icon-alert-circle" style="font-size: 4rem; color: var(--saas-danger);"></i>
                                <h5 style="margin-top: 1rem; font-weight: 600;">Sesión Expirada</h5>
                                <p style="color: var(--saas-gray-500);">El código QR ha expirado. Genera uno nuevo.</p>
                                <button class="btn-saas btn-saas-primary" onclick="refreshQR()">
                                    <i class="feather icon-refresh-cw"></i> Generar nuevo QR
                                </button>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center;" id="qr_placeholder">
                                <i class="feather icon-smartphone" style="font-size: 4rem; color: var(--saas-gray-300);"></i>
                                <h5 style="margin-top: 1rem; font-weight: 600; color: var(--saas-gray-600);">Esperando escaneo</h5>
                                <p style="color: var(--saas-gray-500);">Escanea el código QR con tu WhatsApp</p>
                                <div style="width: 220px; height: 220px; background: var(--saas-gray-100); border-radius: var(--saas-radius-sm); margin: 1rem auto; display: flex; align-items: center; justify-content: center;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=wapi_connect" alt="QR" style="max-width: 100%;">
                                </div>
                                <button class="btn-saas btn-saas-outline btn-saas-sm" onclick="refreshQR()">
                                    <i class="feather icon-refresh-cw"></i> Refrescar QR
                                </button>
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
                                    <span class="status-badge <?= $qr_status === 'connected' ? 'connected' : ($qr_status === 'expired' ? 'disconnected' : 'reconnecting') ?>">
                                        <?= $qr_status === 'connected' ? 'Conectado' : ($qr_status === 'expired' ? 'Expirado' : 'Esperando escaneo') ?>
                                    </span>
                                </div>
                            </div>
                        </div>

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
function refreshQR() {
    document.getElementById('qr_container').innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <i class="feather icon-loader" style="font-size: 2rem; color: var(--saas-primary); animation: spin 1s linear infinite;"></i>
            <p style="margin-top: 1rem; color: var(--saas-gray-500);">Generando nuevo QR...</p>
        </div>
    `;
    setTimeout(() => {
        location.reload();
    }, 2000);
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
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
                        <?php elseif($qr_status === 'reconnecting'): ?>
                            <div style="text-align: center;">
                                <i class="feather icon-refresh-cw" style="font-size: 4rem; color: var(--saas-warning);"></i>
                                <h5 style="margin-top: 1rem; font-weight: 600;">Reconectando...</h5>
                                <p style="color: var(--saas-gray-500);">El bot está intentando recuperar la sesión</p>
                                <small style="color: var(--saas-gray-400);">Si tarda, reinicia el bot o desconecta la sesión desde tu teléfono</small>
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
                            <div class="stat-card-icon <?= $qr_status === 'connected' ? 'green' : ($qr_status === 'expired' ? 'red' : 'yellow') ?>" id="wa_state_icon">
                                <i class="feather icon-wifi"></i>
                            </div>
                            <div class="stat-card-content">
                                <div class="stat-card-label">Estado</div>
                                <div class="stat-card-value">
                                    <span class="status-badge <?= $qr_status === 'connected' ? 'connected' : ($qr_status === 'expired' ? 'disconnected' : ($qr_base64 ? 'reconnecting' : 'disconnected')) ?>" id="wa_state_badge">
                                        <?= $qr_status === 'connected' ? 'Conectado' : ($qr_status === 'expired' ? 'Expirado' : ($qr_base64 ? 'Esperando escaneo' : 'Desconectado')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if($whatsapp_number): ?>
                        <div class="stat-card" style="border: none; box-shadow: none; padding: 0;" id="wa_number_row">
                            <div class="stat-card-icon blue"><i class="feather icon-phone"></i></div>
                            <div class="stat-card-content">
                                <div class="stat-card-label">Número</div>
                                <div class="stat-card-value" id="wa_number_value"><?= $whatsapp_number ?></div>
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
var usId = <?= (int)$this->session->userdata('us_id') ?>;
var lastQr = <?= json_encode($qr_base64 ?: '') ?>;
var lastStatus = <?= json_encode($qr_status ?: '') ?>;
var lastNumber = <?= json_encode($whatsapp_number ?: '') ?>;
var pollTimer = null;

function mapStatus(statusText, hasQr) {
    if (statusText === 'connected') return { label: 'Conectado', badge: 'connected', icon: 'green' };
    if (statusText === 'reconnecting') return { label: 'Reconectando', badge: 'reconnecting', icon: 'yellow' };
    if (statusText === 'expired') return { label: 'Expirado', badge: 'disconnected', icon: 'red' };
    if (statusText === 'waiting_scan' || hasQr) return { label: 'Esperando escaneo', badge: 'reconnecting', icon: 'yellow' };
    return { label: 'Desconectado', badge: 'disconnected', icon: 'red' };
}

function renderQrContainer(state) {
    var c = document.getElementById('qr_container');
    if (!c) return;

    if (state.statusText === 'connected') {
        c.innerHTML = ''
            + '<div style="text-align: center;">'
            + '  <i class="fab fa-whatsapp" style="font-size: 4rem; color: var(--saas-success);"></i>'
            + '  <h5 style="margin-top: 1rem; font-weight: 600;">WhatsApp Conectado</h5>'
            + '  <p style="color: var(--saas-gray-500);">' + (state.whatsappNumber || '') + '</p>'
            + '  <small style="color: var(--saas-gray-400);">El bot está funcionando</small>'
            + '</div>';
        return;
    }

    if (state.statusText === 'reconnecting') {
        c.innerHTML = ''
            + '<div style="text-align: center;">'
            + '  <i class="feather icon-refresh-cw" style="font-size: 4rem; color: var(--saas-warning);"></i>'
            + '  <h5 style="margin-top: 1rem; font-weight: 600;">Reconectando...</h5>'
            + '  <p style="color: var(--saas-gray-500);">El bot está intentando recuperar la sesión</p>'
            + '  <small style="color: var(--saas-gray-400);">Si tarda, reinicia el bot o desconecta la sesión desde tu teléfono</small>'
            + '</div>';
        return;
    }

    if (state.statusText === 'expired') {
        c.innerHTML = ''
            + '<div style="text-align: center;">'
            + '  <i class="feather icon-alert-circle" style="font-size: 4rem; color: var(--saas-danger);"></i>'
            + '  <h5 style="margin-top: 1rem; font-weight: 600;">Sesión Expirada</h5>'
            + '  <p style="color: var(--saas-gray-500);">El código QR ha expirado</p>'
            + '  <button class="btn-saas btn-saas-primary" onclick="refreshQR()">'
            + '    <i class="feather icon-refresh-cw"></i> Generar nuevo QR'
            + '  </button>'
            + '</div>';
        return;
    }

    if (state.qr) {
        c.innerHTML = ''
            + '<div style="text-align: center;">'
            + '  <img id="qr_img" src="data:image/png;base64,' + state.qr + '" alt="QR WhatsApp" style="max-width: 280px; border-radius: var(--saas-radius-sm);">'
            + '  <h5 style="margin-top: 1rem; font-weight: 600;">Escanea con WhatsApp</h5>'
            + '  <p style="color: var(--saas-gray-500);">Abre WhatsApp → Menú → WhatsApp Web</p>'
            + '  <div id="qr_status_text">'
            + '    <span class="status-badge reconnecting">Esperando escaneo...</span>'
            + '  </div>'
            + '</div>';
        return;
    }

    c.innerHTML = ''
        + '<div style="text-align: center;">'
        + '  <i class="feather icon-smartphone" style="font-size: 4rem; color: var(--saas-gray-300);"></i>'
        + '  <h5 style="margin-top: 1rem; font-weight: 600; color: var(--saas-gray-600);">Sin conexión</h5>'
        + '  <p style="color: var(--saas-gray-500);">El bot no ha generado un QR aún</p>'
        + '  <p style="font-size: 0.8125rem; color: var(--saas-gray-400);">Asegúrate de que el bot service esté corriendo</p>'
        + '</div>';
}

function updateStatusUI(statusText, qr, whatsappNumber) {
    var m = mapStatus(statusText, !!qr);
    var badge = document.getElementById('wa_state_badge');
    if (badge) {
        badge.classList.remove('connected', 'reconnecting', 'disconnected');
        badge.classList.add(m.badge);
        badge.textContent = m.label;
    }
    var icon = document.getElementById('wa_state_icon');
    if (icon) {
        icon.classList.remove('green', 'yellow', 'red', 'blue', 'purple');
        icon.classList.add(m.icon);
    }

    var row = document.getElementById('wa_number_row');
    var val = document.getElementById('wa_number_value');
    if (whatsappNumber) {
        if (!row && document.querySelector('.stat-card-content')) {
            var hr = document.querySelector('.card-body hr');
            if (hr) {
                var wrap = hr.parentNode;
                var div = document.createElement('div');
                div.className = 'stat-card';
                div.id = 'wa_number_row';
                div.style.border = 'none';
                div.style.boxShadow = 'none';
                div.style.padding = '0';
                div.innerHTML = ''
                    + '<div class="stat-card-icon blue"><i class="feather icon-phone"></i></div>'
                    + '<div class="stat-card-content">'
                    + '  <div class="stat-card-label">Número</div>'
                    + '  <div class="stat-card-value" id="wa_number_value"></div>'
                    + '</div>';
                wrap.insertBefore(div, hr);
                row = div;
                val = div.querySelector('#wa_number_value');
            }
        }
        if (val) val.textContent = whatsappNumber;
    } else {
        if (row) row.remove();
    }
}

function pollNow() {
    return Promise.all([
        fetch(base_url + 'BotApi/get_qr/' + usId).then(function(r) { return r.json(); }).catch(function() { return null; }),
        fetch(base_url + 'BotApi/get_status/' + usId).then(function(r) { return r.json(); }).catch(function() { return null; })
    ]).then(function(res) {
        var qrRes = res[0] || {};
        var stRes = res[1] || {};
        var statusText = (qrRes && qrRes.status_text) ? qrRes.status_text : (stRes && stRes.data && stRes.data.bs_status) ? stRes.data.bs_status : 'disconnected';
        var qr = (qrRes && qrRes.qr) ? qrRes.qr : '';
        var whatsappNumber = (stRes && stRes.data && stRes.data.bs_whatsapp_number) ? stRes.data.bs_whatsapp_number : (lastNumber || '');

        var qrChanged = (qr || '') !== (lastQr || '');
        var statusChanged = (statusText || '') !== (lastStatus || '');
        var numberChanged = (whatsappNumber || '') !== (lastNumber || '');

        if (qrChanged || statusChanged || numberChanged) {
            renderQrContainer({ statusText: statusText, qr: qr, whatsappNumber: whatsappNumber });
            updateStatusUI(statusText, qr, whatsappNumber);
        }

        lastQr = qr || '';
        lastStatus = statusText || '';
        lastNumber = whatsappNumber || '';
    }).catch(function() {});
}

function startPolling() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(pollNow, 15000);
    pollNow();
}

function refreshQR() {
    document.querySelector('.loading').style.display = 'flex';
    fetch(base_url + 'BotApi/refresh_qr/' + usId, { method: 'POST' })
    .then(function(r) { return r.json(); })
    .then(function(r) {
        document.querySelector('.loading').style.display = 'none';
        if (r.status) {
            lastQr = '';
            lastStatus = 'waiting_scan';
            pollNow();
        } else {
            Swal.fire('Error', r.message || 'No se pudo generar el QR. ¿El bot service está corriendo?', 'error');
        }
    })
    .catch(function() {
        document.querySelector('.loading').style.display = 'none';
    });
}

document.addEventListener('DOMContentLoaded', startPolling);
</script>

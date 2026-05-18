<!-- [ content ] Start -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="page-header">
        <h4>Dashboard</h4>
        <p>Resumen del estado de tu bot y negocio</p>
    </div>

    <!-- Status Cards Row -->
    <div class="row" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin: 0;">

        <!-- WhatsApp Status -->
        <div class="stat-card">
            <div class="stat-card-icon <?=$whatsapp_status === 'connected' ? 'green' : (($whatsapp_status === 'reconnecting' || $whatsapp_status === 'waiting_scan') ? 'yellow' : 'red')?>" id="dash_wa_icon">
                <i class="fab fa-whatsapp"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">WhatsApp</div>
                <div class="stat-card-value">
                    <span class="status-badge <?=$whatsapp_status === 'connected' ? 'connected' : (($whatsapp_status === 'reconnecting' || $whatsapp_status === 'waiting_scan') ? 'reconnecting' : 'disconnected')?>" id="dash_wa_badge">
                        <?php
                            if (($whatsapp_status ?? '') === 'connected') echo 'Conectado';
                            else if (($whatsapp_status ?? '') === 'expired') echo 'Expirado';
                            else if (($whatsapp_status ?? '') === 'waiting_scan') echo 'Esperando escaneo';
                            else if (($whatsapp_status ?? '') === 'reconnecting') echo 'Reconectando';
                            else echo 'Desconectado';
                        ?>
                    </span>
                </div>
                <div class="stat-card-sub" id="dash_wa_number">
                    <?= $whatsapp_number ?? 'No conectado' ?>
                </div>
            </div>
        </div>

        <!-- Bot Status -->
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="feather icon-message-square"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Bot</div>
                <div class="stat-card-value">
                    <span class="status-badge <?= $bot_status === 'active' ? 'connected' : 'disconnected' ?>" id="dash_bot_badge">
                        <?= $bot_status === 'active' ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
                <div class="stat-card-sub">Respuestas automáticas</div>
            </div>
        </div>

        <!-- Server Status -->
        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="feather icon-server"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Servidor</div>
                <div class="stat-card-value">
                    <span class="status-badge connected">Online</span>
                </div>
                <div class="stat-card-sub">PHP <?= phpversion() ?></div>
            </div>
        </div>

        <!-- Products Count -->
        <div class="stat-card">
            <div class="stat-card-icon <?= ($products_count ?? 0) > 0 ? 'green' : 'yellow' ?>">
                <i class="feather icon-package"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Productos</div>
                <div class="stat-card-value"><?= $products_count ?? 0 ?></div>
                <div class="stat-card-sub">Registrados en tu catálogo</div>
            </div>
        </div>

        <!-- Plan Status -->
        <div class="stat-card">
            <?php
                $pi = $plan_info ?? null;
                $exp = $pi && !empty($pi["plan_expires_soon"]);
                $dl = $pi ? ($pi["plan_days_left"] ?? null) : null;
                $planColor = $exp ? 'yellow' : 'green';
                if ($dl !== null && $dl < 0) $planColor = 'red';
            ?>
            <div class="stat-card-icon <?= $planColor ?>">
                <i class="feather icon-calendar"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Plan</div>
                <div class="stat-card-value">
                    <?php if(!$pi || empty($pi["plan_start"]) || empty($pi["plan_end"])): ?>
                        <span class="status-badge disconnected">Sin plan</span>
                    <?php else: ?>
                        <span class="status-badge <?= ($dl !== null && $dl < 0) ? 'disconnected' : ($exp ? 'reconnecting' : 'connected') ?>">
                            <?php
                                if ($dl !== null && $dl < 0) echo 'Vencido';
                                else if ($exp) echo 'Próximo a vencer';
                                else echo 'Activo';
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="stat-card-sub">
                    <?php if($pi && !empty($pi["plan_start"]) && !empty($pi["plan_end"])): ?>
                        Inicia <?= $pi["plan_start"] ?> · Termina <?= $pi["plan_end"] ?>
                    <?php else: ?>
                        Configura la fecha de inicio en Usuarios
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Actions -->
    <div style="margin-top: 2rem;">
        <h5 style="font-size: 0.9375rem; font-weight: 600; color: var(--saas-gray-700); margin-bottom: 1rem;">Acciones rápidas</h5>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="<?=base_url()?>Whatsapp" class="btn-saas btn-saas-outline">
                <i class="fab fa-whatsapp"></i> Conectar WhatsApp
            </a>
            <a href="<?=base_url()?>Business" class="btn-saas btn-saas-outline">
                <i class="feather icon-briefcase"></i> Configurar negocio
            </a>
            <a href="<?=base_url()?>BotConfig" class="btn-saas btn-saas-outline">
                <i class="feather icon-message-square"></i> Configurar bot
            </a>
            <a href="<?=base_url()?>Products" class="btn-saas btn-saas-outline">
                <i class="feather icon-package"></i> Agregar productos
            </a>
        </div>
    </div>

    <!-- Last Activity -->
    <div style="margin-top: 2rem;">
        <div class="card">
            <div class="card-header">Última actividad</div>
            <div class="card-body" style="color: var(--saas-gray-500); font-size: 0.875rem;">
                <?php if(!empty($last_activity)): ?>
                    <p><?= $last_activity ?></p>
                <?php else: ?>
                    <p style="text-align: center; padding: 2rem 0; margin: 0;">No hay actividad reciente</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- [ content ] End -->

<script>
var dashUsId = <?= (int)$this->session->userdata('us_id') ?>;
var dashPollTimer = null;
var dashLastStatus = <?= json_encode($whatsapp_status ?? '') ?>;
var dashLastNumber = <?= json_encode($whatsapp_number ?? '') ?>;

function dashMapWa(statusText) {
    if (statusText === 'connected') return { label: 'Conectado', badge: 'connected', icon: 'green' };
    if (statusText === 'expired') return { label: 'Expirado', badge: 'disconnected', icon: 'red' };
    if (statusText === 'reconnecting') return { label: 'Reconectando', badge: 'reconnecting', icon: 'yellow' };
    if (statusText === 'waiting_scan') return { label: 'Esperando escaneo', badge: 'reconnecting', icon: 'yellow' };
    return { label: 'Desconectado', badge: 'disconnected', icon: 'red' };
}

function dashUpdateUI(statusText, whatsappNumber) {
    var m = dashMapWa(statusText);
    var badge = document.getElementById('dash_wa_badge');
    if (badge) {
        badge.classList.remove('connected', 'reconnecting', 'disconnected');
        badge.classList.add(m.badge);
        badge.textContent = m.label;
    }
    var icon = document.getElementById('dash_wa_icon');
    if (icon) {
        icon.classList.remove('green', 'yellow', 'red', 'blue', 'purple');
        icon.classList.add(m.icon);
    }
    var num = document.getElementById('dash_wa_number');
    if (num) num.textContent = whatsappNumber ? whatsappNumber : 'No conectado';

    var botBadge = document.getElementById('dash_bot_badge');
    if (botBadge) {
        var active = statusText === 'connected';
        botBadge.classList.remove('connected', 'disconnected', 'reconnecting');
        botBadge.classList.add(active ? 'connected' : 'disconnected');
        botBadge.textContent = active ? 'Activo' : 'Inactivo';
    }
}

function dashPollNow() {
    return fetch(base_url + 'BotApi/get_status/' + dashUsId)
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var data = (res && res.data) ? res.data : {};
            var statusText = (data && data.bs_status) ? data.bs_status : 'disconnected';
            var whatsappNumber = (data && data.bs_whatsapp_number) ? data.bs_whatsapp_number : '';

            if ((statusText || '') !== (dashLastStatus || '') || (whatsappNumber || '') !== (dashLastNumber || '')) {
                dashUpdateUI(statusText, whatsappNumber);
                dashLastStatus = statusText || '';
                dashLastNumber = whatsappNumber || '';
            }
        })
        .catch(function() {});
}

function dashStartPolling() {
    if (dashPollTimer) clearInterval(dashPollTimer);
    dashPollTimer = setInterval(dashPollNow, 15000);
    dashPollNow();
}

document.addEventListener('DOMContentLoaded', dashStartPolling);
</script>

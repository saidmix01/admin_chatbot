<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Configuración Técnica</h4>
        <p>Estado del servidor y herramientas de administración</p>
    </div>

    <div class="row" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin: 0;">

        <!-- Server Status -->
        <div class="stat-card">
            <div class="stat-card-icon green">
                <i class="feather icon-server"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Servidor</div>
                <div class="stat-card-value">
                    <span class="status-badge connected">Online</span>
                </div>
                <div class="stat-card-sub" style="margin-top: 0.25rem;">
                    <?= $server_software ?>
                </div>
            </div>
        </div>

        <!-- PHP Version -->
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="feather icon-code"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">PHP Version</div>
                <div class="stat-card-value"><?= $php_version ?></div>
                <div class="stat-card-sub">Entorno de ejecución</div>
            </div>
        </div>

        <!-- Bot Status -->
        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="feather icon-message-square"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-card-label">Bot WhatsApp</div>
                <div class="stat-card-value">
                    <span class="status-badge disconnected">Desconectado</span>
                </div>
                <div class="stat-card-sub">Verifica conexión</div>
            </div>
        </div>

    </div>

    <!-- Actions -->
    <div style="margin-top: 1.5rem;">
        <div class="card">
            <div class="card-header">Acciones del sistema</div>
            <div class="card-body">
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    <button class="btn-saas btn-saas-primary" onclick="restartBot()">
                        <i class="feather icon-refresh-cw"></i> Reiniciar bot
                    </button>
                    <button class="btn-saas btn-saas-outline" onclick="clearCache()">
                        <i class="feather icon-trash-2"></i> Limpiar caché
                    </button>
                    <button class="btn-saas btn-saas-outline" onclick="showLogs()">
                        <i class="feather icon-file-text"></i> Ver logs
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs -->
    <div style="margin-top: 1rem; display: none;" id="logs_section">
        <div class="card">
            <div class="card-header">Logs del sistema</div>
            <div class="card-body">
                <pre id="logs_content" style="background: #1f2937; color: #e5e7eb; padding: 1rem; border-radius: var(--saas-radius-sm); font-size: 0.75rem; max-height: 300px; overflow-y: auto; white-space: pre-wrap;">No hay logs disponibles</pre>
            </div>
        </div>
    </div>
</div>

<script>
function restartBot() {
    Swal.fire({
        title: 'Reiniciar bot',
        text: '¿Estás seguro de reiniciar el bot de WhatsApp?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, reiniciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire('Reiniciado', 'El bot se está reiniciando...', 'success');
        }
    });
}

function clearCache() {
    $.get('<?=base_url()?>TechConfig/clear_cache', function() {
        Swal.fire('Caché limpiado', 'La caché del sistema ha sido eliminada', 'success');
    });
}

function showLogs() {
    $('#logs_section').toggle();
}
</script>
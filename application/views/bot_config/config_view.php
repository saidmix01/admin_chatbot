<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Configuración del Bot</h4>
        <p>Personaliza los mensajes automáticos de tu WhatsApp</p>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Auto-replies toggle -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--saas-gray-100);">
                <div>
                    <div style="font-weight: 600; color: var(--saas-gray-800);">Respuestas automáticas</div>
                    <div style="font-size: 0.8125rem; color: var(--saas-gray-500);">Activar o desactivar el bot</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="auto_reply" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <form id="form_bot_config">
                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje de bienvenida</label>
                    <textarea class="form-saas" name="welcome_msg" rows="3" placeholder="¡Hola! Bienvenido a [nombre del negocio]. ¿En qué podemos ayudarte?">¡Hola! Bienvenido a [nombre del negocio]. ¿En qué podemos ayudarte?</textarea>
                    <small style="color: var(--saas-gray-400);">Se envía cuando un cliente escribe por primera vez</small>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje de menú</label>
                    <textarea class="form-saas" name="menu_msg" rows="4" placeholder="Elige una opción:
1. Ver productos
2. Horario
3. Ubicación
4. Hablar con un asesor">Elige una opción:
1. Ver productos
2. Horario
3. Ubicación
4. Hablar con un asesor</textarea>
                    <small style="color: var(--saas-gray-400);">Menú de opciones que ve el cliente</small>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje fuera de horario</label>
                    <textarea class="form-saas" name="offhours_msg" rows="3" placeholder="Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.">Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.</textarea>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje de despedida</label>
                    <textarea class="form-saas" name="goodbye_msg" rows="2" placeholder="¡Gracias por contactarnos! Que tengas un excelente día.">¡Gracias por contactarnos! Que tengas un excelente día.</textarea>
                </div>

                <hr style="border-color: var(--saas-gray-100); margin: 1.5rem 0;">

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn-saas btn-saas-primary">
                        <i class="feather icon-save"></i> Guardar configuración
                    </button>
                    <button type="reset" class="btn-saas btn-saas-outline">
                        Restablecer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#form_bot_config').on('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        icon: 'success',
        title: 'Configuración guardada',
        text: 'Los mensajes del bot han sido actualizados',
        timer: 2000
    });
});
</script>
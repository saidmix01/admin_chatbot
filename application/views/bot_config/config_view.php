<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Configuración del Bot</h4>
        <p>Personaliza los mensajes automáticos y horarios de atención de tu WhatsApp</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="form_bot_config">

                <!-- ──────── SECCIÓN: HORARIOS DE ATENCIÓN ──────── -->
                <h5 style="margin-bottom: 1rem;">
                    <i class="feather icon-clock" style="margin-right: 0.5rem;"></i>Horarios de Atención
                </h5>

                <div class="form-saas-group">
                    <label class="form-saas-label" style="display: flex; align-items: center; gap: 0.75rem;">
                        <input type="checkbox" name="schedule_enabled_toggle" id="schedule_enabled_toggle"
                            <?= !empty($store->sto_schedule_enabled) && $store->sto_schedule_enabled == 1 ? 'checked' : '' ?>
                        >
                        Activar horario de atención
                    </label>
                    <small style="color: var(--saas-gray-400);">
                        Si está desactivado, el bot atiende 24/7. Si lo activas, debes configurar los horarios.
                    </small>
                    <input type="hidden" name="schedule_enabled" id="schedule_enabled" value="<?= $store->sto_schedule_enabled ?? 0 ?>">
                </div>

                <div id="schedule_fields" style="display: <?= (!empty($store->sto_schedule_enabled) && $store->sto_schedule_enabled == 1) ? 'block' : 'none' ?>; margin-top: 1rem;">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Hora de apertura</label>
                            <input type="time" class="form-saas" name="schedule_open"
                                value="<?= $store->sto_schedule_open ?? '09:00' ?>">
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Hora de cierre</label>
                            <input type="time" class="form-saas" name="schedule_close"
                                value="<?= $store->sto_schedule_close ?? '18:00' ?>">
                        </div>
                    </div>

                    <div class="form-saas-group">
                        <label class="form-saas-label">Días de atención</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.25rem;">
                            <?php
                            $days_map = [
                                1 => 'Lun', 2 => 'Mar', 3 => 'Mié',
                                4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'
                            ];
                            $saved_days = explode(',', $store->sto_schedule_days ?? '1,2,3,4,5');
                            foreach ($days_map as $day_num => $day_label):
                                $checked = in_array((string)$day_num, $saved_days) ? 'checked' : '';
                            ?>
                                <label style="display: flex; align-items: center; gap: 0.25rem; cursor: pointer; padding: 0.35rem 0.6rem; border: 1px solid var(--saas-gray-100); border-radius: 4px; background: var(--saas-gray-50); user-select: none; font-size: 0.875rem;">
                                    <input type="checkbox" class="day-checkbox" value="<?= $day_num ?>" <?= $checked ?>>
                                    <?= $day_label ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="schedule_days" id="schedule_days" value="<?= $store->sto_schedule_days ?? '1,2,3,4,5' ?>">
                        <small style="color: var(--saas-gray-400);">Selecciona los días que tu negocio está abierto</small>
                    </div>
                </div>

                <hr style="border-color: var(--saas-gray-100); margin: 1.5rem 0;">

                <!-- ──────── SECCIÓN: MENSAJES AUTOMÁTICOS ──────── -->
                <h5 style="margin-bottom: 1rem;">
                    <i class="feather icon-message-square" style="margin-right: 0.5rem;"></i>Mensajes Automáticos
                </h5>

                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje de bienvenida</label>
                    <textarea class="form-saas" name="welcome_msg" rows="3" placeholder="¡Hola! Bienvenido a [nombre del negocio]. ¿En qué podemos ayudarte?"><?= $store->sto_wellcome_message ?? '¡Hola! Bienvenido a [nombre del negocio]. ¿En qué podemos ayudarte?' ?></textarea>
                    <small style="color: var(--saas-gray-400);">Se envía cuando un cliente escribe por primera vez. Usa <code>{business}</code> para el nombre del negocio.</small>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Palabras para iniciar el bot</label>
                    <?php
                        $starters_list = [];
                        if (!empty($store->sto_starters)) {
                            $decoded = json_decode($store->sto_starters, true);
                            if (is_array($decoded)) $starters_list = $decoded;
                        }
                    ?>
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;padding:0.5rem;border:1px solid var(--saas-gray-100);border-radius:12px;background:#fff;">
                        <div id="startersTags" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;flex:1 1 auto;min-width:0;"></div>
                        <input type="text" class="form-saas" id="startersInput" placeholder="Escribe y presiona Enter" style="border:none;box-shadow:none;outline:none;flex:1 1 180px;min-width:160px;padding:0.5rem;">
                        <input type="hidden" name="starters_json" id="starters_json" value="<?= htmlspecialchars(json_encode($starters_list, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div style="display:flex;gap:0.5rem;margin-top:0.5rem;">
                        <button type="button" class="btn-saas btn-saas-outline btn-saas-sm" onclick="clearStarters()">Limpiar</button>
                    </div>
                    <small style="color: var(--saas-gray-400);">Presiona Enter para agregar. Si lo dejas vacío, el bot inicia con cualquier mensaje.</small>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje fuera de horario</label>
                    <textarea class="form-saas" name="offhours_msg" rows="3" placeholder="Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos."><?= $store->sto_offhours_message ?? "Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos." ?></textarea>
                    <small style="color: var(--saas-gray-400);">Se envía cuando un cliente escribe fuera del horario configurado (solo si activaste horarios).</small>
                </div>

                <div class="form-saas-group">
                    <label class="form-saas-label">Mensaje de despedida</label>
                    <textarea class="form-saas" name="goodbye_msg" rows="2" placeholder="¡Gracias por contactarnos! Que tengas un excelente día."><?= $store->sto_goodbye_message ?? "¡Gracias por contactarnos! Que tengas un excelente día." ?></textarea>
                    <small style="color: var(--saas-gray-400);">Se envía al finalizar la conversación.</small>
                </div>

                <hr style="border-color: var(--saas-gray-100); margin: 1.5rem 0;">

                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" class="btn-saas btn-saas-primary">
                        <i class="feather icon-save"></i> Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var form = document.getElementById('form_bot_config');
    var toggle = document.getElementById('schedule_enabled_toggle');
    var scheduleFields = document.getElementById('schedule_fields');
    var scheduleEnabled = document.getElementById('schedule_enabled');
    var scheduleDays = document.getElementById('schedule_days');

    // ── Toggle horario ──
    toggle.addEventListener('change', function() {
        var val = this.checked ? 1 : 0;
        scheduleEnabled.value = val;
        scheduleFields.style.display = val ? 'block' : 'none';
    });

    // ── Días de atención checkboxes → hidden ──
    function updateScheduleDays() {
        var checks = document.querySelectorAll('.day-checkbox:checked');
        var vals = [];
        checks.forEach(function(cb) { vals.push(cb.value); });
        scheduleDays.value = vals.join(',');
    }

    document.querySelectorAll('.day-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateScheduleDays);
    });

    // ── Submit ──
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = this.querySelector('button[type="submit"]');
        var orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="feather icon-loader" style="animation: spin 1s linear infinite;"></i> Guardando...';

        // Collect form data into object
        var data = {};
        var fields = form.querySelectorAll('textarea, input[type="time"], input[type="hidden"]');
        fields.forEach(function(f) {
            if (f.name) data[f.name] = f.value;
        });
        // Override menu_msg directly from textarea
        data.welcome_msg = document.querySelector('textarea[name="welcome_msg"]').value;
        data.offhours_msg = document.querySelector('textarea[name="offhours_msg"]').value;
        data.goodbye_msg = document.querySelector('textarea[name="goodbye_msg"]').value;
        var starters = [];
        try { starters = JSON.parse(document.getElementById('starters_json').value || '[]') || []; } catch (e) { starters = []; }
        data.starters = starters;

        fetch(base_url + 'BotConfig/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(function(r) { return r.json(); })
        .then(function(r) {
            btn.disabled = false;
            btn.innerHTML = orig;
            if (r.status) {
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
})();
</script>

<script>
(function() {
    var tagsEl = document.getElementById('startersTags');
    var inputEl = document.getElementById('startersInput');
    var hiddenEl = document.getElementById('starters_json');
    if (!tagsEl || !inputEl || !hiddenEl) return;

    function normalize(v) {
        return (v || '').toString().trim().replace(/\s+/g, ' ');
    }

    function getList() {
        try {
            var arr = JSON.parse(hiddenEl.value || '[]');
            return Array.isArray(arr) ? arr : [];
        } catch (e) {
            return [];
        }
    }

    function setList(arr) {
        hiddenEl.value = JSON.stringify(arr);
    }

    function render() {
        var list = getList();
        tagsEl.innerHTML = '';
        list.forEach(function(t, idx) {
            var tag = document.createElement('span');
            tag.style.display = 'inline-flex';
            tag.style.alignItems = 'center';
            tag.style.gap = '0.35rem';
            tag.style.padding = '0.35rem 0.5rem';
            tag.style.borderRadius = '999px';
            tag.style.background = 'var(--saas-gray-50)';
            tag.style.border = '1px solid var(--saas-gray-100)';
            tag.style.fontSize = '0.85rem';
            tag.style.color = 'var(--saas-gray-700)';
            tag.textContent = t;

            var x = document.createElement('button');
            x.type = 'button';
            x.textContent = '×';
            x.style.border = 'none';
            x.style.background = 'transparent';
            x.style.cursor = 'pointer';
            x.style.color = 'var(--saas-gray-500)';
            x.style.fontSize = '1rem';
            x.style.lineHeight = '1';
            x.addEventListener('click', function() {
                var next = getList().filter(function(_, i) { return i !== idx; });
                setList(next);
                render();
            });

            tag.appendChild(x);
            tagsEl.appendChild(tag);
        });
    }

    function addValue(val) {
        var v = normalize(val);
        if (!v) return;
        var list = getList();
        var exists = list.some(function(x) { return normalize(x).toLowerCase() === v.toLowerCase(); });
        if (exists) return;
        list.push(v);
        setList(list);
        render();
    }

    window.clearStarters = function() {
        setList([]);
        render();
        inputEl.focus();
    };

    inputEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addValue(inputEl.value);
            inputEl.value = '';
        } else if (e.key === 'Backspace' && !inputEl.value) {
            var list = getList();
            if (list.length) {
                list.pop();
                setList(list);
                render();
            }
        }
    });

    inputEl.addEventListener('blur', function() {
        if (inputEl.value.trim()) {
            addValue(inputEl.value);
            inputEl.value = '';
        }
    });

    render();
})();
</script>

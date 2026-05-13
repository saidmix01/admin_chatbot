<div class="container-fluid flex-grow-1 container-p-y" style="min-height: calc(100vh - 60px);">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div style="background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="text-align: center; margin-bottom: 24px;">
                    <div style="font-size: 2.5rem; margin-bottom: 8px;">🧩</div>
                    <h5 style="margin: 0; font-size: 1.1rem;">Create a New Flow</h5>
                    <p class="text-muted" style="font-size: 0.85rem; margin-top: 4px;">Define a conversation path for your bot</p>
                </div>
                <form id="form_create">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Flow Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Sales, Support, Appointment Booking" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.9rem; outline: none; transition: border-color 0.15s;">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Description</label>
                        <textarea name="description" rows="2" placeholder="What is this flow for?" 
                                  style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.9rem; outline: none; transition: border-color 0.15s; resize: vertical;"></textarea>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Trigger Keyword</label>
                        <input type="text" name="trigger_value" placeholder="e.g. menu, buy, help" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.9rem; outline: none; transition: border-color 0.15s;">
                        <small style="color: #9ca3af; font-size: 0.75rem; margin-top: 4px; display: block;">When a customer types this word, the flow will start automatically.</small>
                    </div>
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Parent Flow (opcional)</label>
                        <select name="parent_flow_id" id="parent_flow_select" style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.9rem; outline: none; transition: border-color 0.15s;">
                            <option value="0">--- Ninguno (flujo raiz) ---</option>
                        </select>
                        <small style="color: #9ca3af; font-size: 0.75rem; margin-top: 4px; display: block;">Si es un subflujo, selecciona el flujo padre al que pertenece.</small>
                    </div>
                    <hr style="border-color: #f3f4f6; margin: 20px 0;">
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="<?= base_url() ?>FlowBuilder" class="flow-btn" style="padding: 10px 20px; font-size: 0.85rem;">Cancel</a>
                        <button type="submit" class="flow-btn flow-btn-primary" style="padding: 10px 24px; font-size: 0.85rem;">Create Flow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.flow-btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; font-size: 0.8rem; color: #374151; transition: all 0.15s; text-decoration: none; white-space: nowrap; }
.flow-btn:hover { border-color: #6366F1; color: #6366F1; }
.flow-btn-primary { background: #6366F1; border-color: #6366F1; color: #fff; }
.flow-btn-primary:hover { background: #4F46E5; color: #fff; }
input:focus, textarea:focus { border-color: #6366F1 !important; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }

.toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
.toast-item { padding: 10px 18px; border-radius: 10px; color: #fff; font-size: 0.8rem; font-weight: 500; box-shadow: 0 8px 24px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease; max-width: 340px; display: flex; align-items: center; gap: 8px; pointer-events: auto; }
.toast-item.success { background: #22C55E; }
.toast-item.error { background: #EF4444; }
.toast-item.info { background: #6366F1; }
@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div id='toastContainer' class='toast-container'></div>
<script>

function showToast(msg, type) { type = type || 'info'; var c = document.getElementById('toastContainer'); if (!c) return; var t = document.createElement('div'); t.className = 'toast-item ' + type; t.innerHTML = msg; c.appendChild(t); setTimeout(function() { t.style.opacity = '0'; t.style.transition = 'opacity 0.3s'; setTimeout(function() { t.remove(); }, 300); }, 3000); }

    // Load existing flows for parent selector
    fetch(BASE_URL + 'FlowBuilder/api_flow_list')
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.status || !res.data) return;
            var sel = document.getElementById('parent_flow_select');
            if (!sel) return;
            res.data.forEach(function(f) {
                var opt = document.createElement('option');
                opt.value = f.id;
                opt.textContent = f.name;
                sel.appendChild(opt);
            });
        });

document.getElementById('form_create').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true; btn.innerHTML = 'Creating...';
    const BASE_URL = '<?= base_url() ?>';
    fetch(BASE_URL + 'FlowBuilder/create', {
        method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(new FormData(this))
    }).then(function(r) { return r.json(); }).then(function(d) {
        if (d.status) window.location = '<?= base_url() ?>FlowBuilder/edit/' + d.id;
        else showToast(d.message, "error");
        btn.disabled = false; btn.innerHTML = 'Create Flow';
    }).catch(function() { btn.disabled = false; btn.innerHTML = 'Create Flow'; });
});
</script>

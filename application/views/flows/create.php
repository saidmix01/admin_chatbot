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
</style>

<script>
document.getElementById('form_create').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true; btn.innerHTML = 'Creating...';
    fetch('<?= base_url() ?>FlowBuilder/create', {
        method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(new FormData(this))
    }).then(function(r) { return r.json(); }).then(function(d) {
        if (d.status) window.location = '<?= base_url() ?>FlowBuilder/edit/' + d.id;
        else alert(d.message);
        btn.disabled = false; btn.innerHTML = 'Create Flow';
    }).catch(function() { btn.disabled = false; btn.innerHTML = 'Create Flow'; });
});
</script>

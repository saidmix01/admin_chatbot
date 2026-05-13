<div class="container-fluid flex-grow-1 container-p-y" style="background: #f3f4f6; min-height: calc(100vh - 60px);">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 10px;">
        <div>
            <h4 style="display: flex; align-items: center; gap: 8px; margin: 0; font-size: 1.1rem;">
                <span style="width: 8px; height: 8px; background: <?= $version->status === 'published' ? '#22C55E' : '#6366F1'; ?>; border-radius: 50%; display: inline-block;"></span>
                <?= htmlspecialchars($flow->name) ?>
                <span style="font-size: 0.75rem; font-weight: 400; color: #6b7280; background: #f3f4f6; padding: 2px 10px; border-radius: 20px;">
                    v<?= $version->version ?> · <?= $version->status ?>
                </span>
            </h4>
        </div>
        <div class="d-flex" style="gap: 6px;">
            <input type="text" id="trigger-value" value="<?= !empty($triggers) ? htmlspecialchars($triggers[0]->trigger_value) : '' ?>" placeholder="Keyword trigger" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 12px; font-size: 0.8rem; outline: none; width: 150px;">
            <button onclick="saveTrigger(<?= $flow->id ?>)" class="flow-btn flow-btn-outline" style="font-size: 0.75rem;">Trigger</button>
            <button onclick="saveNodes(<?= $version->id ?>)" class="flow-btn flow-btn-primary" style="font-size: 0.75rem;">💾 Save</button>
            <button onclick="validateFlow(<?= $version->id ?>)" class="flow-btn flow-btn-outline" style="font-size: 0.75rem;">✅ Validate</button>
            <button onclick="publishFlow(<?= $version->id ?>)" class="flow-btn" style="font-size: 0.75rem; background: #22C55E; border-color: #22C55E; color: #fff;">🚀 Publish</button>
            <div class="dropdown" style="position: relative;">
                <button class="flow-btn flow-btn-outline" style="font-size: 0.75rem;" onclick="document.getElementById('flowActionsDropdown').classList.toggle('show')">⋮</button>
                <div id="flowActionsDropdown" class="flow-dropdown-menu" style="position: absolute; right: 0; top: 100%; z-index: 100; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 6px; min-width: 160px; display: none;">
                    <a href="<?= base_url() ?>FlowBuilder" style="display: block; padding: 8px 12px; font-size: 0.8rem; color: #374151; border-radius: 6px; text-decoration: none;">← Back to flows</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin: 0;">
        <!-- Node Canvas -->
        <div class="col-md-9" style="padding: 0 8px 0 0;">
            <div class="flow-canvas" id="flowCanvas">
                <div class="flow-canvas-header">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span style="font-size: 0.8rem; font-weight: 600; color: #374151;">Canvas</span>
                        <span class="text-muted" style="font-size: 0.7rem;" id="nodeCount">0 nodes</span>
                    </div>
                    <div class="d-flex" style="gap: 6px;">
                        <button class="flow-btn flow-btn-outline" style="font-size: 0.7rem; padding: 3px 10px;" onclick="togglePreview()">👁 Preview</button>
                        <button class="flow-btn flow-btn-primary" style="font-size: 0.7rem; padding: 3px 10px;" onclick="openNewNodeModal()">+ Add Node</button>
                    </div>
                </div>
                <div class="flow-canvas-body" id="canvasBody">
                    <div id="flowEmptyState" class="flow-empty-state">
                        <div class="flow-empty-icon">⚡</div>
                        <h5>Start building your flow</h5>
                        <p class="text-muted">Add nodes and connect them to create a conversation path.</p>
                        <button class="flow-btn flow-btn-primary" onclick="openNewNodeModal()">+ Add First Node</button>
                    </div>
                    <div id="nodesContainer" class="flow-nodes-container"></div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-3" style="padding: 0 0 0 8px;">
            <!-- Node palette -->
            <div class="flow-panel">
                <div class="flow-panel-header">Node Types</div>
                <div class="flow-panel-body">
                    <div class="flow-palette-item" onclick="quickAddNode('message')">
                        <span class="flow-type-badge" style="background: #e0f2fe; color: #0284c7;">💬</span>
                        <div><strong style="font-size: 0.8rem;">Message</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Send a text</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('question')">
                        <span class="flow-type-badge" style="background: #fef3c7; color: #d97706;">❓</span>
                        <div><strong style="font-size: 0.8rem;">Question</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Ask & save input</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('choice')">
                        <span class="flow-type-badge" style="background: #ede9fe; color: #7c3aed;">📋</span>
                        <div><strong style="font-size: 0.8rem;">Choice</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Show options</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('condition')">
                        <span class="flow-type-badge" style="background: #fce7f3; color: #db2777;">🔀</span>
                        <div><strong style="font-size: 0.8rem;">Condition</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Branch logic</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('action_webhook')">
                        <span class="flow-type-badge" style="background: #d1fae5; color: #059669;">🔗</span>
                        <div><strong style="font-size: 0.8rem;">Webhook</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">API call</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('goto')">
                        <span class="flow-type-badge" style="background: #e0e7ff; color: #4338ca;">➡️</span>
                        <div><strong style="font-size: 0.8rem;">Go To</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Jump to node</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('end')">
                        <span class="flow-type-badge" style="background: #f3f4f6; color: #6b7280;">⏹️</span>
                        <div><strong style="font-size: 0.8rem;">End</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Finish flow</small></div>
                    </div>
                </div>
            </div>

            <!-- Preview panel -->
            <div class="flow-panel" id="previewPanel" style="display: none;">
                <div class="flow-panel-header">Flow Preview</div>
                <div class="flow-panel-body">
                    <div id="flowPreview" style="font-size: 0.8rem; line-height: 1.6;">
                        <span class="text-muted">Add nodes to see preview...</span>
                    </div>
                </div>
            </div>

            <?php if ($published): ?>
            <div class="flow-panel" style="border-left: 3px solid #22C55E;">
                <div class="flow-panel-header" style="color: #22C55E;">Published v<?= $published->version ?></div>
                <div class="flow-panel-body">
                    <small class="text-muted"><?= htmlspecialchars($published->published_at ?? '') ?></small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Node Edit Modal -->
<div class="modal fade" id="nodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 80px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border: none; padding: 20px 24px 0;">
                <h5 class="modal-title" style="font-size: 1rem;" id="modalTitle">
                    <span id="modalTitleIcon">⚡</span>
                    <span id="modalTitleText">New Node</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="font-size: 1.4rem;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 16px 24px;">
                <input type="hidden" id="edit-idx" value="-1">
                
                <div class="form-row" style="display: flex; gap: 12px; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <label class="flow-label">Node Key</label>
                        <input type="text" id="modal-key" class="flow-input" placeholder="e.g. start, ask_name, show_menu" style="font-family: monospace; font-size: 0.85rem;">
                    </div>
                    <div style="flex: 1;">
                        <label class="flow-label">Type</label>
                        <select id="modal-type" class="flow-input" onchange="toggleModalFields()">
                            <option value="message">💬 Message</option>
                            <option value="question">❓ Question</option>
                            <option value="choice">📋 Choice</option>
                            <option value="condition">🔀 Condition</option>
                            <option value="action_webhook">🔗 Webhook</option>
                            <option value="goto">➡️ Go To</option>
                            <option value="end">⏹️ End</option>
                        </select>
                    </div>
                </div>

                <!-- Message fields -->
                <div id="modal-fields-message" class="modal-fields">
                    <div class="flow-field">
                        <label class="flow-label">Message Text</label>
                        <textarea id="msg-text" class="flow-input" rows="3" placeholder="Hello {{name}}, welcome! How can I help you?"></textarea>
                    </div>
                </div>

                <!-- Question fields -->
                <div id="modal-fields-question" class="modal-fields" style="display:none">
                    <div class="flow-field">
                        <label class="flow-label">Question</label>
                        <textarea id="q-text" class="flow-input" rows="2" placeholder="What's your name?"></textarea>
                    </div>
                    <div class="form-row" style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <label class="flow-label">Save to variable</label>
                            <input type="text" id="q-save" class="flow-input" placeholder="name">
                        </div>
                        <div style="flex: 1;">
                            <label class="flow-label">Retry text</label>
                            <input type="text" id="q-retry" class="flow-input" placeholder="Invalid answer, try again">
                        </div>
                    </div>
                </div>

                <!-- Choice fields -->
                <div id="modal-fields-choice" class="modal-fields" style="display:none">
                    <div class="flow-field">
                        <label class="flow-label">Text</label>
                        <textarea id="c-text" class="flow-input" rows="2" placeholder="Choose an option:"></textarea>
                    </div>
                    <div class="flow-field">
                        <label class="flow-label">Options (one per line: value|Label)</label>
                        <textarea id="c-options" class="flow-input" rows="4" placeholder="1|Sales&#10;2|Support&#10;3|Info"></textarea>
                    </div>
                    <div class="flow-field">
                        <label class="flow-label">Save to variable</label>
                        <input type="text" id="c-save" class="flow-input" placeholder="selection">
                    </div>
                </div>

                <!-- Condition fields -->
                <div id="modal-fields-condition" class="modal-fields" style="display:none">
                    <div class="form-row" style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <label class="flow-label">Variable</label>
                            <input type="text" id="cond-var" class="flow-input" placeholder="name">
                        </div>
                        <div style="flex: 1;">
                            <label class="flow-label">Operator</label>
                            <select id="cond-op" class="flow-input">
                                <option value="equals">= equals</option>
                                <option value="not_equals">!= not equals</option>
                                <option value="gt">> greater</option>
                                <option value="lt">&lt; less</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label class="flow-label">Value</label>
                            <input type="text" id="cond-val" class="flow-input" placeholder="yes">
                        </div>
                    </div>
                    <div class="form-row" style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <label class="flow-label">Go to (true)</label>
                            <input type="text" id="cond-true" class="flow-input" placeholder="node_key" list="nodes-list">
                        </div>
                        <div style="flex: 1;">
                            <label class="flow-label">Go to (false)</label>
                            <input type="text" id="cond-false" class="flow-input" placeholder="node_key" list="nodes-list">
                        </div>
                    </div>
                </div>

                <!-- Webhook fields -->
                <div id="modal-fields-webhook" class="modal-fields" style="display:none">
                    <div class="flow-field">
                        <label class="flow-label">Webhook URL</label>
                        <input type="text" id="wh-url" class="flow-input" placeholder="https://example.com/api">
                    </div>
                    <div class="form-row" style="display: flex; gap: 12px;">
                        <div style="flex: 1;">
                            <label class="flow-label">Method</label>
                            <select id="wh-method" class="flow-input"><option value="POST">POST</option><option value="GET">GET</option></select>
                        </div>
                        <div style="flex: 1;">
                            <label class="flow-label">Save to variable</label>
                            <input type="text" id="wh-save" class="flow-input" placeholder="api_result">
                        </div>
                    </div>
                </div>

                <!-- GoTo fields -->
                <div id="modal-fields-goto" class="modal-fields" style="display:none">
                    <div class="flow-field">
                        <label class="flow-label">Go to node</label>
                        <input type="text" id="goto-to" class="flow-input" placeholder="node_key" list="nodes-list">
                    </div>
                </div>

                <datalist id="nodes-list"></datalist>
            </div>
            <div class="modal-footer" style="border: none; padding: 0 24px 20px; justify-content: space-between;">
                <button class="flow-btn flow-btn-danger" id="deleteNodeBtn" style="display: none; font-size: 0.75rem;" onclick="deleteCurrentNode()">🗑 Delete</button>
                <div>
                    <button class="flow-btn flow-btn-outline" data-dismiss="modal" style="font-size: 0.8rem; margin-right: 6px;">Cancel</button>
                    <button class="flow-btn flow-btn-primary" onclick="saveNodeModal()" style="font-size: 0.8rem;">💾 Save Node</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edge connection modal -->
<div class="modal fade" id="edgeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 25px 80px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border: none; padding: 20px 24px 0;">
                <h5 class="modal-title" style="font-size: 1rem;">🔗 Connect Nodes</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 16px 24px;">
                <input type="hidden" id="edge-edit-idx" value="-1">
                <div class="flow-field">
                    <label class="flow-label">From node</label>
                    <select id="edge-from" class="flow-input"></select>
                </div>
                <div class="flow-field">
                    <label class="flow-label">To node</label>
                    <select id="edge-to" class="flow-input"></select>
                </div>
                <div class="flow-field">
                    <label class="flow-label">Rule (optional JSON)</label>
                    <input type="text" id="edge-rule" class="flow-input" placeholder='e.g. {"when":"choice","equals":2}'>
                </div>
            </div>
            <div class="modal-footer" style="border: none; padding: 0 24px 20px;">
                <button class="flow-btn flow-btn-outline" data-dismiss="modal" style="margin-right: 6px;">Cancel</button>
                <button class="flow-btn flow-btn-primary" onclick="saveEdgeModal()">🔗 Connect</button>
            </div>
        </div>
    </div>
</div>

<style>
.flow-canvas { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; }
.flow-canvas-header { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-bottom: 1px solid #f3f4f6; }
.flow-canvas-body { padding: 20px; min-height: 500px; }
.flow-empty-state { text-align: center; padding: 60px 20px; }
.flow-empty-icon { font-size: 3rem; margin-bottom: 12px; }
.flow-nodes-container { display: flex; flex-direction: column; gap: 10px; }
.flow-panel { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 10px; overflow: hidden; }
.flow-panel-header { padding: 10px 14px; font-size: 0.8rem; font-weight: 600; color: #374151; border-bottom: 1px solid #f3f4f6; }
.flow-panel-body { padding: 10px; }
.flow-palette-item { display: flex; align-items: center; gap: 10px; padding: 7px 8px; border-radius: 8px; cursor: pointer; transition: background 0.15s; }
.flow-palette-item:hover { background: #f9fafb; }
.flow-type-badge { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
.flow-btn { padding: 6px 14px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; font-size: 0.8rem; color: #374151; transition: all 0.15s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
.flow-btn:hover { border-color: #6366F1; color: #6366F1; }
.flow-btn-primary { background: #6366F1; border-color: #6366F1; color: #fff; }
.flow-btn-primary:hover { background: #4F46E5; color: #fff; }
.flow-btn-outline { background: transparent; border-color: #d1d5db; }
.flow-btn-danger { background: transparent; border-color: #fca5a5; color: #dc2626; }
.flow-btn-danger:hover { background: #fef2f2; border-color: #dc2626; }
.flow-input { width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.85rem; outline: none; transition: border-color 0.15s; background: #fff; }
.flow-input:focus { border-color: #6366F1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.flow-label { display: block; font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 4px; }
.flow-field { margin-bottom: 10px; }
.flow-dropdown-menu.show { display: block !important; }

/* Visual Node Cards */
.flow-visual-node { background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px 16px; cursor: pointer; transition: all 0.2s; position: relative; }
.flow-visual-node:hover { border-color: #6366F1; box-shadow: 0 4px 12px rgba(99,102,241,0.1); }
.flow-visual-node.node-start { border-left: 4px solid #22C55E; }
.flow-visual-node.node-end { border-left: 4px solid #6b7280; }
.flow-visual-node.node-message { border-left: 4px solid #0284c7; }
.flow-visual-node.node-question { border-left: 4px solid #d97706; }
.flow-visual-node.node-choice { border-left: 4px solid #7c3aed; }
.flow-visual-node.node-condition { border-left: 4px solid #db2777; }
.flow-visual-node.node-action_webhook { border-left: 4px solid #059669; }
.flow-visual-node.node-goto { border-left: 4px solid #4338ca; }
.flow-visual-node .node-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
.flow-visual-node .node-key { font-family: monospace; font-size: 0.8rem; font-weight: 600; color: #374151; }
.flow-visual-node .node-type-badge { font-size: 0.65rem; padding: 2px 8px; border-radius: 10px; font-weight: 500; }
.flow-visual-node .node-summary { margin-top: 4px; font-size: 0.75rem; color: #6b7280; }
.flow-visual-node .node-connections { display: flex; gap: 4px; margin-top: 6px; flex-wrap: wrap; }
.flow-visual-node .node-conn { font-size: 0.65rem; background: #f3f4f6; padding: 2px 8px; border-radius: 6px; color: #6b7280; }
.flow-visual-node .node-conn i { color: #6366F1; }
.flow-visual-node .node-actions { position: absolute; top: 8px; right: 8px; display: none; gap: 2px; }
.flow-visual-node:hover .node-actions { display: flex; }
.flow-visual-node .node-actions button { width: 24px; height: 24px; border: none; background: #f3f4f6; border-radius: 6px; cursor: pointer; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; }
.flow-visual-node .node-actions button:hover { background: #e5e7eb; }
.flow-visual-node.dragging { opacity: 0.5; }
.flow-visual-node.drag-over { border-color: #6366F1; border-style: dashed; }
@media (max-width: 768px) { .col-md-9, .col-md-3 { padding: 0 !important; } }
/* Toast */
.toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 8px; }
.toast-msg { padding: 12px 20px; border-radius: 10px; color: #fff; font-size: 0.85rem; font-weight: 500; box-shadow: 0 10px 30px rgba(0,0,0,0.15); animation: toastIn 0.3s ease; max-width: 360px; display: flex; align-items: center; gap: 8px; }
.toast-msg.success { background: #22C55E; }
.toast-msg.error { background: #EF4444; }
.toast-msg.info { background: #6366F1; }
 toastIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div id=	oastContainer class=	oast-container></div>
<script>


// Close dropdown on outside click
document.addEventListener('click', function(e) {
    var dd = document.getElementById('flowActionsDropdown');
    if (dd && dd.classList.contains('show') && !e.target.closest('.dropdown')) {
        dd.classList.remove('show');
    }
});

var nodes = <?= json_encode(array_map(function($n) {
    $p = json_decode($n->payload_json, true) ?: [];
    return ['node_key' => $n->node_key, 'type' => $n->type, 'payload' => $p];
}, $nodes)) ?>;
var edges = <?= json_encode(array_map(function($e) {
    $r = $e->rule_json ? json_decode($e->rule_json, true) : null;
    return ['from' => $e->from_node_key, 'to' => $e->to_node_key, 'rule' => $r];
}, $edges)) ?>;

function toggleModalFields() {
    var type = document.getElementById('modal-type').value;
    document.querySelectorAll('.modal-fields').forEach(function(el) { el.style.display = 'none'; });
    var f = document.getElementById('modal-fields-' + type);
    if (f) f.style.display = 'block';
    if (type === 'end') document.getElementById('modal-fields-message').style.display = 'block';
    // Update node list for condition/goto
    var list = document.getElementById('nodes-list');
    list.innerHTML = nodes.map(function(n) { return '<option value="' + n.node_key + '">'; }).join('');
}

function getIcon(type) {
    var icons = { 'start': '⚡', 'message': '💬', 'question': '❓', 'choice': '📋', 'condition': '🔀', 'action_webhook': '🔗', 'goto': '➡️', 'end': '⏹️' };
    return icons[type] || '⚡';
}

function getTypeLabel(type) {
    var labels = { 'start': 'Start', 'message': 'Message', 'question': 'Question', 'choice': 'Choice', 'condition': 'Condition', 'action_webhook': 'Webhook', 'goto': 'Go To', 'end': 'End' };
    return labels[type] || type;
}

function getNodeSummary(n) {
    var p = n.payload || {};
    switch (n.type) {
        case 'message': return (p.text||'').substring(0, 60);
        case 'question': return '❓ ' + (p.save_to||'?') + ': "' + (p.text||'').substring(0, 40) + '"';
        case 'choice': return '📋 ' + (p.options||[]).length + ' options → ' + (p.save_to||'var');
        case 'condition': return '🔀 if ' + (p.if ? (p.if.var||'?') + ' ' + (p.if.op||'==') + ' ' + (p.if.value||'') : '?');
        case 'action_webhook': return '🔗 ' + ((p.url||'').substring(0, 40)||'');
        case 'goto': return '➡️ → ' + (p.to||'?');
        case 'end': return '⏹️ End flow';
        default: return '';
    }
}

function renderVisualCanvas() {
    var container = document.getElementById('nodesContainer');
    var empty = document.getElementById('flowEmptyState');
    var count = document.getElementById('nodeCount');

    if (!nodes.length) {
        container.innerHTML = '';
        empty.style.display = 'block';
        count.textContent = '0 nodes';
        return;
    }
    
    empty.style.display = 'none';
    count.textContent = nodes.length + ' nodes · ' + edges.length + ' connections';

    var html = '<div class="flow-visual-list">';
    nodes.forEach(function(n, i) {
        var outgoing = edges.filter(function(e) { return e.from === n.node_key; });
        var incoming = edges.filter(function(e) { return e.to === n.node_key; });
        var outStr = outgoing.map(function(e) { return '<span class="node-conn">→ ' + e.to + (e.rule ? ' <small style="color:#9ca3af">'+JSON.stringify(e.rule)+'</small>' : '') + '</span>'; }).join('');
        var inStr = incoming.map(function(e) { return '<span class="node-conn"><i>← ' + e.from + '</i></span>'; }).join('');

        var isStart = n.node_key === 'start';
        var isEnd = n.type === 'end';
        
        html += '<div class="flow-visual-node node-' + n.type + (isStart ? ' node-start' : '') + (isEnd ? ' node-end' : '') + '" draggable="true" onclick="editNode(' + i + ')" data-idx="' + i + '" ondragstart="onNodeDragStart(event)" ondragover="onNodeDragOver(event)" ondrop="onNodeDrop(event)" ondragend="onNodeDragEnd(event)">';
        html += '<div class="node-actions" onclick="event.stopPropagation();">';
        html += '<button onclick="addEdgeFrom(' + i + ')" title="Connect">🔗</button>';
        html += '<button onclick="event.stopPropagation(); nodes.splice(' + i + ',1); renderVisualCanvas(); }" title="Delete">✕</button>';
        html += '</div>';
        html += '<div class="node-top">';
        html += '<div><span class="node-key">' + getIcon(n.type) + ' ' + n.node_key + '</span> <span class="node-type-badge" style="background:#f3f4f6">' + getTypeLabel(n.type) + '</span></div>';
        html += '<div style="font-size:0.65rem;color:#9ca3af;">#' + (i+1) + '</div>';
        html += '</div>';
        html += '<div class="node-summary">' + getNodeSummary(n) + '</div>';
        if (inStr || outStr) {
            html += '<div class="node-connections">' + inStr + outStr + '</div>';
        }
        html += '</div>';
    });
    html += '</div>';
    container.innerHTML = html;
}

// --- DRAG & DROP for reordering nodes ---
var dragIdx = null;

window.onNodeDragStart = function(e) {
    var el = e.target.closest('.flow-visual-node');
    if (!el) return;
    dragIdx = parseInt(el.dataset.idx);
    el.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', dragIdx);
};

window.onNodeDragOver = function(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    var el = e.target.closest('.flow-visual-node');
    if (el && !el.classList.contains('dragging')) el.classList.add('drag-over');
};

window.onNodeDragEnd = function(e) {
    document.querySelectorAll('.flow-visual-node').forEach(function(el) { el.classList.remove('dragging', 'drag-over'); });
    dragIdx = null;
};

window.onNodeDrop = function(e) {
    e.preventDefault();
    document.querySelectorAll('.flow-visual-node').forEach(function(el) { el.classList.remove('dragging', 'drag-over'); });
    var fromIdx = parseInt(e.dataTransfer.getData('text/plain'));
    var target = e.target.closest('.flow-visual-node');
    if (!target || isNaN(fromIdx)) return;
    var toIdx = parseInt(target.dataset.idx);
    if (fromIdx === toIdx) return;
    // Reorder
    var item = nodes.splice(fromIdx, 1)[0];
    nodes.splice(toIdx, 0, item);
    renderVisualCanvas();
};

// --- Node CRUD ---
function openNewNodeModal() {
    document.getElementById('edit-idx').value = '-1';
    document.getElementById('modalTitleText').textContent = 'New Node';
    document.getElementById('modalTitleIcon').textContent = '⚡';
    document.getElementById('modal-key').value = '';
    document.getElementById('deleteNodeBtn').style.display = 'none';
    toggleModalFields();
    $('#nodeModal').modal('show');
}

function editNode(idx) {
    var n = nodes[idx];
    document.getElementById('edit-idx').value = idx;
    document.getElementById('modalTitleText').textContent = 'Edit: ' + n.node_key;
    document.getElementById('modalTitleIcon').textContent = getIcon(n.type);
    document.getElementById('modal-key').value = n.node_key;
    document.getElementById('modal-type').value = n.type;
    document.getElementById('deleteNodeBtn').style.display = 'inline-block';
    var p = n.payload || {};
    document.getElementById('msg-text').value = p.text || '';
    document.getElementById('q-text').value = p.text || '';
    document.getElementById('q-save').value = p.save_to || '';
    document.getElementById('q-retry').value = p.retry_text || '';
    document.getElementById('c-text').value = p.text || '';
    document.getElementById('c-options').value = (p.options || []).map(function(o) { return o.value + '|' + o.label; }).join('\n');
    document.getElementById('c-save').value = p.save_to || '';
    document.getElementById('cond-var').value = p.if ? p.if.var : '';
    document.getElementById('cond-op').value = p.if ? (p.if.op || 'equals') : 'equals';
    document.getElementById('cond-val').value = p.if ? p.if.value : '';
    document.getElementById('cond-true').value = p.true_to || '';
    document.getElementById('cond-false').value = p.false_to || '';
    document.getElementById('wh-url').value = p.url || '';
    document.getElementById('wh-method').value = p.method || 'POST';
    document.getElementById('wh-save').value = p.save_to || '';
    document.getElementById('goto-to').value = p.to || '';
    toggleModalFields();
    $('#nodeModal').modal('show');
}

function deleteCurrentNode() {
    var idx = parseInt(document.getElementById('edit-idx').value);
    if (isNaN(idx) || idx < 0) return;
    
    var key = nodes[idx].node_key;
    nodes.splice(idx, 1);
    edges = edges.filter(function(e) { return e.from !== key && e.to !== key; });
    $('#nodeModal').modal('hide');
    renderVisualCanvas();
}

function saveNodeModal() {
    var idx = parseInt(document.getElementById('edit-idx').value);
    var key = document.getElementById('modal-key').value.trim();
    if (!key) { showToast('Node key is required', 'error'); return; }
    var type = document.getElementById('modal-type').value;
    var payload = {};
    switch (type) {
        case 'message': payload = { text: document.getElementById('msg-text').value }; break;
        case 'question': payload = { text: document.getElementById('q-text').value, save_to: document.getElementById('q-save').value, retry_text: document.getElementById('q-retry').value }; break;
        case 'choice':
            var lines = document.getElementById('c-options').value.split('\n').filter(Boolean);
            var opts = lines.map(function(l) { var p = l.split('|'); return { value: parseInt(p[0]) || 1, label: p[1] || p[0] }; });
            payload = { text: document.getElementById('c-text').value, options: opts, save_to: document.getElementById('c-save').value }; break;
        case 'condition': payload = { if: { var: document.getElementById('cond-var').value, op: document.getElementById('cond-op').value, value: document.getElementById('cond-val').value }, true_to: document.getElementById('cond-true').value, false_to: document.getElementById('cond-false').value }; break;
        case 'action_webhook': payload = { url: document.getElementById('wh-url').value, method: document.getElementById('wh-method').value, save_to: document.getElementById('wh-save').value }; break;
        case 'goto': payload = { to: document.getElementById('goto-to').value }; break;
        default: payload = {};
    }
    if (isNaN(idx) || idx === -1) {
        nodes.push({ node_key: key, type: type, payload: payload });
    } else {
        // Update edges if key changed
        var oldKey = nodes[idx].node_key;
        if (oldKey !== key) {
            edges.forEach(function(e) {
                if (e.from === oldKey) e.from = key;
                if (e.to === oldKey) e.to = key;
            });
        }
        nodes[idx].node_key = key;
        nodes[idx].type = type;
        nodes[idx].payload = payload;
    }
    $('#nodeModal').modal('hide');
    renderVisualCanvas();
}

function quickAddNode(type) {
    var base = { 'message': 'msg', 'question': 'ask', 'choice': 'menu', 'condition': 'if', 'action_webhook': 'api', 'goto': 'go', 'end': 'end' };
    var key = base[type] || 'node';
    var i = 1;
    while (nodes.some(function(n) { return n.node_key === key + i; })) i++;
    nodes.push({ node_key: key + i, type: type, payload: {} });
    renderVisualCanvas();
}

// --- Edge management ---
function addEdgeFrom(idx) {
    var n = nodes[idx];
    updateEdgeSelects();
    document.getElementById('edge-edit-idx').value = '-1';
    document.getElementById('edge-from').value = n.node_key;
    document.getElementById('edge-to').value = '';
    document.getElementById('edge-rule').value = '';
    $('#edgeModal').modal('show');
}

function updateEdgeSelects() {
    var from = document.getElementById('edge-from');
    var to = document.getElementById('edge-to');
    var opts = '<option value="">— select —</option>' + nodes.map(function(n) { return '<option value="' + n.node_key + '">' + getIcon(n.type) + ' ' + n.node_key + '</option>'; }).join('');
    from.innerHTML = opts;
    to.innerHTML = opts;
}

function saveEdgeModal() {
    var from = document.getElementById('edge-from').value;
    var to = document.getElementById('edge-to').value;
    if (!from || !to) { showToast('Select From and To nodes', 'error'); return; }
    if (from === to) { showToast('Cannot connect a node to itself', 'error'); return; }
    if (edges.some(function(e) { return e.from === from && e.to === to; })) { showToast('Connection already exists', 'error'); return; }
    var rule = document.getElementById('edge-rule').value.trim();
    var ruleObj = rule ? (function(){ try { return JSON.parse(rule); } catch(e) { return rule; } })() : null;
    edges.push({ from: from, to: to, rule: ruleObj });
    $('#edgeModal').modal('hide');
    renderVisualCanvas();
}

// --- Actions ---
function saveTrigger(fid) {
    var val = document.getElementById('trigger-value').value.trim();
    fetch('<?= base_url() ?>FlowBuilder/save_trigger/' + fid, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'trigger_value=' + encodeURIComponent(val)
    }).then(function(r) { return r.json(); }).then(function(d) { showToast(d.message, d.status ? "success" : "error"); showToast(d.message, d.status ? 'success' : 'error'); });
}

function saveNodes(vid) {
    fetch('<?= base_url() ?>FlowBuilder/save_nodes/' + vid, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ nodes: nodes, edges: edges })
    }).then(function(r) { return r.json(); }).then(function(d) { showToast(d.message, d.status ? "success" : "error"); showToast(d.message, d.status ? 'success' : 'error'); });
}

function validateFlow(vid) {
    fetch('<?= base_url() ?>FlowBuilder/validate_version/' + vid)
    .then(function(r) { return r.json(); }).then(function(d) {
        var msg = d.status ? '✅ ' + d.message : '❌ Errors:\n' + d.errors.join('\n');
        showToast(msg, d.status ? "success" : "error");
    });
}

function publishFlow(vid) {
    showToast('Publishing...', 'info');
    fetch('<?= base_url() ?>FlowBuilder/publish/' + vid)
    .then(function(r) { return r.json(); }).then(function(d) {
        showToast(d.message, d.status ? "success" : "error"); if (d.status) { setTimeout(function() { location.reload(); }, 1500); }
    });
}

function togglePreview() {
    var panel = document.getElementById('previewPanel');
    panel.style.display = panel.style.display === 'none' ? '' : 'none';
    renderPreview();
}

function renderPreview() {
    var div = document.getElementById('flowPreview');
    if (!nodes.length) { div.innerHTML = '<span class="text-muted">Add nodes first...</span>'; return; }
    var start = nodes.find(function(n) { return n.node_key === 'start'; }) || nodes[0];
    var h = '';
    var visited = {};
    var cur = start.node_key;
    for (var i = 0; i < 50; i++) {
        if (visited[cur]) { h += '<span class="text-warning">⚠️ Loop detected at ' + cur + '</span><br>'; break; }
        visited[cur] = true;
        var n = nodes.find(function(nn) { return nn.node_key === cur; });
        if (!n) { h += '<span class="text-danger">⛔ Node "' + cur + '" not found</span><br>'; break; }
        var icon = getIcon(n.type);
        h += '<div style="padding: 4px 8px; margin: 2px 0; background: #f9fafb; border-radius: 6px; font-size: 0.78rem;">' + icon + ' <strong>' + n.node_key + '</strong> <span class="text-muted">(' + getTypeLabel(n.type) + ')</span></div>';
        if (n.type === 'end' || n.type === 'goto') {
            if (n.type === 'end') h += '<div style="padding-left: 12px; color: #6b7280;">✓ End</div>';
            if (n.type === 'goto') { cur = n.payload.to; if (!cur) break; continue; }
            break;
        }
        if (n.type === 'condition') {
            var t = n.payload.true_to, f = n.payload.false_to;
            h += '<div style="padding-left: 16px; font-size: 0.7rem; color: #059669;">✓ true → ' + (t || '?') + '</div>';
            h += '<div style="padding-left: 16px; font-size: 0.7rem; color: #dc2626;">✗ false → ' + (f || '?') + '</div>';
            cur = t;
            if (!cur) break;
            continue;
        }
        var edge = edges.find(function(e) { return e.from === cur; });
        if (edge) { cur = edge.to; } else { h += '<div style="padding-left: 12px; color: #9ca3af;">⏹ No connection → ends here</div>'; break; }
    }
    div.innerHTML = h;
}

renderVisualCanvas();
</script>

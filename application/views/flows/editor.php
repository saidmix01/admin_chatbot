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
            <input type="text" id="trigger-value" value="<?= !empty($triggers) ? htmlspecialchars($triggers[0]->trigger_value) : '' ?>" placeholder="Palabra clave" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px 12px; font-size: 0.8rem; outline: none; width: 150px;">
            <button onclick="saveTrigger(<?= $flow->id ?>)" class="flow-btn flow-btn-outline" style="font-size: 0.75rem;">Trigger</button>
            <button onclick="saveNodes(<?= $version->id ?>)" class="flow-btn flow-btn-primary" style="font-size: 0.75rem;">💾 Guardar</button>
            <button onclick="validateFlow(<?= $version->id ?>)" class="flow-btn flow-btn-outline" style="font-size: 0.75rem;">✅ Validar</button>
            <button onclick="publishFlow(<?= $version->id ?>)" class="flow-btn" style="font-size: 0.75rem; background: #22C55E; border-color: #22C55E; color: #fff;">🚀 Publicar</button>
            <div class="dropdown" style="position: relative;">
                <button class="flow-btn flow-btn-outline" style="font-size: 0.75rem;" onclick="document.getElementById('flowActionsDropdown').classList.toggle('show')">⋮</button>
                <div id="flowActionsDropdown" class="flow-dropdown-menu" style="position: absolute; right: 0; top: 100%; z-index: 100; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); padding: 6px; min-width: 160px; display: none;">
                    <a href="<?= base_url() ?>FlowBuilder" style="display: block; padding: 8px 12px; font-size: 0.8rem; color: #374151; border-radius: 6px; text-decoration: none;">← Volver a flujos</a>
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
                        <span style="font-size: 0.8rem; font-weight: 600; color: #374151;">Steps</span>
                        <span class="text-muted" style="font-size: 0.7rem;" id="nodeCount">0 pasos</span>
                    </div>
                    <div class="d-flex" style="gap: 6px;">
                        <button class="flow-btn flow-btn-outline" style="font-size: 0.7rem; padding: 3px 10px;" onclick="alert()">👁 Vista Previa</button>
                        <button class="flow-btn flow-btn-primary" style="font-size: 0.7rem; padding: 3px 10px;" onclick="openNewNodeModal()">+ Agregar paso</button>
                    </div>
                </div>
                <div class="flow-canvas-body" id="canvasBody">
                    <div id="flowEmptyState" class="flow-empty-state">
                        <div class="flow-empty-icon">⚡</div>
                        <h5>Comienza a construir tu flujo</h5>
                        <p class="text-muted">Agrega pasos y conectalos para crear tu conversacion.</p>
                        <button class="flow-btn flow-btn-primary" onclick="openNewNodeModal()">+ Agregar primer paso</button>
                    </div>
                    <div id="stepsContainer" class="flow-steps-container"></div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-3" style="padding: 0 0 0 8px;">
            <!-- Node palette -->
            <div class="flow-panel">
                <div class="flow-panel-header">Tipos de nodo</div>
                <div class="flow-panel-body">
                    <div class="flow-palette-item" onclick="quickAddNode('message')">
                        <span class="flow-type-badge" style="background: #e0f2fe; color: #0284c7;">💬</span>
                        <div><strong style="font-size: 0.8rem;">Mensaje</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Enviar un mensaje</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('question')">
                        <span class="flow-type-badge" style="background: #fef3c7; color: #d97706;">❓</span>
                        <div><strong style="font-size: 0.8rem;">Pregunta</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Preguntar y guardar respuesta</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('choice')">
                        <span class="flow-type-badge" style="background: #ede9fe; color: #7c3aed;">📋</span>
                        <div><strong style="font-size: 0.8rem;">Opcion</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Mostrar opciones</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('condition')">
                        <span class="flow-type-badge" style="background: #fce7f3; color: #db2777;">🔀</span>
                        <div><strong style="font-size: 0.8rem;">Condicion</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Condicion (si/entonces)</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('action_webhook')">
                        <span class="flow-type-badge" style="background: #d1fae5; color: #059669;">🔗</span>
                        <div><strong style="font-size: 0.8rem;">Webhook</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Llamar API externa</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('call_flow')">
                        <span class="flow-type-badge" style="background: #fae8ff; color: #a21caf;">🔗</span>
                        <div><strong style="font-size: 0.8rem;">Llamar flujo</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Llamar a otro flujo</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('goto')">
                        <span class="flow-type-badge" style="background: #e0e7ff; color: #4338ca;">➡️</span>
                        <div><strong style="font-size: 0.8rem;">Ir a</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Ir a otro nodo</small></div>
                    </div>
                    <div class="flow-palette-item" onclick="quickAddNode('end')">
                        <span class="flow-type-badge" style="background: #f3f4f6; color: #6b7280;">⏹️</span>
                        <div><strong style="font-size: 0.8rem;">Fin</strong><br><small style="font-size: 0.65rem; color: #9ca3af;">Finalizar el flujo</small></div>
                    </div>
                </div>
            </div>

            <!-- Preview panel -->
            <div class="flow-panel" id="previewPanel" style="display: none;">
                <div class="flow-panel-header">Vista previa</div>
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
                            <option value="call_flow">🔗 Call Flow</option>
                            <option value="goto">➡️ Go To</option>
                            <option value="end">⏹️ End</option>
                        </select>
                    </div>
                </div>

                <!-- Message fields -->
                <div id="modal-fields-message" class="modal-fields">
                    <div class="flow-field">
                        <label class="flow-label">Texto del mensaje</label>
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
                            <label class="flow-label">Guardar en variable</label>
                            <input type="text" id="q-save" class="flow-input" placeholder="name">
                        </div>
                        <div style="flex: 1;">
                            <label class="flow-label">Texto de reintento</label>
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
                        <label class="flow-label">Cargar del catalogo</label>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button type="button" class="flow-btn flow-btn-outline" style="font-size:0.75rem;padding:4px 12px;" onclick="loadCatalog('producto')">📦 Cargar productos</button>
                            <button type="button" class="flow-btn flow-btn-outline" style="font-size:0.75rem;padding:4px 12px;" onclick="loadCatalog('servicio')">🔧 Cargar servicios</button>
                            <span id="catalogSource" style="font-size:0.7rem;color:#6b7280;align-self:center;"></span>
                        </div>
                    </div>
                    <div id="catalogPreview" style="display:none;background:#f9fafb;border-radius:8px;padding:8px;margin-bottom:8px;max-height:150px;overflow-y:auto;font-size:0.8rem;"></div>
                    <div class="flow-field">
                        <label class="flow-label">Options (one per line: value|Label)</label>
                        <textarea id="c-options" class="flow-input" rows="4" placeholder="1|Sales&#10;2|Support&#10;3|Info"></textarea>
                    </div>
                    <div class="flow-field">
                        <label class="flow-label">Guardar en variable</label>
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
                            <label class="flow-label">Guardar en variable</label>
                            <input type="text" id="wh-save" class="flow-input" placeholder="api_result">
                        </div>
                    </div>
                </div>

                <!-- GoTo fields -->
                <div id="modal-fields-goto" class="modal-fields" style="display:none">
                    <div class="flow-field">
                        <label class="flow-label">Ir al nodo</label>
                        <input type="text" id="goto-to" class="flow-input" placeholder="node_key" list="nodes-list">
                    </div>
                </div>

                <!-- Call Flow fields -->
                <div id="modal-fields-call_flow" class="modal-fields" style="display:none">
                    <div class="flow-field">
                        <label class="flow-label">Flujo destino</label>
                        <select id="cf-target" class="flow-input" style="font-size:0.85rem;">
                            <option value="">--- Select flow ---</option>
                        </select>
                        <small style="color: #9ca3af; font-size: 0.7rem;">When this flow ends, execution returns to the parent flow.</small>
                    </div>
                    <div class="flow-field">
                        <label class="flow-label">Nodo de retorno (optional)</label>
                        <input type="text" id="cf-return" class="flow-input" placeholder="node_key to return to in parent" list="parent-nodes-list">
                        <small style="color: #9ca3af; font-size: 0.7rem;">Leave empty to continue from where call was made.</small>
                    </div>
                </div>

                <datalist id="nodes-list"></datalist>
            </div>
            <div class="modal-footer" style="border: none; padding: 0 24px 20px; justify-content: space-between;">
                <button class="flow-btn flow-btn-danger" id="deleteNodeBtn" style="display: none; font-size: 0.75rem;" onclick="deleteCurrentNode()">🗑 Delete</button>
                <div>
                    <button class="flow-btn flow-btn-outline" data-dismiss="modal" style="font-size: 0.8rem; margin-right: 6px;">Cancel</button>
                    <button class="flow-btn flow-btn-primary" onclick="saveNodeModal()" style="font-size: 0.8rem;">💾 Guardar Node</button>
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
.flow-visual-node.node-call_flow { border-left: 4px solid #a21caf; }
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

.toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
.toast-item { padding: 10px 18px; border-radius: 10px; color: #fff; font-size: 0.8rem; font-weight: 500; box-shadow: 0 8px 24px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease; max-width: 340px; display: flex; align-items: center; gap: 8px; pointer-events: auto; }
.toast-item.success { background: #22C55E; }
.toast-item.error { background: #EF4444; }
.toast-item.info { background: #6366F1; }
@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

.flow-steps-container { display: flex; flex-direction: column; gap: 0; padding: 10px 0; }
.flow-step-wrapper { position: relative; padding-left: 40px; }
.flow-step-wrapper::before { content: ""; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #e5e7eb; }
.flow-step-wrapper:first-child::before { top: 24px; }
.flow-step-wrapper:last-child::before { bottom: auto; height: 24px; }
.flow-step-dot { position: absolute; left: 8px; top: 20px; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #d1d5db; background: #fff; z-index: 1; }
.flow-step-dot.start { border-color: #22C55E; background: #22C55E; }
.flow-step-dot.end { border-color: #6b7280; background: #f3f4f6; }
.flow-step-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 4px; cursor: pointer; }
.flow-step-card:hover { border-color: #6366F1; box-shadow: 0 2px 8px rgba(99,102,241,0.1); }
.flow-step-card .step-header { display: flex; align-items: center; gap: 8px; padding: 12px 14px; border-bottom: 1px solid #f9fafb; }
.flow-step-card .step-num { width: 22px; height: 22px; border-radius: 50%; background: #f3f4f6; color: #6b7280; font-weight: 600; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; }
.flow-step-card .step-icon { font-size: 1rem; }
.flow-step-card .step-type { font-size: 0.65rem; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; color: #6b7280; }
.flow-step-card .step-text { flex: 1; font-size: 0.85rem; font-weight: 500; color: #374151; }
.flow-step-card .step-body { padding: 10px 14px; }
.flow-step-card .step-detail { font-size: 0.78rem; color: #6b7280; margin-bottom: 4px; }
.flow-step-card .step-detail strong { color: #374151; margin-right: 6px; }
.flow-step-card .step-actions { display: flex; gap: 4px; padding: 8px 14px; border-top: 1px solid #f3f4f6; }
.flow-step-card .step-actions button { padding: 3px 10px; border: 1px solid #e5e7eb; background: #fff; border-radius: 6px; font-size: 0.7rem; cursor: pointer; color: #6b7280; }
.flow-step-card .step-actions button:hover { border-color: #6366F1; color: #6366F1; }
.flow-step-card.step-message { border-left: 3px solid #0284c7; }
.flow-step-card.step-question { border-left: 3px solid #d97706; }
.flow-step-card.step-choice { border-left: 3px solid #7c3aed; }
.flow-step-card.step-condition { border-left: 3px solid #db2777; }
.flow-step-card.step-call_flow { border-left: 3px solid #a21caf; }
.flow-step-card.step-goto { border-left: 3px solid #4338ca; }
.flow-step-card.step-end { border-left: 3px solid #6b7280; }
.flow-step-card.step-action_webhook { border-left: 3px solid #059669; }
.flow-step-add { display: flex; justify-content: center; padding: 2px 0; }
.flow-step-add button { width: 26px; height: 26px; border-radius: 50%; border: 2px dashed #d1d5db; background: #fff; color: #9ca3af; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; }
.flow-step-add button:hover { border-color: #6366F1; color: #6366F1; background: #eef2ff; }
.flow-step-options { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
.flow-step-option { font-size: 0.7rem; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; color: #6b7280; }

</style>

<div id=	oastContainer class=	oast-container></div>
<script>
const BASE_URL = '<?= base_url() ?>';
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
// --- Core Functions ---

function toggleModalFields() {
    var type = document.getElementById('modal-type').value;
    document.querySelectorAll('.modal-fields').forEach(function(el) { el.style.display = 'none'; });
    var f = document.getElementById('modal-fields-' + type);
    if (f) f.style.display = 'block';
    if (type === 'call_flow') loadFlowsForSelector();
    updateNodeList();
}

function updateNodeList() {
    var list = document.getElementById('nodes-list');
    if (list) list.innerHTML = nodes.map(function(n) { return '<option value="' + n.node_key + '">'; }).join('');
}

function getIcon(type) {
    var icons = { 'start': '⚡', 'message': '💬', 'question': '❓', 'choice': '📋', 'condition': '🔀', 'action_webhook': '🔗', 'call_flow': '🔗', 'goto': '➡️', 'end': '⏹️' };
    return icons[type] || '⚡';
}

function getTypeLabel(type) {
    var labels = { 'start': 'Start', 'message': 'Message', 'question': 'Question', 'choice': 'Choice', 'condition': 'Condition', 'action_webhook': 'Webhook', 'call_flow': 'Call Flow', 'goto': 'Go To', 'end': 'End' };
    return labels[type] || type;
}

function getNodeSummary(n) {
    var p = n.payload || {};
    switch (n.type) {
        case 'message': return (p.text||'').substring(0, 60);
        case 'question': return 'Save: ' + (p.save_to||'?') + ' - "' + (p.text||'').substring(0, 40) + '"';
        case 'choice': return 'Save: ' + (p.save_to||'var') + ' - ' + (p.options||[]).length + ' options';
        case 'condition': return 'if ' + (p.if ? p.if.var + ' ' + p.if.op + ' ' + p.if.value : '?');
        case 'action_webhook': return (p.url||'').substring(0, 40);
        case 'call_flow': return '-> ' + (p.target_flow_name || p.target_flow_id || '?');
        case 'goto': return '-> ' + (p.to||'?');
        case 'end': return 'End flow';
        default: return '';
    }
}

// --- Step View ---

function renderSteps() {
    var el = document.getElementById('stepsContainer');
    var cnt = document.getElementById('nodeCount');
    if (!nodes.length) { el.innerHTML = '<div style=text-align:center;padding:40px;color:#9ca3af;font-size:0.85rem;>No steps yet. Click + Add Node to begin.</div>'; cnt.textContent = '0 nodes'; return; }
    cnt.textContent = nodes.length + ' steps';
    var h = '';
    nodes.forEach(function(n, i) {
        var p = n.payload || {};
        var t = p.text || '(no text)';
        h += '<div class=flow-step-wrapper>';
        h += '<div class=flow-step-dot' + (n.node_key==='start'?' start':'') + (n.type==='end'?' end':'') + '></div>';
        h += '<div class="flow-step-card step-' + n.type + '" onclick=editNode(' + i + ')>';
        h += '<div class=step-header><span class=step-num>' + (i+1) + '</span>';
        h += '<span class=step-icon>' + getIcon(n.type) + '</span>';
        h += '<span class=step-type>' + getTypeLabel(n.type) + '</span>';
        h += '<span class=step-text>' + escapeHtml(t) + '</span></div>';
        h += '<div class=step-body>';
        if (n.node_key==='start') h += '<div class=step-detail style=color:#22C55E;>Flow starts here</div>';
        if (n.type==='end') h += '<div class=step-detail style=color:#6b7280;>Flow ends</div>';
        if ((n.type==='question'||n.type==='choice')&&p.save_to) h += '<div class=step-detail><strong>Save:</strong> ' + p.save_to + '</div>';
        if (n.type==='question'&&p.retry_text) h += '<div class=step-detail><strong>Retry:</strong> ' + p.retry_text + '</div>';
        if (n.type==='choice'&&p.options&&p.options.length) {
            h += '<div class=step-detail><strong>Options:</strong></div><div class=flow-step-options>';
            p.options.forEach(function(o) { h += '<span class=flow-step-option>' + o.value + '. ' + escapeHtml(o.label) + '</span>'; });
            h += '</div>';
        }
        if (n.type==='condition'&&p.if) h += '<div class=step-detail><strong>If:</strong> ' + p.if.var + ' ' + p.if.op + ' ' + p.if.value + '</div>';
        if (n.type==='action_webhook'&&p.url) h += '<div class=step-detail><strong>URL:</strong> ' + p.url.substring(0,50) + '</div>';
        if (n.type==='goto'&&p.to) h += '<div class=step-detail><strong>To:</strong> ' + p.to + '</div>';
        if (n.type==='call_flow'&&p.target_flow_id) h += '<div class=step-detail><strong>Subflow:</strong> ' + (p.target_flow_name||'ID '+p.target_flow_id) + '</div>';
        h += '</div>';
        h += '<div class=step-actions onclick=event.stopPropagation();>';
        h += '<button onclick=editNode(' + i + ')>Edit</button>';
        h += '<button onclick=nodes.splice(' + i + ',1);renderSteps();showToast("Deleted","success");>Delete</button>';
        h += '</div></div></div>';
        h += '<div class=flow-step-add><button onclick=openNewNodeModal() title="Add step">+</button></div>';
    });
    el.innerHTML = h;
}

// --- Node CRUD ---

function openNewNodeModal() {
    document.getElementById('edit-idx').value = '-1';
    document.getElementById('modalTitleText').textContent = 'New Step';
    document.getElementById('modal-key').value = '';
    document.getElementById('deleteNodeBtn').style.display = 'none';
    toggleModalFields();
    $('#nodeModal').modal('show');
}

function editNode(idx) {
    var n = nodes[idx];
    document.getElementById('edit-idx').value = idx;
    document.getElementById('modalTitleText').textContent = 'Edit: ' + n.node_key;
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
    document.getElementById('cf-target').value = p.target_flow_id || '';
    document.getElementById('cf-return').value = p.return_node || '';
    toggleModalFields();
    $('#nodeModal').modal('show');
}

function deleteCurrentNode() {
    var idx = parseInt(document.getElementById('edit-idx').value);
    if (isNaN(idx) || idx < 0) return;
    nodes.splice(idx, 1);
    $('#nodeModal').modal('hide');
    renderSteps();
    showToast('Deleted', 'success');
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
            var catalogSrc = document.getElementById('catalogSource').dataset.type || '';
            payload = { text: document.getElementById('c-text').value, options: opts, save_to: document.getElementById('c-save').value, catalog_source: catalogSrc };
            break;
        case 'condition': payload = { if: { var: document.getElementById('cond-var').value, op: document.getElementById('cond-op').value, value: document.getElementById('cond-val').value }, true_to: document.getElementById('cond-true').value, false_to: document.getElementById('cond-false').value }; break;
        case 'action_webhook': payload = { url: document.getElementById('wh-url').value, method: document.getElementById('wh-method').value, save_to: document.getElementById('wh-save').value }; break;
        case 'call_flow':
            var cfTarget = document.getElementById('cf-target');
            payload = { target_flow_id: cfTarget.value, target_flow_name: cfTarget.options[cfTarget.selectedIndex] ? cfTarget.options[cfTarget.selectedIndex].text : '', return_node: document.getElementById('cf-return').value || '' };
            break;
        case 'goto': payload = { to: document.getElementById('goto-to').value }; break;
        default: payload = {};
    }
    if (isNaN(idx) || idx === -1) {
        nodes.push({ node_key: key, type: type, payload: payload });
    } else {
        var oldKey = nodes[idx].node_key;
        if (oldKey !== key) {
            // Update edges referencing this node (but we auto-build edges, so this is optional)
        }
        nodes[idx].node_key = key;
        nodes[idx].type = type;
        nodes[idx].payload = payload;
    }
    $('#nodeModal').modal('hide');
    renderSteps();
}

function quickAddNode(type) {
    var base = { 'message': 'msg', 'question': 'ask', 'choice': 'menu', 'condition': 'if', 'action_webhook': 'api', 'call_flow': 'sub', 'goto': 'go', 'end': 'end' };
    var key = base[type] || 'node';
    var i = 1;
    while (nodes.some(function(n) { return n.node_key === key + i; })) i++;
    nodes.push({ node_key: key + i, type: type, payload: {} });
    renderSteps();
}

function loadFlowsForSelector() {
    var sel = document.getElementById('cf-target');
    if (!sel || sel.options.length > 1) return;
    fetch(BASE_URL + 'FlowBuilder/api_flow_list')
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.status || !res.data) return;
            var flowId = window.location.pathname.split('/').pop();
            res.data.forEach(function(f) {
                if (f.id != flowId) {
                    var opt = document.createElement('option');
                    opt.value = f.id;
                    opt.textContent = (f.parent_flow_id ? '  ' : '') + f.name;
                    sel.appendChild(opt);
                }
            });
        }).catch(function() {});
}

// --- Helpers ---

function showToast(msg, type) {
    type = type || 'info';
    var c = document.getElementById('toastContainer');
    if (!c) return;
    var t = document.createElement('div');
    t.className = 'toast-item ' + type;
    t.innerHTML = msg;
    c.appendChild(t);
    setTimeout(function() {
        t.style.opacity = '0';
        t.style.transition = 'opacity 0.3s';
        setTimeout(function() { t.remove(); }, 300);
    }, 3000);
}

function loadCatalog(type) {
    var btn = document.getElementById('c-options');
    if (!btn) { showToast('Catalog not available', 'error'); return; }
    fetch(BASE_URL + 'FlowBuilder/api_catalog')
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (!res.status || !res.data) { showToast('Error loading catalog', 'error'); return; }
            var items = type === 'producto' ? res.data.products : res.data.services;
            if (!items.length) { showToast('No ' + type + ' found', 'info'); return; }
            var preview = document.getElementById('catalogPreview');
            if (!preview) return;
            var html = '<div style="font-weight:600;margin-bottom:4px;">' + (type === 'producto' ? 'Products' : 'Services') + ' (' + items.length + ')</div>';
            var opts = '';
            items.forEach(function(item, idx) {
                var priceNum = parseFloat(item.ser_price || 0);
                var priceStr = '$' + priceNum.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                html += '<div style="padding:2px 4px;border-bottom:1px solid #e5e7eb;">' + (idx+1) + '. ' + escapeHtml(item.ser_name) + ' - ' + priceStr + '</div>';
                opts += (idx+1) + '|' + item.ser_name + '\\n';
            });
            preview.innerHTML = html;
            preview.style.display = '';
            btn.value = opts;
            var src = document.getElementById('catalogSource');
            if (src) { src.textContent = 'Loaded from: ' + (type === 'producto' ? 'Products' : 'Services'); src.dataset.type = type; }
            showToast('Loaded ' + items.length + ' ' + type, 'success');
        }).catch(function(err) { showToast('Error: ' + err.message, 'error'); });
}

function escapeHtml(t) { if (!t) return ''; var d = document.createElement('div'); d.appendChild(document.createTextNode(t)); return d.innerHTML; }

// --- Actions ---

function saveTrigger(fid) {
    var val = document.getElementById('trigger-value').value.trim();
    fetch(BASE_URL + 'FlowBuilder/save_trigger/' + fid, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: 'trigger_value=' + encodeURIComponent(val)
    }).then(function(r) { return r.json(); }).then(function(d) { showToast(d.message, d.status ? 'success' : 'error'); }).catch(function(e) { showToast('Error: ' + e.message, 'error'); });
}

function saveNodes(vid) {
    var ae = [];
    for (var i = 0; i < nodes.length - 1; i++) {
        var f = nodes[i];
        var t = nodes[i+1];
        if (f.type === 'end') break;
        ae.push({from: f.node_key, to: t.node_key, rule: null});
    }
    fetch(BASE_URL + 'FlowBuilder/save_nodes/' + vid, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
        body: JSON.stringify({ nodes: nodes, edges: ae })
    }).then(function(r) { return r.json(); }).then(function(d) {
        showToast(d.message, d.status ? 'success' : 'error');
        if (d.status) setTimeout(function() { location.reload(); }, 1000);
    }).catch(function(e) { showToast('Error: ' + e.message, 'error'); });
}

function validateFlow(vid) {
    fetch(BASE_URL + 'FlowBuilder/validate_version/' + vid, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(function(r) { return r.json(); }).then(function(d) {
        showToast(d.status ? 'Valid flow!' : 'Errors: ' + (d.errors || []).join(', '), d.status ? 'success' : 'error');
    }).catch(function(e) { showToast('Error: ' + e.message, 'error'); });
}

function publishFlow(vid) {
    showToast('Publishing...', 'info');
    fetch(BASE_URL + 'FlowBuilder/publish/' + vid, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(function(r) { return r.json(); }).then(function(d) {
        showToast(d.message, d.status ? 'success' : 'error');
        if (d.status) { setTimeout(function() { location.reload(); }, 1500); }
    }).catch(function(e) { showToast('Error: ' + e.message, 'error'); });
}

</script>

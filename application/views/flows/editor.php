<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Editor: <?= htmlspecialchars($flow->name) ?></h4>
        <p>v<?= $version->version ?> (<?= $version->status ?>)</p>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="nodeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo nodo</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-idx" value="-1">
                    <div class="form-saas-group">
                        <label class="form-saas-label">Node key</label>
                        <input type="text" id="modal-key" class="form-saas" placeholder="start, ask_name, etc">
                    </div>
                    <div class="form-saas-group">
                        <label class="form-saas-label">Tipo</label>
                        <select id="modal-type" class="form-saas" onchange="toggleModalFields()">
                            <option value="message">Mensaje (message)</option>
                            <option value="question">Pregunta (question)</option>
                            <option value="choice">Opciones (choice)</option>
                            <option value="condition">Condicion (condition)</option>
                            <option value="action_webhook">Webhook (action_webhook)</option>
                            <option value="goto">Ir a (goto)</option>
                            <option value="end">Fin (end)</option>
                        </select>
                    </div>

                    <div id="modal-fields-message" class="modal-fields">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Texto del mensaje</label>
                            <textarea id="msg-text" class="form-saas" rows="3" placeholder="Hola {{name}}, bienvenido..."></textarea>
                        </div>
                    </div>

                    <div id="modal-fields-question" class="modal-fields" style="display:none">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Pregunta</label>
                            <textarea id="q-text" class="form-saas" rows="2" placeholder="¿Cómo te llamas?"></textarea>
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Guardar en variable</label>
                            <input type="text" id="q-save" class="form-saas" placeholder="name">
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Texto de reintento</label>
                            <input type="text" id="q-retry" class="form-saas" placeholder="Respuesta invalida, intenta de nuevo">
                        </div>
                    </div>

                    <div id="modal-fields-choice" class="modal-fields" style="display:none">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Texto</label>
                            <textarea id="c-text" class="form-saas" rows="2" placeholder="Elige una opcion:"></textarea>
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Opciones (una por linea, formato: valor|Etiqueta)</label>
                            <textarea id="c-options" class="form-saas" rows="4" placeholder="1|Ventas&#10;2|Soporte&#10;3|Info"></textarea>
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Guardar en variable</label>
                            <input type="text" id="c-save" class="form-saas" placeholder="seleccion">
                        </div>
                    </div>

                    <div id="modal-fields-condition" class="modal-fields" style="display:none">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-saas-group">
                                    <label class="form-saas-label">Variable</label>
                                    <input type="text" id="cond-var" class="form-saas" placeholder="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-saas-group">
                                    <label class="form-saas-label">Operador</label>
                                    <select id="cond-op" class="form-saas">
                                        <option value="equals">=</option>
                                        <option value="not_equals">!=</option>
                                        <option value="gt">></option>
                                        <option value="lt"><</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-saas-group">
                                    <label class="form-saas-label">Valor</label>
                                    <input type="text" id="cond-val" class="form-saas" placeholder="si">
                                </div>
                            </div>
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Ir a (true)</label>
                            <input type="text" id="cond-true" class="form-saas" placeholder="nombre_del_nodo">
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Ir a (false)</label>
                            <input type="text" id="cond-false" class="form-saas" placeholder="nombre_del_nodo">
                        </div>
                    </div>

                    <div id="modal-fields-webhook" class="modal-fields" style="display:none">
                        <div class="form-saas-group">
                            <label class="form-saas-label">URL del webhook</label>
                            <input type="text" id="wh-url" class="form-saas" placeholder="https://ejemplo.com/api">
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Metodo</label>
                            <select id="wh-method" class="form-saas">
                                <option value="POST">POST</option>
                                <option value="GET">GET</option>
                            </select>
                        </div>
                        <div class="form-saas-group">
                            <label class="form-saas-label">Guardar en variable</label>
                            <input type="text" id="wh-save" class="form-saas" placeholder="resultado">
                        </div>
                    </div>

                    <div id="modal-fields-goto" class="modal-fields" style="display:none">
                        <div class="form-saas-group">
                            <label class="form-saas-label">Ir al nodo</label>
                            <input type="text" id="goto-to" class="form-saas" placeholder="nombre_del_nodo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-saas btn-saas-outline" data-dismiss="modal">Cancelar</button>
                    <button class="btn-saas btn-saas-primary" onclick="saveNodeModal()">Guardar nodo</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header with-elements">
                    <h6>Nodos</h6>
                    <div class="card-header-elements ml-auto">
                        <button class="btn btn-sm btn-outline-primary" onclick="openNewNodeModal()">+ Nodo</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="nodes-table">
                            <thead><tr><th>Key</th><th>Tipo</th><th>Contenido</th><th></th></tr></thead>
                            <tbody id="nodes-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h6>Conexiones</h6></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Desde</th><th>Hacia</th><th>Regla</th><th></th></tr></thead>
                        <tbody id="edges-body"></tbody>
                    </table>
                    <button class="btn btn-sm btn-outline-primary" onclick="addEdge()">+ Conexion</button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h6>Vista previa</h6></div>
                <div class="card-body">
                    <div id="flow-preview" style="font-family:monospace;font-size:13px;background:#f8f9fa;padding:15px;border-radius:8px;min-height:100px"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><h6>Trigger</h6></div>
                <div class="card-body">
                    <input type="text" id="trigger-value" class="form-saas" placeholder="Ej: menu" value="<?= !empty($triggers) ? $triggers[0]->trigger_value : '' ?>">
                    <button class="btn-saas btn-saas-outline mt-2" onclick="saveTrigger(<?= $flow->id ?>)">Guardar</button>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h6>Acciones</h6></div>
                <div class="card-body">
                    <button class="btn-saas btn-saas-primary w-100 mb-2" onclick="saveNodes(<?= $version->id ?>)">💾 Guardar</button>
                    <button class="btn-saas btn-saas-outline w-100 mb-2" onclick="validateFlow(<?= $version->id ?>)">✅ Validar</button>
                    <button class="btn-saas btn-saas-primary w-100" style="background:#22C55E;border-color:#22C55E" onclick="publishFlow(<?= $version->id ?>)">🚀 Publicar</button>
                    <?php if ($published): ?>
                    <div class="alert alert-success mt-2" style="font-size:12px">v<?= $published->version ?> publicada <?= substr($published->published_at ?? '', 0, 10) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edge modal -->
<div class="modal fade" id="edgeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Nueva conexion</h5></div>
            <div class="modal-body">
                <input type="hidden" id="edge-edit-idx" value="-1">
                <div class="form-saas-group">
                    <label class="form-saas-label">Desde (node key)</label>
                    <input type="text" id="edge-from" class="form-saas" placeholder="start" list="nodes-list">
                </div>
                <div class="form-saas-group">
                    <label class="form-saas-label">Hacia (node key)</label>
                    <input type="text" id="edge-to" class="form-saas" placeholder="ask_name" list="nodes-list">
                </div>
                <datalist id="nodes-list"></datalist>
                <div class="form-saas-group">
                    <label class="form-saas-label">Regla (JSON, opcional)</label>
                    <input type="text" id="edge-rule" class="form-saas" placeholder='{"when":"choice","equals":2}'>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-saas btn-saas-outline" data-dismiss="modal">Cancelar</button>
                <button class="btn-saas btn-saas-primary" onclick="saveEdgeModal()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
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
}

function openNewNodeModal() {
    document.getElementById('edit-idx').value = '-1';
    document.getElementById('modalTitle').textContent = 'Nuevo nodo';
    document.getElementById('modal-key').value = '';
    toggleModalFields();
    $('#nodeModal').modal('show');
}

function editNode(idx) {
    var n = nodes[idx];
    document.getElementById('edit-idx').value = idx;
    document.getElementById('modalTitle').textContent = 'Editar: ' + n.node_key;
    document.getElementById('modal-key').value = n.node_key;
    document.getElementById('modal-type').value = n.type;
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

function saveNodeModal() {
    var idx = parseInt(document.getElementById('edit-idx').value);
    var key = document.getElementById('modal-key').value.trim();
    if (!key) { alert('Node key requerido'); return; }
    var type = document.getElementById('modal-type').value;
    var payload = {};
    switch (type) {
        case 'message': payload = { text: document.getElementById('msg-text').value }; break;
        case 'question': payload = { text: document.getElementById('q-text').value, save_to: document.getElementById('q-save').value, retry_text: document.getElementById('q-retry').value }; break;
        case 'choice':
            var lines = document.getElementById('c-options').value.split('\n').filter(Boolean);
            var opts = lines.map(function(l) { var p = l.split('|'); return { value: parseInt(p[0]) || (opts.length+1), label: p[1] || p[0] }; });
            payload = { text: document.getElementById('c-text').value, options: opts, save_to: document.getElementById('c-save').value }; break;
        case 'condition': payload = { if: { var: document.getElementById('cond-var').value, op: document.getElementById('cond-op').value, value: document.getElementById('cond-val').value }, true_to: document.getElementById('cond-true').value, false_to: document.getElementById('cond-false').value }; break;
        case 'action_webhook': payload = { url: document.getElementById('wh-url').value, method: document.getElementById('wh-method').value, save_to: document.getElementById('wh-save').value }; break;
        case 'goto': payload = { to: document.getElementById('goto-to').value }; break;
        default: payload = {};
    }
    if (idx === -1) {
        nodes.push({ node_key: key, type: type, payload: payload });
    } else {
        nodes[idx].node_key = key;
        nodes[idx].type = type;
        nodes[idx].payload = payload;
    }
    $('#nodeModal').modal('hide');
    renderAll();
}

function addEdge() {
    var list = document.getElementById('nodes-list');
    list.innerHTML = nodes.map(function(n) { return '<option value="' + n.node_key + '">'; }).join('');
    document.getElementById('edge-edit-idx').value = '-1';
    document.getElementById('edge-from').value = '';
    document.getElementById('edge-to').value = '';
    document.getElementById('edge-rule').value = '';
    $('#edgeModal').modal('show');
}

function saveEdgeModal() {
    var idx = parseInt(document.getElementById('edge-edit-idx').value);
    var from = document.getElementById('edge-from').value.trim();
    var to = document.getElementById('edge-to').value.trim();
    var rule = document.getElementById('edge-rule').value.trim();
    if (!from || !to) { alert('Desde y Hacia requeridos'); return; }
    var ruleObj = rule ? (function(){ try { return JSON.parse(rule); } catch(e) { return rule; } })() : null;
    if (idx === -1) {
        edges.push({ from: from, to: to, rule: ruleObj });
    } else {
        edges[idx].from = from; edges[idx].to = to; edges[idx].rule = ruleObj;
    }
    $('#edgeModal').modal('hide');
    renderAll();
}

function renderAll() {
    renderNodes(); renderEdges(); renderPreview();
}

function renderNodes() {
    var tbody = document.getElementById('nodes-body');
    tbody.innerHTML = '';
    nodes.forEach(function(n, i) {
        var s = '';
        switch (n.type) {
            case 'message': s = (n.payload.text||'').substring(0, 50); break;
            case 'question': s = '? ' + (n.payload.save_to||'') + ' = ' + (n.payload.text||'').substring(0, 30); break;
            case 'choice': s = (n.payload.options||[]).length + ' opc > ' + (n.payload.save_to||''); break;
            case 'condition': s = 'if ' + (n.payload.if ? n.payload.if.var + ' ' + n.payload.if.op + ' ' + n.payload.if.value : ''); break;
            case 'action_webhook': s = 'WEB ' + ((n.payload.url||'').substring(0, 30)||''); break;
            case 'goto': s = '-> ' + (n.payload.to||''); break;
            default: s = '-';
        }
        var badge = n.node_key === 'start' ? 'badge-success' : (n.type === 'end' ? 'badge-secondary' : 'badge-info');
        tr = document.createElement('tr');
        tr.innerHTML = '<td><strong>' + n.node_key + '</strong></td><td><span class="badge ' + badge + '">' + n.type + '</span></td><td><small>' + s + '</small></td><td><button class="btn btn-sm btn-outline-info" onclick="editNode(' + i + ')">Editar</button> <button class="btn btn-sm btn-outline-danger" onclick="nodes.splice(' + i + ',1);renderAll()">X</button></td>';
        tbody.appendChild(tr);
    });
}

function renderEdges() {
    var tbody = document.getElementById('edges-body');
    tbody.innerHTML = '';
    edges.forEach(function(e, i) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>' + e.from + ' <i class="feather icon-arrow-right"></i> ' + e.to + '</td><td><small>' + (e.rule ? JSON.stringify(e.rule) : '-') + '</small></td><td><button class="btn btn-sm btn-outline-danger" onclick="edges.splice(' + i + ',1);renderEdges()">X</button></td>';
        tbody.appendChild(tr);
    });
}

function renderPreview() {
    var div = document.getElementById('flow-preview');
    if (!nodes.length) { div.innerHTML = '<span class="text-muted">Agrega nodos...</span>'; return; }
    var h = '';
    var s = nodes.find(function(n) { return n.node_key === 'start'; });
    if (!s) h += '<span class="text-danger">Falta nodo "start"</span><br>';
    var cur = s ? 'start' : (nodes[0] ? nodes[0].node_key : '');
    for (var i = 0; i < 20; i++) {
        var n = nodes.find(function(nn) { return nn.node_key === cur; });
        if (!n) break;
        h += '<span class="badge ' + (n.type==='end'?'badge-secondary':'badge-success') + ' mr-1">' + n.node_key + '</span> <small>(' + n.type + ')</small><br>';
        if (n.type === 'end') { h += '<span class="text-success mt-1">Fin</span>'; break; }
        var e = edges.find(function(ee) { return ee.from === cur; });
        cur = e ? e.to : null;
        if (!cur) break;
    }
    div.innerHTML = h;
}

function saveTrigger(fid) {
    fetch('<?= base_url() ?>FlowBuilder/save_trigger/' + fid, {
        method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'trigger_value=' + encodeURIComponent(document.getElementById('trigger-value').value)
    }).then(function(r) { return r.json(); }).then(function(d) { alert(d.message); });
}

function saveNodes(vid) {
    fetch('<?= base_url() ?>FlowBuilder/save_nodes/' + vid, {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ nodes: nodes, edges: edges })
    }).then(function(r) { return r.json(); }).then(function(d) { alert(d.message); });
}

function validateFlow(vid) {
    fetch('<?= base_url() ?>FlowBuilder/validate_version/' + vid)
    .then(function(r) { return r.json(); }).then(function(d) {
        alert(d.status ? 'OK: ' + d.message : 'ERROR:\n' + d.errors.join('\n'));
    });
}

function publishFlow(vid) {
    if (!confirm('Publicar? Sesiones activas seguiran con la version anterior.')) return;
    fetch('<?= base_url() ?>FlowBuilder/publish/' + vid)
    .then(function(r) { return r.json(); }).then(function(d) { alert(d.message); if (d.status) location.reload(); });
}

renderAll();
</script>

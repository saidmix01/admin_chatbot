<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Editor: <?= htmlspecialchars($flow->name) ?></h4>
        <p>v<?= $version->version ?> (<?= $version->status ?>)</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header with-elements">
                    <h6>Nodos</h6>
                    <div class="card-header-elements ml-auto">
                        <select id="new-node-type" class="form-control" style="width:auto;display:inline-block">
                            <option value="message">Mensaje</option>
                            <option value="question">Pregunta</option>
                            <option value="choice">Opciones</option>
                            <option value="condition">Condición</option>
                            <option value="action_webhook">Webhook</option>
                            <option value="goto">Ir a</option>
                            <option value="end">Fin</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="addNode()">+ Nodo</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="nodes-table">
                            <thead><tr><th>Key</th><th>Tipo</th><th>Contenido</th><th></th></tr></thead>
                            <tbody id="nodes-body"></tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-2" style="font-size:12px">
                        El nodo "start" es el punto de entrada. Usa "end" para finalizar. Conecta los nodos en la sección "Conexiones".
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h6>Conexiones (edges)</h6></div>
                <div class="card-body">
                    <table class="table table-sm" id="edges-table">
                        <thead><tr><th>Desde</th><th>Hacia</th><th>Regla</th><th></th></tr></thead>
                        <tbody id="edges-body"></tbody>
                    </table>
                    <button class="btn btn-sm btn-outline-primary" onclick="addEdge()">+ Conexión</button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h6>Vista previa</h6></div>
                <div class="card-body">
                    <div id="flow-preview" style="font-family:monospace;font-size:13px;background:#f8f9fa;padding:15px;border-radius:8px;min-height:100px">
                        Carga los nodos para ver el flujo...
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><h6>Trigger</h6></div>
                <div class="card-body">
                    <input type="text" id="trigger-value" class="form-saas" placeholder="Ej: menu" value="<?= !empty($triggers) ? $triggers[0]->trigger_value : '' ?>">
                    <button class="btn-saas btn-saas-outline mt-2" onclick="saveTrigger(<?= $flow->id ?>)">Guardar trigger</button>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h6>Acciones</h6></div>
                <div class="card-body">
                    <button class="btn-saas btn-saas-primary w-100 mb-2" onclick="saveNodes(<?= $version->id ?>)">💾 Guardar borrador</button>
                    <button class="btn-saas btn-saas-outline w-100 mb-2" onclick="validateFlow(<?= $version->id ?>)">✅ Validar flujo</button>
                    <button class="btn-saas btn-saas-primary w-100" style="background:#22C55E;border-color:#22C55E" onclick="publishFlow(<?= $version->id ?>)">🚀 Publicar</button>
                    <?php if ($published): ?>
                    <div class="alert alert-success mt-2" style="font-size:12px">v<?= $published->version ?> publicada el <?= substr($published->published_at ?? '', 0, 10) ?></div>
                    <?php endif; ?>
                </div>
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

function renderNodes() {
    var tbody = document.getElementById('nodes-body');
    tbody.innerHTML = '';
    nodes.forEach(function(n, i) {
        var tr = document.createElement('tr');
        var payloadStr = '';
        if (n.type === 'message') payloadStr = (n.payload.text || '').substring(0, 50);
        else if (n.type === 'question') payloadStr = 'Save: ' + (n.payload.save_to || '') + ' | ' + (n.payload.text || '').substring(0, 30);
        else if (n.type === 'choice') payloadStr = (n.payload.options || []).length + ' opciones | Save: ' + (n.payload.save_to || '');
        else if (n.type === 'condition') payloadStr = n.payload.var + ' ' + n.payload.op + ' ' + n.payload.value;
        else if (n.type === 'action_webhook') payloadStr = (n.payload.url || '').substring(0, 40);
        else if (n.type === 'goto') payloadStr = '→ ' + (n.payload.to || '');
        else payloadStr = '-';
        tr.innerHTML = '<td><input class="form-control form-control-sm" value="' + n.node_key + '" style="width:120px" onchange="nodes[' + i + '].node_key=this.value;renderPreview()"></td>' +
            '<td><span class="badge badge-info">' + n.type + '</span></td>' +
            '<td><small>' + payloadStr + '</small></td>' +
            '<td><button class="btn btn-sm btn-outline-info" onclick="editNode(' + i + ')">✏️</button> <button class="btn btn-sm btn-outline-danger" onclick="nodes.splice(' + i + ',1);renderNodes();renderEdges();renderPreview()">✕</button></td>';
        tbody.appendChild(tr);
    });
    renderPreview();
}

function renderEdges() {
    var tbody = document.getElementById('edges-body');
    tbody.innerHTML = '';
    edges.forEach(function(e, i) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>' + e.from + '</td><td>' + e.to + '</td><td><small>' + (e.rule ? JSON.stringify(e.rule) : '-') + '</small></td>' +
            '<td><button class="btn btn-sm btn-outline-danger" onclick="edges.splice(' + i + ',1);renderEdges()">✕</button></td>';
        tbody.appendChild(tr);
    });
}

function addNode() {
    var type = document.getElementById('new-node-type').value;
    var key = prompt('Nombre del nodo (node_key):', type + '_' + nodes.length);
    if (!key) return;
    var payload = {};
    if (type === 'message') payload = { text: 'Escribe tu mensaje aquí' };
    else if (type === 'question') payload = { text: '¿Tu pregunta?', save_to: 'respuesta', validation: { min_len: 1, max_len: 200 }, retry_text: 'Respuesta inválida' };
    else if (type === 'choice') payload = { text: 'Elige una opción:', options: [{ value: 1, label: 'Opción 1' }], save_to: 'seleccion', retry_text: 'Opción inválida' };
    else if (type === 'condition') payload = { if: { var: 'variable', op: 'equals', value: 'si' }, true_to: 'rama_si', false_to: 'rama_no' };
    else if (type === 'action_webhook') payload = { url: 'https://ejemplo.com/api', method: 'POST', body: {}, save_to: 'resultado' };
    else if (type === 'goto') payload = { to: 'otro_nodo' };
    else if (type === 'end') payload = {};
    nodes.push({ node_key: key, type: type, payload: payload });
    renderNodes();
    renderEdges();
    renderPreview();
}

function addEdge() {
    edges.push({ from: '', to: '', rule: null });
    renderEdges();
}

function editNode(idx) {
    var n = nodes[idx];
    var json = prompt('Editar payload JSON del nodo "' + n.node_key + '":', JSON.stringify(n.payload, null, 2));
    if (!json) return;
    try { n.payload = JSON.parse(json); renderNodes(); } catch(e) { alert('JSON inválido'); }
}

function renderPreview() {
    var div = document.getElementById('flow-preview');
    if (nodes.length === 0) { div.textContent = 'Agrega nodos para ver el flujo...'; return; }
    var html = '<strong>Flujo:</strong><br>';
    var start = nodes.find(n => n.node_key === 'start');
    if (!start) { html += '⚠️ No hay nodo "start"<br>'; }
    var current = start ? 'start' : (nodes[0] ? nodes[0].node_key : '');
    for (var i = 0; i < 20; i++) {
        var node = nodes.find(n => n.node_key === current);
        if (!node) break;
        html += '→ <strong>' + node.node_key + '</strong> (' + node.type + ')<br>';
        if (node.type === 'end') { html += '✅ Fin del flujo'; break; }
        var edge = edges.find(e => e.from === current);
        current = edge ? edge.to : null;
        if (!current) break;
    }
    div.innerHTML = html;
}

function saveTrigger(flowId) {
    var val = document.getElementById('trigger-value').value;
    fetch('<?= base_url() ?>FlowBuilder/save_trigger/' + flowId, {
        method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'trigger_value=' + encodeURIComponent(val)
    }).then(function(r) { return r.json(); }).then(function(d) { alert(d.message); });
}

function saveNodes(versionId) {
    var data = { nodes: nodes, edges: edges };
    fetch('<?= base_url() ?>FlowBuilder/save_nodes/' + versionId, {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r) { return r.json(); }).then(function(d) { alert(d.message); });
}

function validateFlow(versionId) {
    fetch('<?= base_url() ?>FlowBuilder/validate_version/' + versionId)
    .then(function(r) { return r.json(); }).then(function(d) {
        if (d.status) { alert('✅ ' + d.message); }
        else { alert('❌ Errores:\n' + d.errors.join('\n')); }
    });
}

function publishFlow(versionId) {
    if (!confirm('¿Publicar esta versión? Las sesiones activas seguirán con la versión anterior.')) return;
    fetch('<?= base_url() ?>FlowBuilder/publish/' + versionId)
    .then(function(r) { return r.json(); }).then(function(d) { alert(d.message); if(d.status) location.reload(); });
}

renderNodes();
renderEdges();
</script>

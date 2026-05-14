<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4>Flujos conversacionales</h4>
            <p>Crea los menus y opciones que vera el cliente despues del saludo de bienvenida</p>
        </div>
        <a href="<?= base_url() ?>FlowBuilder/create" class="btn-saas btn-saas-primary">+ Nuevo flujo</a>
    </div>

    <!-- Flujos padre (primer nivel) -->
    <h5 class="mb-3">Flujos principales</h5>
    <div class="row">
        <?php 
        $parent_flows = array_filter($flows, fn($f) => empty($f->parent_flow_id));
        foreach ($parent_flows as $f):
            $tr = $this->db->where('flow_id', $f->id)->get('flow_triggers')->result();
            $pub = $this->db->where('flow_id', $f->id)->where('status', 'published')->order_by('version', 'DESC')->get('flow_versions')->row();
            $childs = $this->db->where('parent_flow_id', $f->id)->get('flows')->result();
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card flow-card h-100" style="border-left: 4px solid <?= $f->is_active ? '#6366F1' : '#ccc' ?>;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0 font-weight-bold"><?= htmlspecialchars($f->name) ?></h6>
                        <span class="badge badge-<?= $f->is_active ? 'success' : 'secondary' ?>"><?= $f->is_active ? 'Activo' : 'Inactivo' ?></span>
                    </div>
                    <?php if ($f->description): ?><p class="text-muted small mb-2"><?= htmlspecialchars($f->description) ?></p><?php endif; ?>
                    <div class="small text-muted mb-2">
                        Trigger: <strong><?= !empty($tr) ? $tr[0]->trigger_value : '-' ?></strong>
                        &middot; Publicado: <strong><?= $pub ? 'v'.$pub->version : '-' ?></strong>
                        &middot; Orden: <strong><?= $f->display_order ?></strong>
                    </div>
                    <?php if ($childs): ?>
                    <div class="small text-muted mb-2">
                        Hijos: <?= implode(', ', array_map(fn($c) => $c->name, $childs)) ?>
                    </div>
                    <?php endif; ?>
                    <div class="mt-2">
                        <a href="<?= base_url() ?>FlowBuilder/edit/<?= $f->id ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="<?= base_url() ?>FlowBuilder/create?parent=<?= $f->id ?>" class="btn btn-sm btn-outline-info">+ Subflujo</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($parent_flows)): ?>
        <div class="col-12">
            <div class="alert alert-info">No hay flujos aun. Crea tu primer flujo para que los clientes vean opciones al escribir "menu".</div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Subflujos (segundo nivel) -->
    <?php 
    $child_flows = array_filter($flows, fn($f) => !empty($f->parent_flow_id));
    if ($child_flows): 
    ?>
    <hr class="my-4">
    <h5 class="mb-3">Subflujos</h5>
    <div class="row">
        <?php foreach ($child_flows as $f): 
            $tr = $this->db->where('flow_id', $f->id)->get('flow_triggers')->result();
            $pub = $this->db->where('flow_id', $f->id)->where('status', 'published')->order_by('version', 'DESC')->get('flow_versions')->row();
            $parent = $this->db->where('id', $f->parent_flow_id)->get('flows')->row();
        ?>
        <div class="col-md-4 mb-3">
            <div class="card flow-card" style="border-left: 3px solid #22C55E;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1 font-weight-bold"><?= htmlspecialchars($f->name) ?></h6>
                            <small class="text-muted">Padre: <?= $parent ? htmlspecialchars($parent->name) : '-' ?></small>
                        </div>
                        <a href="<?= base_url() ?>FlowBuilder/edit/<?= $f->id ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.flow-card { transition: box-shadow 0.2s, transform 0.2s; cursor: default; }
.flow-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); transform: translateY(-2px); }
</style>

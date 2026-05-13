<div class="container-fluid flex-grow-1 container-p-y">
    <div class="page-header">
        <h4>Flujos conversacionales</h4>
        <p>Crea y gestiona los flujos de conversación de tu bot</p>
    </div>
    <div class="card">
        <div class="card-header with-elements">
            <div class="card-header-elements ml-auto">
                <a href="<?= base_url() ?>FlowBuilder/create" class="btn btn-saas btn-saas-primary">+ Nuevo flujo</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Nombre</th><th>Triggers</th><th>Publicado</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($flows as $f):
                        $tr = $this->db->where('flow_id', $f->id)->get('flow_triggers')->result();
                        $pub = $this->db->where('flow_id', $f->id)->where('status', 'published')->order_by('version', 'DESC')->get('flow_versions')->row();
                        $draft = $this->db->where('flow_id', $f->id)->where('status', 'draft')->order_by('version', 'DESC')->get('flow_versions')->row();
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($f->name) ?></strong><br><small><?= htmlspecialchars($f->description ?? '') ?></small></td>
                        <td><?= implode(', ', array_map(fn($t) => $t->trigger_value, $tr)) ?: '<span class="text-muted">-</span>' ?></td>
                        <td><?= $pub ? 'v' . $pub->version . ' (' . substr($pub->published_at ?? '', 0, 10) . ')' : '-' ?></td>
                        <td><?= $f->is_active ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>' ?></td>
                        <td>
                            <a href="<?= base_url() ?>FlowBuilder/edit/<?= $f->id ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($flows)): ?><tr><td colspan="5" class="text-center text-muted">No hay flujos aún. Crea uno.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

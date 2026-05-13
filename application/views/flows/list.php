<div class="container-fluid flex-grow-1 container-p-y" style="min-height: calc(100vh - 60px);">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 10px;">
        <div>
            <h4 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="feather icon-git-branch" style="color: #6366F1;"></i>
                Flujos conversacionales
            </h4>
            <small class="text-muted">Create and manage your bot's conversation flows</small>
        </div>
        <a href="<?= base_url() ?>FlowBuilder/create" class="flow-btn flow-btn-primary" style="font-size: 0.85rem; padding: 8px 18px;">+ New Flow</a>
    </div>

    <?php if (empty($flows)): ?>
    <div style="text-align: center; padding: 80px 20px; background: #fff; border-radius: 16px; border: 1px solid #e5e7eb;">
        <div style="font-size: 3.5rem; margin-bottom: 16px;">🧩</div>
        <h5 style="color: #374151;">No flows yet</h5>
        <p class="text-muted" style="font-size: 0.9rem; max-width: 400px; margin: 0 auto 20px;">Start by creating your first conversation flow. Each flow can handle different customer intents like sales, support, or scheduling.</p>
        <a href="<?= base_url() ?>FlowBuilder/create" class="flow-btn flow-btn-primary" style="font-size: 0.85rem; padding: 10px 24px;">+ Create Your First Flow</a>
    </div>
    <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px;">
        <?php foreach ($flows as $f):
            $tr = $this->db->where('flow_id', $f->id)->get('flow_triggers')->result();
            $pub = $this->db->where('flow_id', $f->id)->where('status', 'published')->order_by('version', 'DESC')->get('flow_versions')->row();
            $draft = $this->db->where('flow_id', $f->id)->where('status', 'draft')->order_by('version', 'DESC')->get('flow_versions')->row();
            $nodeCount = $draft ? $this->db->where('flow_version_id', $draft->id)->get('flow_nodes')->num_rows() : 0;
        ?>
        <div style="background: #fff; border-radius: 14px; border: 1px solid #e5e7eb; padding: 18px; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.04); position: relative;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">
                <div style="flex: 1;">
                    <h6 style="margin: 0 0 4px; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                        <?= htmlspecialchars($f->name) ?>
                        <?php if ($f->is_active): ?>
                            <span style="width: 6px; height: 6px; background: #22C55E; border-radius: 50%; display: inline-block;"></span>
                        <?php else: ?>
                            <span style="width: 6px; height: 6px; background: #9ca3af; border-radius: 50%; display: inline-block;"></span>
                        <?php endif; ?>
                    </h6>
                    <?php if ($f->description): ?>
                        <small style="color: #6b7280;"><?= htmlspecialchars($f->description) ?></small>
                    <?php endif; ?>
                </div>
                <div style="display: flex; gap: 4px;">
                    <a href="<?= base_url() ?>FlowBuilder/edit/<?= $f->id ?>" class="flow-btn flow-btn-primary" style="font-size: 0.7rem; padding: 4px 10px;">Edit</a>
                </div>
            </div>

            <div style="display: flex; gap: 16px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6; font-size: 0.75rem; color: #6b7280;">
                <span>📦 <?= $nodeCount ?> nodes</span>
                <?php if ($pub): ?>
                    <span style="color: #22C55E;">🚀 v<?= $pub->version ?> published</span>
                <?php else: ?>
                    <span style="color: #9ca3af;">⏳ Draft</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($tr)): ?>
            <div style="margin-top: 8px; display: flex; gap: 4px; flex-wrap: wrap;">
                <?php foreach ($tr as $t): ?>
                    <span style="font-size: 0.65rem; background: #eef2ff; color: #6366F1; padding: 2px 8px; border-radius: 10px;">⚡ <?= htmlspecialchars($t->trigger_value) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.flow-btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; font-size: 0.8rem; color: #374151; transition: all 0.15s; text-decoration: none; white-space: nowrap; }
.flow-btn:hover { border-color: #6366F1; color: #6366F1; }
.flow-btn-primary { background: #6366F1; border-color: #6366F1; color: #fff; }
.flow-btn-primary:hover { background: #4F46E5; color: #fff; }
</style>

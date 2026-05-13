<!-- Flow Builder — Visual Conversation Flow Editor -->
<div class="layout-content flow-builder-layout">

    <!-- [ content ] Start -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div>
                <h4 class="font-weight-bold py-3 mb-0" style="display: flex; align-items: center; gap: 10px;">
                    <i class="feather icon-git-branch" style="color: var(--saas-primary);"></i>
                    Flow Builder
                </h4>
                <div class="text-muted small mt-0 d-block breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item active">Flow Builder</li>
                    </ol>
                </div>
            </div>
            <div class="d-flex gap-2" style="gap: 8px;">
                <button class="btn btn-outline-primary btn-sm" id="btnToggleMode" onclick="toggleMode()" title="Cambiar vista">
                    <i class="feather icon-layers"></i> <span id="modeLabel">Chat Tree</span>
                </button>
                <button class="btn btn-primary" onclick="openNodeModal(null, 0)">
                    <i class="feather icon-plus"></i> New Root Question
                </button>
            </div>
        </div>

        <!-- Tab bar: which flow to edit -->
        <div class="card mb-3">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <span class="font-weight-medium" style="font-size: 0.85rem; color: var(--saas-gray-600);">Edit mode:</span>
                    <div class="btn-group btn-group-sm" role="group" id="flowModeTabs">
                        <button type="button" class="btn btn-outline-primary active" data-mode="answers" onclick="setFlowMode('answers')">
                            <i class="feather icon-message-square"></i> Q&A Flow
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-mode="chat" onclick="setFlowMode('chat')">
                            <i class="feather icon-layout"></i> Chat Tree
                        </button>
                    </div>
                    <div class="ml-auto d-flex" style="gap: 8px;">
                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="feather icon-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="flowSearch" placeholder="Search questions..." oninput="filterFlow(this.value)">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flow Canvas -->
        <div class="card">
            <div class="card-body p-3">
                <div id="flowCanvas">
                    <div class="text-center py-5" id="flowEmpty">
                        <div style="font-size: 3rem; color: var(--saas-gray-300); margin-bottom: 1rem;">
                            <i class="feather icon-git-branch"></i>
                        </div>
                        <h5 style="color: var(--saas-gray-500);">No questions yet</h5>
                        <p class="text-muted small">Start building your conversation flow by adding your first question.</p>
                        <button class="btn btn-primary btn-sm" onclick="openNodeModal(null, 0)">
                            <i class="feather icon-plus"></i> Add First Question
                        </button>
                    </div>
                    <div id="flowTree" style="display: none;">
                        <!-- Tree rendered by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats row -->
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center" style="gap: 12px;">
                        <div style="width: 40px; height: 40px; background: var(--saas-primary-light); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--saas-primary);">
                            <i class="feather icon-message-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Questions</div>
                            <div class="font-weight-bold" id="statTotal">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center" style="gap: 12px;">
                        <div style="width: 40px; height: 40px; background: #dcfce7; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                            <i class="feather icon-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Active</div>
                            <div class="font-weight-bold" id="statActive">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body d-flex align-items-center" style="gap: 12px;">
                        <div style="width: 40px; height: 40px; background: #fef3c7; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #d97706;">
                            <i class="feather icon-share-2"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Branches</div>
                            <div class="font-weight-bold" id="statBranches">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ content ] End -->
</div>

<!-- Node Edit Modal -->
<div class="modal fade" id="nodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100);">
                <h5 class="modal-title" id="nodeModalTitle">
                    <i class="feather icon-edit-2" style="color: var(--saas-primary);"></i>
                    <span id="nodeModalLabel">New Question</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for Questions (answers mode) -->
                <form id="formQuestion" class="flow-edit-form" data-type="answers">
                    <input type="hidden" name="que_id" id="que_id">
                    <input type="hidden" name="que_parent" id="que_parent" value="0">

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Question Text</label>
                            <textarea class="form-control" name="que_question" id="que_question" rows="2" placeholder="e.g. ¿Qué producto deseas ordenar?" required></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" name="que_order" id="que_order" value="1" min="1">
                        </div>
                        <div class="form-group col-md-8">
                            <label class="form-label">Parent Question (0 = root)</label>
                            <select class="form-control" name="que_parent_select" id="que_parent_select">
                                <option value="0">— Root (no parent) —</option>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Form for Chat Questions (chat mode) -->
                <form id="formChatQuestion" class="flow-edit-form" data-type="chat" style="display:none;">
                    <input type="hidden" name="chq_id" id="chq_id">
                    <input type="hidden" name="chq_parent" id="chq_parent_2" value="0">

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Question Text</label>
                            <textarea class="form-control" name="chq_text" id="chq_text" rows="2" placeholder="e.g. ¿Cómo deseas pagar?" required></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="chq_status" id="chq_status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="chq_type" id="chq_type" onchange="toggleChatTypeFields()">
                                <option value="1">List</option>
                                <option value="2">Yes / No</option>
                                <option value="3">Text Input</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3" id="listSelectGroup">
                            <label class="form-label">List</label>
                            <select class="form-control" name="lis_id" id="lis_id">
                                <option value="">— Select List —</option>
                                <?php if (!empty($lists)): ?>
                                <?php foreach ($lists as $list): ?>
                                <option value="<?= $list->lis_id ?>"><?= htmlspecialchars($list->lis_name) ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3" id="responseSelectGroup" style="display:none;">
                            <label class="form-label">Response</label>
                            <select class="form-control" name="chq_response" id="chq_response">
                                <option value="">— Select —</option>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" name="chq_order" id="chq_order" value="1" min="1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Parent Question</label>
                            <select class="form-control" name="chq_parent_select" id="chq_parent_select">
                                <option value="0">— Root (no parent) —</option>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Answers section (shown when editing a question in answers mode) -->
                <div id="answersSection" style="display:none;" class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0" style="color: var(--saas-gray-600);">
                            <i class="feather icon-list"></i> Answers / Options
                        </h6>
                        <button class="btn btn-sm btn-outline-primary" onclick="openAnswerModal()">
                            <i class="feather icon-plus"></i> Add Answer
                        </button>
                    </div>
                    <div id="answersList" class="flow-answers-list">
                        <!-- Answers rendered by JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--saas-gray-100);">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnDeleteNode" style="display:none;" onclick="deleteCurrentNode()">
                    <i class="feather icon-trash-2"></i> Delete
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveNode" onclick="saveCurrentNode()">
                    <i class="feather icon-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Answer Edit Modal -->
<div class="modal fade" id="answerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid var(--saas-gray-100);">
                <h5 class="modal-title">
                    <i class="feather icon-check-square" style="color: var(--saas-primary);"></i>
                    <span id="answerModalLabel">New Answer</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAnswer">
                    <input type="hidden" name="ans_id" id="ans_id">
                    <input type="hidden" name="que_id" id="answer_que_id">
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="ans_description" id="ans_description" placeholder="e.g. Product A - $10">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="ans_price" id="ans_price" placeholder="0.00">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="ans_qty" id="ans_qty" placeholder="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" name="ans_order" id="ans_order" placeholder="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">More Information</label>
                        <textarea class="form-control" name="ans_more_information" id="ans_more_information" rows="2" placeholder="Additional info..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--saas-gray-100);">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnDeleteAnswer" style="display:none;" onclick="deleteCurrentAnswer()">
                    <i class="feather icon-trash-2"></i> Delete
                </button>
                <button type="button" class="btn btn-primary" onclick="saveAnswer()">
                    <i class="feather icon-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-body text-center py-4">
                <div style="font-size: 2.5rem; color: var(--saas-danger); margin-bottom: 0.75rem;">
                    <i class="feather icon-alert-triangle"></i>
                </div>
                <h6 id="confirmTitle">Are you sure?</h6>
                <p class="text-muted small mb-0" id="confirmMessage">This action cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid var(--saas-gray-100);">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmActionBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast notification container -->
<div id="toastContainer" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>

<script>
    const BASE_URL = '<?= base_url() ?>';
    const LISTS = <?= json_encode($lists) ?>;
</script>

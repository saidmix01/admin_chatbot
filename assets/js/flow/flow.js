/**
 * Wapi Flow Builder — Visual Conversation Flow Editor
 *
 * Features:
 * - Tree/mindmap view of questions
 * - Two modes: Q&A Flow and Chat Tree
 * - Drag-to-reorder nodes
 * - Create, edit, delete questions inline
 * - Manage answers per question
 * - Search/filter across nodes
 */

(function () {
    'use strict';

    // ─── State ───
    let currentMode = 'answers'; // 'answers' | 'chat'
    let editingNode = null;     // node data currently being edited
    let editingAnswer = null;   // answer being edited
    let currentNodeId = null;   // for delete operations
    let currentAnswerId = null;
    let allNodes = [];          // flattened list for search

    // ─── Init ───
    document.addEventListener('DOMContentLoaded', function () {
        loadTree();

        // Bootstrap modals need manual reinit for dynamic content
        $('#nodeModal').on('hidden.bs.modal', function () {
            editingNode = null;
            currentNodeId = null;
            document.getElementById('btnDeleteNode').style.display = 'none';
            document.getElementById('answersSection').style.display = 'none';
        });

        $('#answerModal').on('hidden.bs.modal', function () {
            editingAnswer = null;
            currentAnswerId = null;
            document.getElementById('btnDeleteAnswer').style.display = 'none';
        });
    });

    // ─── Mode switching ───
    window.toggleMode = function () {
        setFlowMode(currentMode === 'answers' ? 'chat' : 'answers');
    };

    window.setFlowMode = function (mode) {
        currentMode = mode;
        document.querySelectorAll('#flowModeTabs button').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });
        document.getElementById('modeLabel').textContent =
            mode === 'answers' ? 'Q&A Flow' : 'Chat Tree';
        loadTree();
    };

    // ─── Load tree ───
    function loadTree() {
        const container = document.getElementById('flowTree');
        const empty = document.getElementById('flowEmpty');

        container.innerHTML = '<div class="flow-loading"><i class="feather icon-loader"></i></div>';
        container.style.display = 'block';
        empty.style.display = 'none';

        const endpoint = currentMode === 'answers'
            ? BASE_URL + 'Flow/api_question_tree'
            : BASE_URL + 'Flow/api_chat_tree';

        fetch(endpoint)
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status && res.data) {
                    allNodes = [];
                    flattenNodes(res.data, 0);
                    if (res.data.length === 0) {
                        container.style.display = 'none';
                        empty.style.display = 'block';
                        updateStats([]);
                    } else {
                        container.style.display = 'block';
                        empty.style.display = 'none';
                        container.innerHTML = renderTree(res.data, 0);
                        updateStats(res.data);
                        bindDragEvents();
                    }
                } else {
                    container.innerHTML =
                        '<div class="text-center py-5 text-muted">Error loading flow. ' +
                        (res.message || '') + '</div>';
                }
            })
            .catch(function (err) {
                container.innerHTML =
                    '<div class="text-center py-5 text-muted">Network error: ' + err.message + '</div>';
            });
    }

    function flattenNodes(nodes, depth) {
        nodes.forEach(function (n) {
            n._depth = depth;
            allNodes.push(n);
            if (n.children && n.children.length) {
                flattenNodes(n.children, depth + 1);
            }
        });
    }

    // ─── Render Tree ───
    function renderTree(nodes, depth) {
        if (!nodes || nodes.length === 0) return '';
        var html = '<div class="flow-tree-container">';

        nodes.forEach(function (node, idx) {
            var hasChildren = node.children && node.children.length > 0;
            var isRoot = depth === 0;
            var isActive = node.status !== undefined ? node.status === 1 : true;

            // Connector line above (not for root)
            if (depth > 0) {
                html += '<div class="flow-connector"><div class="flow-connector-line"></div></div>';
            }

            // Add button before (between siblings)
            if (idx === 0 && depth > 0) {
                html += '<div class="flow-add-between">' +
                    '<button onclick="openNodeModal(null, ' + node.parent + ')" title="Add sibling">' +
                    '<i class="feather icon-plus"></i></button></div>';
            }

            // Node card
            html += '<div class="flow-node-wrapper">';
            html += '<div class="flow-node' +
                (isRoot ? ' root-node' : '') +
                (!isActive ? ' inactive' : '') +
                '" data-id="' + node.id + '" data-parent="' + (node.parent || 0) + '"' +
                ' draggable="true" ondragstart="onDragStart(event)" ondragover="onDragOver(event)"' +
                ' ondrop="onDrop(event)" ondragend="onDragEnd(event)"' +
                ' onclick="openNodeModal(' + node.id + ', ' + (node.parent || 0) + ')">';

            // Drag handle
            html += '<div class="drag-handle" onclick="event.stopPropagation();">⠿</div>';

            // Header
            html += '<div class="node-header">';
            var text = node.question || node.text || '(no text)';
            html += '<div class="node-question">' + escapeHtml(text) + '</div>';
            html += '<div class="node-badges">';

            // Status badge
            if (currentMode === 'chat') {
                html += '<span class="node-badge ' + (isActive ? 'active' : 'inactive') + '">' +
                    (isActive ? 'ON' : 'OFF') + '</span>';
                // Type badge
                var typeMap = { '1': 'List', '2': 'Yes/No', '3': 'Text' };
                var typeLabel = typeMap[node.type] || '?';
                var typeClass = node.type === 1 ? 'type-list' : node.type === 2 ? 'type-yesno' : 'type-text';
                html += '<span class="node-badge ' + typeClass + '">' + typeLabel + '</span>';
            } else {
                var ansCount = node.answers ? node.answers.length : 0;
                html += '<span class="node-badge type-list">' + ansCount + ' answers</span>';
            }
            html += '</div></div>';

            // Meta row
            html += '<div class="node-meta">';
            html += '<span><i class="feather icon-hash"></i> ID ' + node.id + '</span>';
            html += '<span><i class="feather icon-arrow-right"></i> Order ' + (node.order || 0) + '</span>';
            if (hasChildren) {
                html += '<span><i class="feather icon-share-2"></i> ' + node.children.length + ' children</span>';
            }
            html += '</div>';

            // Actions bar
            html += '<div class="node-actions" onclick="event.stopPropagation();">';
            html += '<button onclick="openNodeModal(' + node.id + ', ' + (node.parent || 0) + ')" title="Edit">' +
                '<i class="feather icon-edit-2"></i> Edit</button>';
            html += '<button onclick="openNodeModal(null, ' + node.id + ')" title="Add child">' +
                '<i class="feather icon-plus-circle"></i> Add child</button>';
            html += '<button class="danger" onclick="confirmDeleteNode(' + node.id + ')" title="Delete">' +
                '<i class="feather icon-trash-2"></i></button>';
            html += '</div>';

            html += '</div>'; // .flow-node

            // Children
            if (hasChildren) {
                html += '<div class="flow-children">';
                node.children.forEach(function (child) {
                    html += '<div class="flow-child-wrapper">';
                    html += renderTree([child], depth + 1);
                    html += '</div>';
                });
                html += '</div>';
            } else {
                // Add child button at leaf
                html += '<div class="flow-add-between">' +
                    '<button onclick="openNodeModal(null, ' + node.id + ')" title="Add child question">' +
                    '<i class="feather icon-plus"></i></button></div>';
            }

            html += '</div>'; // .flow-node-wrapper

            // Add button after (between siblings)
            var nextParent = nodes[idx + 1] ? nodes[idx + 1].parent : node.parent;
            if (depth > 0 && idx < nodes.length - 1) {
                html += '<div class="flow-add-between">' +
                    '<button onclick="openNodeModal(null, ' + nextParent + ')" title="Add sibling">' +
                    '<i class="feather icon-plus"></i></button></div>';
            }
        });

        html += '</div>'; // .flow-tree-container
        return html;
    }

    // ─── Drag & Drop ───
    window.onDragStart = function (e) {
        var node = e.target.closest('.flow-node');
        if (!node) return;
        node.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', node.dataset.id);
    };

    window.onDragOver = function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var node = e.target.closest('.flow-node');
        if (node && !node.classList.contains('dragging')) {
            node.classList.add('drag-over');
        }
    };

    window.onDragEnd = function (e) {
        document.querySelectorAll('.flow-node').forEach(function (n) {
            n.classList.remove('dragging', 'drag-over');
        });
    };

    window.onDrop = function (e) {
        e.preventDefault();
        document.querySelectorAll('.flow-node').forEach(function (n) {
            n.classList.remove('dragging', 'drag-over');
        });

        var fromId = e.dataTransfer.getData('text/plain');
        var target = e.target.closest('.flow-node');
        if (!target || !fromId || fromId === target.dataset.id) return;

        // For now, just reorder siblings in current mode
        var parent = target.dataset.parent;
        var parentNode = target.closest('.flow-tree-container');

        var siblings = parentNode ? parentNode.querySelectorAll('.flow-node') : [];
        var items = [];
        siblings.forEach(function (s, idx) {
            items.push({
                que_id: s.dataset.id,
                chq_id: s.dataset.id,
                que_order: idx + 1,
                chq_order: idx + 1,
                que_parent: parent,
                chq_parent: parent
            });
        });

        var endpoint = currentMode === 'answers'
            ? BASE_URL + 'Flow/reorder_questions'
            : BASE_URL + 'Flow/reorder_chat';

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: items })
        })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status) {
                    showToast('Reordered successfully', 'success');
                    loadTree();
                } else {
                    showToast('Error reordering: ' + res.message, 'error');
                }
            })
            .catch(function (err) {
                showToast('Network error: ' + err.message, 'error');
            });
    };

    function bindDragEvents() {
        // Cleanup handled by onDragEnd
    }

    // ─── Search / Filter ───
    window.filterFlow = function (query) {
        var q = query.toLowerCase().trim();
        document.querySelectorAll('.flow-node').forEach(function (node) {
            var text = (node.querySelector('.node-question') || {}).textContent || '';
            if (!q) {
                node.classList.remove('search-hide', 'search-match');
                // Show parent containers
                var wrapper = node.closest('.flow-node-wrapper');
                if (wrapper) wrapper.style.display = '';
                return;
            }
            var match = text.toLowerCase().indexOf(q) !== -1;
            if (match) {
                node.classList.remove('search-hide');
                node.classList.add('search-match');
                // Show all ancestor wrappers
                var el = node.closest('.flow-node-wrapper');
                while (el) {
                    el.style.display = '';
                    el = el.parentElement ? el.parentElement.closest('.flow-node-wrapper') : null;
                }
            } else {
                node.classList.add('search-hide');
                node.classList.remove('search-match');
            }
        });

        // Hide wrappers where all children are hidden
        document.querySelectorAll('.flow-node-wrapper').forEach(function (wrapper) {
            var visibleNodes = wrapper.querySelectorAll('.flow-node:not(.search-hide)');
            if (visibleNodes.length === 0) {
                var addBtns = wrapper.querySelectorAll('.flow-add-between');
                var allHidden = true;
                wrapper.querySelectorAll('.flow-node').forEach(function (n) {
                    if (!n.classList.contains('search-hide')) allHidden = false;
                });
                if (allHidden) wrapper.style.display = 'none';
            }
        });
    };

    // ─── Open Node Modal ───
    window.openNodeModal = function (nodeId, parentId) {
        var formAnswers = document.getElementById('formQuestion');
        var formChat = document.getElementById('formChatQuestion');

        if (currentMode === 'answers') {
            formAnswers.style.display = '';
            formChat.style.display = 'none';
            document.getElementById('answersSection').style.display = nodeId ? '' : 'none';
            populateParentSelect('que_parent_select', nodeId);

            if (nodeId) {
                // Load existing question from its parent level
                var p = parentId || 0;
                fetch(BASE_URL + 'Flow/api_questions?parent=' + p)
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        var found = null;
                        if (res.status && res.data) {
                            res.data.forEach(function (n) {
                                if (parseInt(n.que_id) === parseInt(nodeId)) found = n;
                            });
                        }
                        // If not found at this level, try root
                        if (!found && p !== 0) {
                            fetch(BASE_URL + 'Flow/api_questions?parent=0')
                                .then(function (r2) { return r2.json(); })
                                .then(function (res2) {
                                    if (res2.status && res2.data) {
                                        res2.data.forEach(function (n) {
                                            if (parseInt(n.que_id) === parseInt(nodeId)) found = n;
                                        });
                                    }
                                    fillQuestionForm(found, nodeId, parentId);
                                });
                        } else {
                            fillQuestionForm(found, nodeId, parentId);
                        }
                    });
            } else {
                // New node
                fillQuestionForm(null, null, parentId);
            }
        } else {
            formAnswers.style.display = 'none';
            formChat.style.display = '';
            document.getElementById('answersSection').style.display = 'none';
            populateParentSelect('chq_parent_select', nodeId);

            if (nodeId) {
                fetch(BASE_URL + 'Flow/api_chat_questions?parent=' + (parentId || 0))
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res.status && res.data) {
                            var found = null;
                            res.data.forEach(function (n) {
                                if (parseInt(n.chq_id) === parseInt(nodeId)) found = n;
                            });
                            fillChatForm(found, nodeId, parentId);
                        } else {
                            fillChatForm(null, nodeId, parentId);
                        }
                    });
            } else {
                fillChatForm(null, null, parentId);
            }
        }

        $('#nodeModal').modal('show');
    };

    function fillQuestionForm(data, nodeId, parentId) {
        var form = document.getElementById('formQuestion');
        form.querySelector('#que_id').value = data ? data.que_id : (nodeId || '');
        form.querySelector('#que_question').value = data ? (data.que_question || '') : '';
        form.querySelector('#que_order').value = data ? (data.que_order || 1) : 1;
        form.querySelector('#que_parent').value = data ? (data.que_parent || (parentId || 0)) : (parentId || 0);
        form.querySelector('#que_parent_select').value = data ? (data.que_parent || (parentId || 0)) : (parentId || 0);

        var title = document.getElementById('nodeModalLabel');
        title.textContent = data ? 'Edit Question #' + data.que_id : 'New Question';

        var delBtn = document.getElementById('btnDeleteNode');
        delBtn.style.display = data ? '' : 'none';
        currentNodeId = data ? data.que_id : null;

        // Load answers
        if (data && data.que_id) {
            loadAnswers(data.que_id);
        } else {
            document.getElementById('answersSection').style.display = 'none';
        }
    }

    function fillChatForm(data, nodeId, parentId) {
        var form = document.getElementById('formChatQuestion');
        form.querySelector('#chq_id').value = data ? data.chq_id : (nodeId || '');
        form.querySelector('#chq_text').value = data ? (data.chq_text || '') : '';
        form.querySelector('#chq_status').value = data ? (data.chq_status || 1) : 1;
        form.querySelector('#chq_type').value = data ? (data.chq_type || 1) : 1;
        form.querySelector('#lis_id').value = data ? (data.lis_id || '') : '';
        form.querySelector('#chq_response').value = data ? (data.chq_response || '') : '';
        form.querySelector('#chq_order').value = data ? (data.chq_order || 1) : 1;
        form.querySelector('#chq_parent_2').value = data ? (data.chq_parent || (parentId || 0)) : (parentId || 0);
        form.querySelector('#chq_parent_select').value = data ? (data.chq_parent || (parentId || 0)) : (parentId || 0);

        var title = document.getElementById('nodeModalLabel');
        title.textContent = data ? 'Edit Question #' + data.chq_id : 'New Question';

        var delBtn = document.getElementById('btnDeleteNode');
        delBtn.style.display = data ? '' : 'none';
        currentNodeId = data ? data.chq_id : null;

        toggleChatTypeFields();
    }

    function populateParentSelect(selectId, excludeId) {
        var select = document.getElementById(selectId);
        if (!select) return;
        var currentVal = select.value;
        select.innerHTML = '<option value="0">— Root (no parent) —</option>';

        allNodes.forEach(function (n) {
            var id = n.que_id || n.chq_id || n.id;
            if (id && parseInt(id) !== parseInt(excludeId)) {
                var text = n.question || n.text || '(no text)';
                var opt = document.createElement('option');
                opt.value = id;
                opt.textContent = '#' + id + ' ' + text.substring(0, 50);
                select.appendChild(opt);
            }
        });

        if (currentVal) select.value = currentVal;
    }

    // ─── Chat type fields toggle ───
    window.toggleChatTypeFields = function () {
        var type = parseInt(document.getElementById('chq_type').value);
        document.getElementById('listSelectGroup').style.display = type === 1 ? '' : 'none';
        document.getElementById('responseSelectGroup').style.display = type === 2 ? '' : 'none';
    };

    // ─── Save current node ───
    window.saveCurrentNode = function () {
        if (currentMode === 'answers') {
            var form = document.getElementById('formQuestion');
            var data = {
                que_id: form.querySelector('#que_id').value || null,
                que_question: form.querySelector('#que_question').value,
                que_order: parseInt(form.querySelector('#que_order').value) || 1,
                que_parent: parseInt(form.querySelector('#que_parent_select').value) || 0
            };

            if (!data.que_question.trim()) {
                showToast('Question text is required', 'error');
                return;
            }

            fetch(BASE_URL + 'Flow/save_question', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.status) {
                        showToast('Question saved!', 'success');
                        $('#nodeModal').modal('hide');
                        loadTree();
                    } else {
                        showToast('Error: ' + res.message, 'error');
                    }
                })
                .catch(function (err) {
                    showToast('Network error: ' + err.message, 'error');
                });
        } else {
            var form = document.getElementById('formChatQuestion');
            var data = {
                chq_id: form.querySelector('#chq_id').value || null,
                chq_text: form.querySelector('#chq_text').value,
                chq_order: parseInt(form.querySelector('#chq_order').value) || 1,
                chq_parent: parseInt(form.querySelector('#chq_parent_select').value) || 0,
                chq_status: parseInt(form.querySelector('#chq_status').value) || 1,
                chq_type: parseInt(form.querySelector('#chq_type').value) || 1,
                chq_response: form.querySelector('#chq_response').value || null,
                lis_id: form.querySelector('#lis_id').value || null
            };

            if (!data.chq_text.trim()) {
                showToast('Question text is required', 'error');
                return;
            }

            fetch(BASE_URL + 'Flow/save_chat_question', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.status) {
                        showToast('Question saved!', 'success');
                        $('#nodeModal').modal('hide');
                        loadTree();
                    } else {
                        showToast('Error: ' + res.message, 'error');
                    }
                })
                .catch(function (err) {
                    showToast('Network error: ' + err.message, 'error');
                });
        }
    };

    // ─── Delete node ───
    window.confirmDeleteNode = function (nodeId) {
        document.getElementById('confirmTitle').textContent = 'Delete this question?';
        document.getElementById('confirmMessage').textContent =
            'This will delete this question and all its children. This cannot be undone.';
        document.getElementById('confirmActionBtn').onclick = function () {
            performDelete(nodeId);
            $('#confirmModal').modal('hide');
        };
        $('#confirmModal').modal('show');
    };

    window.deleteCurrentNode = function () {
        if (currentNodeId) {
            confirmDeleteNode(currentNodeId);
        }
    };

    function performDelete(nodeId) {
        var endpoint = currentMode === 'answers'
            ? BASE_URL + 'Flow/delete_question'
            : BASE_URL + 'Flow/delete_chat_question';
        var key = currentMode === 'answers' ? 'que_id' : 'chq_id';
        var body = {};
        body[key] = nodeId;

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status) {
                    showToast('Deleted successfully', 'success');
                    $('#nodeModal').modal('hide');
                    loadTree();
                } else {
                    showToast('Error: ' + res.message, 'error');
                }
            })
            .catch(function (err) {
                showToast('Network error: ' + err.message, 'error');
            });
    }

    // ─── Answers ───
    function loadAnswers(queId) {
        var section = document.getElementById('answersSection');
        section.style.display = '';

        fetch(BASE_URL + 'Flow/api_answers?que_id=' + queId)
            .then(function (r) { return r.json(); })
            .then(function (res) {
                renderAnswers(res.data || []);
            });
    }

    function renderAnswers(answers) {
        var container = document.getElementById('answersList');
        if (!answers || answers.length === 0) {
            container.innerHTML =
                '<div class="text-muted small py-2 text-center">No answers yet. Click "Add Answer" to create one.</div>';
            return;
        }

        var html = '';
        answers.forEach(function (a) {
            html += '<div class="flow-answer-item">';
            html += '<span class="answer-order text-muted small">#' + (a.ans_order || '') + '</span>';
            html += '<span class="answer-text">' + escapeHtml(a.ans_description || '(no description)') + '</span>';
            if (a.ans_price) {
                html += '<span class="answer-price">$' + parseFloat(a.ans_price).toFixed(0) + '</span>';
            }
            html += '<span class="answer-actions">';
            html += '<button onclick="openAnswerModal(' + a.ans_id + ')" title="Edit">' +
                '<i class="feather icon-edit-2"></i></button>';
            html += '<button class="danger" onclick="confirmDeleteAnswer(' + a.ans_id + ')" title="Delete">' +
                '<i class="feather icon-trash-2"></i></button>';
            html += '</span></div>';
        });
        container.innerHTML = html;
    }

    window.openAnswerModal = function (ansId) {
        var form = document.getElementById('formAnswer');
        var queId = document.getElementById('que_id').value;

        if (ansId) {
            fetch(BASE_URL + 'Flow/api_answers?que_id=' + queId)
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.status && res.data) {
                        var found = null;
                        res.data.forEach(function (a) {
                            if (parseInt(a.ans_id) === parseInt(ansId)) found = a;
                        });
                        fillAnswerForm(found, queId);
                    } else {
                        fillAnswerForm(null, queId);
                    }
                });
        } else {
            fillAnswerForm(null, queId);
        }

        $('#answerModal').modal('show');
    };

    function fillAnswerForm(data, queId) {
        var form = document.getElementById('formAnswer');
        form.querySelector('#ans_id').value = data ? data.ans_id : '';
        form.querySelector('#answer_que_id').value = queId;
        form.querySelector('#ans_description').value = data ? (data.ans_description || '') : '';
        form.querySelector('#ans_price').value = data ? (data.ans_price || '') : '';
        form.querySelector('#ans_qty').value = data ? (data.ans_qty || '') : '';
        form.querySelector('#ans_order').value = data ? (data.ans_order || '') : '';
        form.querySelector('#ans_more_information').value = data ? (data.ans_more_information || '') : '';

        var title = document.getElementById('answerModalLabel');
        title.textContent = data ? 'Edit Answer' : 'New Answer';

        var delBtn = document.getElementById('btnDeleteAnswer');
        delBtn.style.display = data ? '' : 'none';
        currentAnswerId = data ? data.ans_id : null;
    }

    window.saveAnswer = function () {
        var form = document.getElementById('formAnswer');
        var data = {
            ans_id: form.querySelector('#ans_id').value || null,
            que_id: form.querySelector('#answer_que_id').value,
            ans_description: form.querySelector('#ans_description').value,
            ans_price: form.querySelector('#ans_price').value || null,
            ans_qty: form.querySelector('#ans_qty').value || null,
            ans_order: form.querySelector('#ans_order').value || null,
            ans_more_information: form.querySelector('#ans_more_information').value || ''
        };

        if (!data.ans_description.trim()) {
            showToast('Description is required', 'error');
            return;
        }

        fetch(BASE_URL + 'Flow/save_answer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status) {
                    showToast('Answer saved!', 'success');
                    $('#answerModal').modal('hide');
                    loadAnswers(document.getElementById('que_id').value);
                } else {
                    showToast('Error: ' + res.message, 'error');
                }
            })
            .catch(function (err) {
                showToast('Network error: ' + err.message, 'error');
            });
    };

    window.deleteCurrentAnswer = function () {
        if (currentAnswerId) {
            confirmDeleteAnswer(currentAnswerId);
        }
    };

    window.confirmDeleteAnswer = function (ansId) {
        document.getElementById('confirmTitle').textContent = 'Delete this answer?';
        document.getElementById('confirmMessage').textContent = 'This cannot be undone.';
        document.getElementById('confirmActionBtn').onclick = function () {
            performDeleteAnswer(ansId);
            $('#confirmModal').modal('hide');
        };
        $('#confirmModal').modal('show');
    };

    function performDeleteAnswer(ansId) {
        fetch(BASE_URL + 'Flow/delete_answer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ans_id: ansId })
        })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.status) {
                    showToast('Answer deleted', 'success');
                    loadAnswers(document.getElementById('que_id').value);
                } else {
                    showToast('Error: ' + res.message, 'error');
                }
            });
    }

    // ─── Stats ───
    function updateStats(nodes) {
        var total = 0;
        var active = 0;
        var branches = 0;

        function count(ns) {
            ns.forEach(function (n) {
                total++;
                var isActive = n.status !== undefined ? n.status === 1 : true;
                if (isActive) active++;
                if (n.children && n.children.length > 0) {
                    branches++;
                    count(n.children);
                }
            });
        }

        count(nodes);
        document.getElementById('statTotal').textContent = total;
        document.getElementById('statActive').textContent = active;
        document.getElementById('statBranches').textContent = branches;
    }

    // ─── Toast ───
    function showToast(message, type) {
        type = type || 'info';
        var icons = { success: 'feather icon-check-circle', error: 'feather icon-alert-circle', info: 'feather icon-info' };
        var container = document.getElementById('toastContainer');
        var toast = document.createElement('div');
        toast.className = 'flow-toast ' + type;
        toast.innerHTML = '<i class="' + (icons[type] || icons.info) + '"></i> ' + escapeHtml(message);
        container.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);
    }

    // ─── Utilities ───
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

})();

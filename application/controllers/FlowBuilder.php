<?php
class FlowBuilder extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
        $this->load->model('Store/Store_model', 'Store_model');
    }

    private function json_response($payload, $status_code = 200) {
        $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload));
    }

    private function require_auth() {
        if (!$this->session->userdata('login')) {
            if ($this->input->is_ajax_request()) {
                $this->json_response(['status' => false, 'message' => 'No autorizado'], 401);
                exit;
            }
            redirect(base_url());
        }
    }

    private function get_store() {
        $this->Store_model->data = ["s.us_id" => $this->session->userdata('us_id')];
        $stores = $this->Store_model->get_stores();
        return !empty($stores['data']) ? $stores['data'][0] : null;
    }

    private function assert_store_or_fail() {
        $store = $this->get_store();
        if (!$store) {
            if ($this->input->is_ajax_request()) {
                $this->json_response(['status' => false, 'message' => 'Sin tienda'], 400);
                exit;
            }
            show_error('Sin tienda');
            exit;
        }
        return $store;
    }

    private function assert_version_access_or_fail($version_id, $store_id) {
        $row = $this->db
            ->select('v.id, v.flow_id, f.tenant_id')
            ->from('flow_versions v')
            ->join('flows f', 'f.id = v.flow_id', 'inner')
            ->where('v.id', (int) $version_id)
            ->get()
            ->row();

        if (!$row || (string) $row->tenant_id !== (string) $store_id) {
            $this->json_response(['status' => false, 'message' => 'No autorizado'], 403);
            exit;
        }

        return $row;
    }

    private function load_admin_view($view, $data = []) {
        $user_data = get_user_content(["us_id" => $this->session->userdata('us_id')]);
        $this->Menus_profile_model->data = ["us_id" => $this->session->userdata('us_id')];
        $menus = $this->Menus_profile_model->get_menu_user();
        $header = [
            "title" => $data['title'] ?? 'Flujos',
            "active_menu" => "flows",
            "user_data" => $user_data["data"],
            "menus" => $menus["data"] ?? []
        ];
        $data_footer = [];
        $this->load->view('includes/header', $header);
        $this->load->view('flows/' . $view, $data);
        $this->load->view('includes/footer', $data_footer);
    }

    public function index() {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        $flows = $this->db->where('tenant_id', $store->sto_id)->order_by('created_at', 'DESC')->get('flows')->result();
        $this->load_admin_view('list', ['flows' => $flows, 'store' => $store]);
    }

    public function create() {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        if ($this->input->method() === 'post') {
            $name = $this->input->post('name');
            $desc = $this->input->post('description');
            $order = intval($this->input->post('display_order') ?: 0);
            $trigger = $this->input->post('trigger_value');
            if (!$name) { $this->json_response(['status' => false, 'message' => 'Nombre requerido'], 400); return; }
            $this->db->insert('flows', [
                'tenant_id' => $store->sto_id, 'name' => $name,
                'description' => $desc, 'is_active' => 1,
                'display_order' => $order
            ]);
            $flow_id = $this->db->insert_id();
            if ($trigger) {
                $this->db->insert('flow_triggers', ['flow_id' => $flow_id, 'trigger_type' => 'keyword', 'trigger_value' => strtolower(trim($trigger))]);
            }
            $this->db->insert('flow_versions', ['flow_id' => $flow_id, 'version' => 1, 'status' => 'draft']);
            $this->json_response(['status' => true, 'message' => 'Flujo creado', 'id' => $flow_id]);
            return;
        }
        $parent_flows = $this->db->where('tenant_id', $store->sto_id)->where('parent_flow_id', null)->order_by('display_order', 'ASC')->get('flows')->result();
        $services = $this->db->query("SELECT s.* FROM services s JOIN service_user su ON s.ser_id = su.ser_id WHERE su.us_id = ? AND s.ser_status = 1 ORDER BY s.ser_name", [$this->session->userdata('us_id')])->result();
        $parent_id = intval($this->input->get('parent') ?: 0);
        $this->load_admin_view('create', ['store' => $store, 'parent_flows' => $parent_flows, 'services' => $services, 'parent_id' => $parent_id]);
    }

    public function edit($id = 0) {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        $flow = $this->db->where('id', $id)->where('tenant_id', $store->sto_id)->get('flows')->row();
        if (!$flow) { show_error('Flujo no encontrado'); return; }
        $triggers = $this->db->where('flow_id', $id)->get('flow_triggers')->result();
        $published = $this->db->where('flow_id', $id)->where('status', 'published')->order_by('version', 'DESC')->get('flow_versions')->row();
        $version = $this->db->where('flow_id', $id)->where('status', 'draft')->order_by('version', 'DESC')->get('flow_versions')->row();
        if (!$version) {
            $maxv = $this->db->select_max('version')->where('flow_id', $id)->get('flow_versions')->row()->version ?? 0;
            $this->db->insert('flow_versions', ['flow_id' => $id, 'version' => $maxv + 1, 'status' => 'draft']);
            $version = $this->db->where('flow_id', $id)->where('status', 'draft')->get('flow_versions')->row();

            if ($published && $version) {
                $this->db->trans_start();
                $pubNodes = $this->db->where('flow_version_id', $published->id)->order_by('id')->get('flow_nodes')->result();
                foreach ($pubNodes as $n) {
                    $this->db->insert('flow_nodes', [
                        'flow_version_id' => $version->id,
                        'node_key' => $n->node_key,
                        'type' => $n->type,
                        'payload_json' => $n->payload_json
                    ]);
                }

                $pubEdges = $this->db->where('flow_version_id', $published->id)->order_by('id')->get('flow_edges')->result();
                foreach ($pubEdges as $e) {
                    $this->db->insert('flow_edges', [
                        'flow_version_id' => $version->id,
                        'from_node_key' => $e->from_node_key,
                        'to_node_key' => $e->to_node_key,
                        'rule_json' => $e->rule_json
                    ]);
                }
                $this->db->trans_complete();
            }
        }
        $nodes = $this->db->where('flow_version_id', $version->id)->order_by('id')->get('flow_nodes')->result();
        $edges = $this->db->where('flow_version_id', $version->id)->get('flow_edges')->result();
        $this->load_admin_view('editor', [
            'flow' => $flow, 'triggers' => $triggers, 'version' => $version,
            'nodes' => $nodes, 'edges' => $edges, 'published' => $published, 'store' => $store
        ]);
    }

    public function save_nodes($version_id = 0) {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        $this->assert_version_access_or_fail($version_id, $store->sto_id);
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) { $this->json_response(['status' => false, 'message' => 'Datos inválidos'], 400); return; }

        $seenNodeKeys = [];
        if (!empty($input['nodes'])) {
            foreach ($input['nodes'] as $n) {
                $key = trim((string)($n['node_key'] ?? ''));
                $type = trim((string)($n['type'] ?? ''));
                if ($key === '' || $type === '') {
                    $this->json_response(['status' => false, 'message' => 'Hay nodos inválidos (node_key/type)'], 400);
                    return;
                }
                if (isset($seenNodeKeys[$key])) {
                    $this->json_response(['status' => false, 'message' => "node_key duplicado: $key"], 400);
                    return;
                }
                $seenNodeKeys[$key] = true;
            }
        }
        if (!empty($input['edges'])) {
            foreach ($input['edges'] as $e) {
                $from = trim((string)($e['from'] ?? ''));
                $to = trim((string)($e['to'] ?? ''));
                if ($from === '' || $to === '') {
                    $this->json_response(['status' => false, 'message' => 'Hay conexiones inválidas (from/to)'], 400);
                    return;
                }
                if (!empty($seenNodeKeys) && (!isset($seenNodeKeys[$from]) || !isset($seenNodeKeys[$to]))) {
                    $this->json_response(['status' => false, 'message' => 'Hay conexiones apuntando a nodos inexistentes'], 400);
                    return;
                }
            }
        }

        $this->db->trans_start();
        $this->db->where('flow_version_id', $version_id)->delete('flow_edges');
        $this->db->where('flow_version_id', $version_id)->delete('flow_nodes');
        if (!empty($input['nodes'])) {
            foreach ($input['nodes'] as $n) {
                $this->db->insert('flow_nodes', [
                    'flow_version_id' => $version_id,
                    'node_key' => trim((string)$n['node_key']),
                    'type' => trim((string)$n['type']),
                    'payload_json' => json_encode($n['payload'] ?? [])
                ]);
            }
        }
        if (!empty($input['edges'])) {
            foreach ($input['edges'] as $e) {
                $this->db->insert('flow_edges', [
                    'flow_version_id' => $version_id,
                    'from_node_key' => trim((string)$e['from']),
                    'to_node_key' => trim((string)$e['to']),
                    'rule_json' => isset($e['rule']) ? json_encode($e['rule']) : null
                ]);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->json_response(['status' => false, 'message' => 'No se pudo guardar'], 500);
            return;
        }
        $this->json_response(['status' => true, 'message' => 'Nodos guardados']);
    }

    public function validate_version($version_id = 0) {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        $this->assert_version_access_or_fail($version_id, $store->sto_id);
        $nodes = $this->db->where('flow_version_id', $version_id)->get('flow_nodes')->result();
        $edges = $this->db->where('flow_version_id', $version_id)->get('flow_edges')->result();
        $errors = [];
        $node_keys = array_map(fn($n) => $n->node_key, $nodes);
        if (!in_array('start', $node_keys)) $errors[] = 'Falta nodo "start"';
        foreach ($edges as $e) {
            if (!in_array($e->from_node_key, $node_keys)) $errors[] = "Edge desde '$e->from_node_key' apunta a nodo inexistente";
            if (!in_array($e->to_node_key, $node_keys)) $errors[] = "Edge hacia '$e->to_node_key' no existe";
        }
        foreach ($nodes as $n) {
            if ($n->type === 'end') continue;
            $has_out = false;
            foreach ($edges as $e) { if ($e->from_node_key === $n->node_key) { $has_out = true; break; } }
            if (!$has_out && $n->type !== 'end') $errors[] = "Nodo '$n->node_key' no tiene salida";
        }
        $this->json_response(['status' => empty($errors), 'errors' => $errors, 'message' => empty($errors) ? 'Flujo válido' : 'Errores encontrados']);
    }

    public function publish($version_id = 0) {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        $this->assert_version_access_or_fail($version_id, $store->sto_id);
        $version = $this->db->where('id', $version_id)->get('flow_versions')->row();
        if (!$version) { $this->json_response(['status' => false, 'message' => 'Versión no encontrada'], 404); return; }
        $nodes = $this->db->where('flow_version_id', $version_id)->get('flow_nodes')->result();
        if (empty($nodes)) { $this->json_response(['status' => false, 'message' => 'No hay nodos que publicar'], 400); return; }
        $this->db->where('flow_id', $version->flow_id)->where('status', 'published')->update('flow_versions', ['status' => 'archived']);
        $this->db->where('id', $version_id)->update('flow_versions', ['status' => 'published', 'published_at' => date('Y-m-d H:i:s')]);
        $this->json_response(['status' => true, 'message' => 'Flujo publicado']);
    }

    public function save_trigger($flow_id = 0) {
        $this->require_auth();
        $store = $this->assert_store_or_fail();
        $flow = $this->db->where('id', (int) $flow_id)->where('tenant_id', $store->sto_id)->get('flows')->row();
        if (!$flow) { $this->json_response(['status' => false, 'message' => 'No autorizado'], 403); return; }
        $val = $this->input->post('trigger_value');
        $this->db->where('flow_id', $flow_id)->delete('flow_triggers');
        if ($val) {
            $this->db->insert('flow_triggers', ['flow_id' => $flow_id, 'trigger_type' => 'keyword', 'trigger_value' => strtolower(trim($val))]);
        }
        $this->json_response(['status' => true, 'message' => 'Trigger guardado']);
    }
}

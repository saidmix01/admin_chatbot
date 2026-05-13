<?php
class FlowBuilder extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
        $this->load->model('Store/Store_model', 'Store_model');
    }

    private function require_auth() {
        if (!$this->session->userdata('login')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => false, 'message' => 'No autorizado']);
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
        $data_footer = ["scripts" => ["js/general.js"]];
        $this->load->view('includes/header', $header);
        $this->load->view('flows/' . $view, $data);
        $this->load->view('includes/footer', $data_footer);
    }

    public function index() {
        $this->require_auth();
        $store = $this->get_store();
        if (!$store) { show_error('Sin tienda'); return; }
        $flows = $this->db->where('tenant_id', $store->sto_id)->order_by('created_at', 'DESC')->get('flows')->result();
        $this->load_admin_view('list', ['flows' => $flows, 'store' => $store]);
    }

    public function create() {
        $this->require_auth();
        $store = $this->get_store();
        if ($this->input->method() === 'post') {
            $name = $this->input->post('name');
            $desc = $this->input->post('description');
            $order = intval($this->input->post('display_order') ?: 0);
            $trigger = $this->input->post('trigger_value');
            if (!$name) { echo json_encode(['status' => false, 'message' => 'Nombre requerido']); return; }
            $this->db->insert('flows', [
                'tenant_id' => $store->sto_id, 'name' => $name,
                'description' => $desc, 'is_active' => 1
            ]);
            $flow_id = $this->db->insert_id();
            if ($trigger) {
                $this->db->insert('flow_triggers', ['flow_id' => $flow_id, 'trigger_type' => 'keyword', 'trigger_value' => strtolower(trim($trigger))]);
            }
            $this->db->insert('flow_versions', ['flow_id' => $flow_id, 'version' => 1, 'status' => 'draft']);
            echo json_encode(['status' => true, 'message' => 'Flujo creado', 'id' => $flow_id]);
            return;
        }
        $this->load_admin_view('create', ['store' => $store]);
    }

    public function edit($id = 0) {
        $this->require_auth();
        $store = $this->get_store();
        $flow = $this->db->where('id', $id)->where('tenant_id', $store->sto_id)->get('flows')->row();
        if (!$flow) { show_error('Flujo no encontrado'); return; }
        $triggers = $this->db->where('flow_id', $id)->get('flow_triggers')->result();
        $version = $this->db->where('flow_id', $id)->where('status', 'draft')->order_by('version', 'DESC')->get('flow_versions')->row();
        if (!$version) {
            $maxv = $this->db->select_max('version')->where('flow_id', $id)->get('flow_versions')->row()->version ?? 0;
            $this->db->insert('flow_versions', ['flow_id' => $id, 'version' => $maxv + 1, 'status' => 'draft']);
            $version = $this->db->where('flow_id', $id)->where('status', 'draft')->get('flow_versions')->row();
        }
        $nodes = $this->db->where('flow_version_id', $version->id)->order_by('id')->get('flow_nodes')->result();
        $edges = $this->db->where('flow_version_id', $version->id)->get('flow_edges')->result();
        $published = $this->db->where('flow_id', $id)->where('status', 'published')->order_by('version', 'DESC')->get('flow_versions')->row();
        $this->load_admin_view('editor', [
            'flow' => $flow, 'triggers' => $triggers, 'version' => $version,
            'nodes' => $nodes, 'edges' => $edges, 'published' => $published, 'store' => $store
        ]);
    }

    public function save_nodes($version_id = 0) {
        $this->require_auth();
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) { echo json_encode(['status' => false, 'message' => 'Datos inválidos']); return; }
        $this->db->trans_start();
        $this->db->where('flow_version_id', $version_id)->delete('flow_nodes');
        $this->db->where('flow_version_id', $version_id)->delete('flow_edges');
        if (!empty($input['nodes'])) {
            foreach ($input['nodes'] as $n) {
                $this->db->insert('flow_nodes', [
                    'flow_version_id' => $version_id,
                    'node_key' => $n['node_key'],
                    'type' => $n['type'],
                    'payload_json' => json_encode($n['payload'] ?? [])
                ]);
            }
        }
        if (!empty($input['edges'])) {
            foreach ($input['edges'] as $e) {
                $this->db->insert('flow_edges', [
                    'flow_version_id' => $version_id,
                    'from_node_key' => $e['from'],
                    'to_node_key' => $e['to'],
                    'rule_json' => isset($e['rule']) ? json_encode($e['rule']) : null
                ]);
            }
        }
        $this->db->trans_complete();
        echo json_encode(['status' => true, 'message' => 'Nodos guardados']);
    }

    public function validate_version($version_id = 0) {
        $this->require_auth();
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
        echo json_encode(['status' => empty($errors), 'errors' => $errors, 'message' => empty($errors) ? 'Flujo válido' : 'Errores encontrados']);
    }

    public function publish($version_id = 0) {
        $this->require_auth();
        $version = $this->db->where('id', $version_id)->get('flow_versions')->row();
        if (!$version) { echo json_encode(['status' => false, 'message' => 'Versión no encontrada']); return; }
        $nodes = $this->db->where('flow_version_id', $version_id)->get('flow_nodes')->result();
        if (empty($nodes)) { echo json_encode(['status' => false, 'message' => 'No hay nodos que publicar']); return; }
        $this->db->where('flow_id', $version->flow_id)->where('status', 'published')->update('flow_versions', ['status' => 'archived']);
        $this->db->where('id', $version_id)->update('flow_versions', ['status' => 'published', 'published_at' => date('Y-m-d H:i:s')]);
        echo json_encode(['status' => true, 'message' => 'Flujo publicado']);
    }

    public function save_trigger($flow_id = 0) {
        $this->require_auth();
        $val = $this->input->post('trigger_value');
        $this->db->where('flow_id', $flow_id)->delete('flow_triggers');
        if ($val) {
            $this->db->insert('flow_triggers', ['flow_id' => $flow_id, 'trigger_type' => 'keyword', 'trigger_value' => strtolower(trim($val))]);
        }
        echo json_encode(['status' => true, 'message' => 'Trigger guardado']);
    }
}

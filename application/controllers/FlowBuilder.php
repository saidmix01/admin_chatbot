<?php
class FlowBuilder extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
        $this->load->model('Store/Store_model', 'Store_model');
    }

    private function json_response($data, $code = 200) {
        $this->output->set_status_header($code);
        $this->output->set_content_type('application/json');
        echo json_encode($data);
        exit;
    }

    private function wants_json() {
        $accept = (string) $this->input->get_request_header('Accept');
        $ct = (string) $this->input->get_request_header('Content-Type');
        return $this->input->is_ajax_request()
            || stripos($accept, 'application/json') !== false
            || stripos($ct, 'application/json') !== false;
    }

    private function require_auth() {
        if (!$this->session->userdata('login')) {
            if ($this->wants_json()) $this->json_response(['status' => false, 'message' => 'No autorizado'], 401);
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
        if (!$store) {
            if ($this->input->method() === 'post') $this->json_response(['status' => false, 'message' => 'Sin tienda'], 400);
            show_error('Sin tienda');
            return;
        }
        if ($this->input->method() === 'post') {
            $name = $this->input->post('name');
            $desc = $this->input->post('description');
            $order = intval($this->input->post('display_order') ?: 0);
            $trigger = $this->input->post('trigger_value');
            $parent_id = (int)($this->input->post('parent_flow_id') ?: 0);
            if (!$name) $this->json_response(['status' => false, 'message' => 'Nombre requerido'], 422);

            if ($parent_id) {
                $parent = $this->db->query(
                    "SELECT id FROM flows WHERE id = ? AND tenant_id = ?",
                    [$parent_id, (int)$store->sto_id]
                )->row();
                if (!$parent) $this->json_response(['status' => false, 'message' => 'Flujo padre no encontrado'], 422);
            }

            $trigger_norm = '';
            if ($trigger) $trigger_norm = strtolower(trim((string)$trigger));
            if ($trigger_norm !== '') {
                $trigger_exists = $this->db->query(
                    "SELECT ft.id
                     FROM flow_triggers ft
                     INNER JOIN flows f ON f.id = ft.flow_id
                     WHERE f.tenant_id = ? AND ft.trigger_type = 'keyword' AND ft.trigger_value = ?
                     LIMIT 1",
                    [(int)$store->sto_id, $trigger_norm]
                )->row();
                if ($trigger_exists) $this->json_response(['status' => false, 'message' => 'El trigger ya está en uso'], 422);
            }

            $this->db->trans_begin();

            $this->db->insert('flows', [
                'tenant_id' => $store->sto_id,
                'name' => $name,
                'description' => $desc,
                'is_active' => 1,
                'parent_flow_id' => $parent_id ?: null,
                'display_order' => $order
            ]);
            $flow_id = (int) $this->db->insert_id();

            if ($trigger_norm !== '') {
                $this->db->insert('flow_triggers', [
                    'flow_id' => $flow_id,
                    'trigger_type' => 'keyword',
                    'trigger_value' => $trigger_norm
                ]);
            }

            $this->db->insert('flow_versions', ['flow_id' => $flow_id, 'version' => 1, 'status' => 'draft']);

            if ($this->db->trans_status() === false) {
                $err = $this->db->error();
                $this->db->trans_rollback();
                log_message('error', 'FlowBuilder create failed us_id='.(int)$this->session->userdata('us_id').' tenant_id='.(int)$store->sto_id.' db_error='.$err['message']);
                $this->json_response(['status' => false, 'message' => 'Error creando el flujo'], 500);
            }

            $this->db->trans_commit();
            $this->json_response(['status' => true, 'message' => 'Flujo creado', 'id' => $flow_id]);
        }
        $this->load_admin_view('create', ['store' => $store]);
    }

    public function edit($id = 0) {
        $this->require_auth();
        $store = $this->get_store();
        if (!$store) { show_error('Sin tienda'); return; }
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
        $store = $this->get_store();
        if (!$store) $this->json_response(['status' => false, 'message' => 'Sin tienda'], 400);

        $version_id = (int) $version_id;
        if (!$version_id) $this->json_response(['status' => false, 'message' => 'version_id requerido'], 422);

        $version = $this->db->query(
            "SELECT fv.id, fv.flow_id
             FROM flow_versions fv
             INNER JOIN flows f ON f.id = fv.flow_id
             WHERE fv.id = ? AND f.tenant_id = ?",
            [$version_id, (int) $store->sto_id]
        )->row();
        if (!$version) $this->json_response(['status' => false, 'message' => 'Versión no encontrada'], 404);

        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            $this->json_response(['status' => false, 'message' => 'Body vacío'], 400);
        }

        $input = json_decode($raw, true);
        if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->json_response(['status' => false, 'message' => 'JSON inválido'], 400);
        }

        $nodes_in = $input['nodes'] ?? null;
        $edges_in = $input['edges'] ?? null;

        $errors = [];
        if (!is_array($nodes_in)) $errors[] = 'nodes debe ser un arreglo';
        if ($edges_in !== null && !is_array($edges_in)) $errors[] = 'edges debe ser un arreglo';

        $node_keys = [];
        $nodes_rows = [];

        if (is_array($nodes_in)) {
            foreach ($nodes_in as $idx => $n) {
                if (!is_array($n)) { $errors[] = "Nodo #$idx inválido"; continue; }
                $key = trim((string)($n['node_key'] ?? ''));
                $type = trim((string)($n['type'] ?? ''));
                if ($key === '') { $errors[] = "Nodo #$idx: node_key requerido"; continue; }
                if ($type === '') { $errors[] = "Nodo #$idx: type requerido"; continue; }
                if (isset($node_keys[$key])) { $errors[] = "node_key duplicado: $key"; continue; }
                $node_keys[$key] = true;

                $payload = $n['payload'] ?? [];
                $payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($payload_json === false) { $errors[] = "Nodo $key: payload no serializable"; continue; }

                $nodes_rows[] = [
                    'flow_version_id' => $version_id,
                    'node_key' => $key,
                    'type' => $type,
                    'payload_json' => $payload_json
                ];
            }
        }

        $edges_rows = [];
        if (is_array($edges_in)) {
            foreach ($edges_in as $idx => $e) {
                if (!is_array($e)) { $errors[] = "Edge #$idx inválido"; continue; }
                $from = trim((string)($e['from'] ?? ''));
                $to = trim((string)($e['to'] ?? ''));
                if ($from === '' || $to === '') { $errors[] = "Edge #$idx: from/to requeridos"; continue; }
                if (!isset($node_keys[$from])) $errors[] = "Edge #$idx: from '$from' no existe";
                if (!isset($node_keys[$to])) $errors[] = "Edge #$idx: to '$to' no existe";

                $rule_json = null;
                if (array_key_exists('rule', $e) && $e['rule'] !== null) {
                    $rule_json = json_encode($e['rule'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    if ($rule_json === false) { $errors[] = "Edge #$idx: rule no serializable"; continue; }
                }

                $edges_rows[] = [
                    'flow_version_id' => $version_id,
                    'from_node_key' => $from,
                    'to_node_key' => $to,
                    'rule_json' => $rule_json
                ];
            }
        }

        if (!empty($errors)) {
            $this->json_response(['status' => false, 'message' => 'Validación fallida', 'errors' => $errors], 422);
        }

        $this->db->trans_begin();

        $this->db->where('flow_version_id', $version_id)->delete('flow_edges');
        $this->db->where('flow_version_id', $version_id)->delete('flow_nodes');

        if (!empty($nodes_rows)) $this->db->insert_batch('flow_nodes', $nodes_rows);
        if (!empty($edges_rows)) $this->db->insert_batch('flow_edges', $edges_rows);

        if ($this->db->trans_status() === false) {
            $err = $this->db->error();
            $this->db->trans_rollback();
            log_message('error', 'FlowBuilder save_nodes failed us_id='.(int)$this->session->userdata('us_id').' tenant_id='.(int)$store->sto_id.' version_id='.(int)$version_id.' db_error='.$err['message']);
            $this->json_response(['status' => false, 'message' => 'Error guardando nodos'], 500);
        }

        $this->db->trans_commit();
        $this->json_response(['status' => true, 'message' => 'Nodos guardados', 'data' => ['nodes' => count($nodes_rows), 'edges' => count($edges_rows)]]);
    }
    public function validate_version($version_id = 0) {
        $this->require_auth();
        $store = $this->get_store();
        if (!$store) $this->json_response(['status' => false, 'message' => 'Sin tienda'], 400);
        $version_id = (int) $version_id;
        $ok = $this->db->query(
            "SELECT fv.id
             FROM flow_versions fv
             INNER JOIN flows f ON f.id = fv.flow_id
             WHERE fv.id = ? AND f.tenant_id = ?",
            [$version_id, (int) $store->sto_id]
        )->row();
        if (!$ok) $this->json_response(['status' => false, 'message' => 'Versión no encontrada'], 404);

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
        $store = $this->get_store();
        if (!$store) $this->json_response(['status' => false, 'message' => 'Sin tienda'], 400);
        $version_id = (int) $version_id;
        $version = $this->db->query(
            "SELECT fv.id, fv.flow_id
             FROM flow_versions fv
             INNER JOIN flows f ON f.id = fv.flow_id
             WHERE fv.id = ? AND f.tenant_id = ?",
            [$version_id, (int) $store->sto_id]
        )->row();
        if (!$version) $this->json_response(['status' => false, 'message' => 'Versión no encontrada'], 404);
        $nodes = $this->db->where('flow_version_id', $version_id)->get('flow_nodes')->result();
        if (empty($nodes)) $this->json_response(['status' => false, 'message' => 'No hay nodos que publicar'], 422);

        $this->db->trans_begin();
        $this->db->where('flow_id', (int)$version->flow_id)->where('status', 'published')->update('flow_versions', ['status' => 'archived']);
        $this->db->where('id', $version_id)->update('flow_versions', ['status' => 'published', 'published_at' => date('Y-m-d H:i:s')]);

        if ($this->db->trans_status() === false) {
            $err = $this->db->error();
            $this->db->trans_rollback();
            log_message('error', 'FlowBuilder publish failed us_id='.(int)$this->session->userdata('us_id').' tenant_id='.(int)$store->sto_id.' version_id='.(int)$version_id.' db_error='.$err['message']);
            $this->json_response(['status' => false, 'message' => 'Error publicando el flujo'], 500);
        }

        $this->db->trans_commit();
        $this->json_response(['status' => true, 'message' => 'Flujo publicado']);
    }

    public function save_trigger($flow_id = 0) {
        $this->require_auth();
        $store = $this->get_store();
        if (!$store) $this->json_response(['status' => false, 'message' => 'Sin tienda'], 400);
        $flow_id = (int) $flow_id;
        if (!$flow_id) $this->json_response(['status' => false, 'message' => 'flow_id requerido'], 422);

        $flow = $this->db->query(
            "SELECT id FROM flows WHERE id = ? AND tenant_id = ?",
            [$flow_id, (int) $store->sto_id]
        )->row();
        if (!$flow) $this->json_response(['status' => false, 'message' => 'Flujo no encontrado'], 404);

        $val = $this->input->post('trigger_value');
        $this->db->trans_begin();
        $this->db->where('flow_id', $flow_id)->delete('flow_triggers');
        if ($val) {
            $this->db->insert('flow_triggers', [
                'flow_id' => $flow_id,
                'trigger_type' => 'keyword',
                'trigger_value' => strtolower(trim($val))
            ]);
        }

        if ($this->db->trans_status() === false) {
            $err = $this->db->error();
            $this->db->trans_rollback();
            log_message('error', 'FlowBuilder save_trigger failed us_id='.(int)$this->session->userdata('us_id').' tenant_id='.(int)$store->sto_id.' flow_id='.(int)$flow_id.' db_error='.$err['message']);
            $this->json_response(['status' => false, 'message' => 'Error guardando trigger'], 500);
        }

        $this->db->trans_commit();
        $this->json_response(['status' => true, 'message' => 'Trigger guardado']);
    }

    /**
     * API: Get products & services catalog for the current user
     */
    public function api_catalog()
    {
        $this->require_auth();
        $us_id = $this->session->userdata('us_id');
        
        $products = $this->db->query(
            "SELECT s.ser_id, s.ser_name, s.ser_price, s.ser_description, s.ser_type, s.ser_imagen
             FROM services s
             INNER JOIN service_user su ON su.ser_id = s.ser_id
             WHERE su.us_id = ? AND s.ser_status = 1 AND s.ser_type = 'producto'
             ORDER BY s.ser_name",
            [(int)$us_id]
        )->result();
        
        $services = $this->db->query(
            "SELECT s.ser_id, s.ser_name, s.ser_price, s.ser_description, s.ser_type, s.ser_imagen
             FROM services s
             INNER JOIN service_user su ON su.ser_id = s.ser_id
             WHERE su.us_id = ? AND s.ser_status = 1 AND s.ser_type = 'servicio'
             ORDER BY s.ser_name",
            [(int)$us_id]
        )->result();
        
        $this->output->set_content_type('application/json');
        echo json_encode([
            'status' => true,
            'data' => [
                'products' => $products,
                'services' => $services
            ]
        ]);
    }


    /**
     * API: Get all flows with hierarchy info
     */
    public function api_flow_list()
    {
        $this->require_auth();
        $store = $this->get_store();
        if (!$store) {
            $this->json_response(["status" => false, "data" => []], 400);
        }

        $flows = $this->db->query(
            "SELECT f.id, f.name, f.description, f.parent_flow_id, f.is_active, f.display_order,
                    (SELECT COUNT(*) FROM flow_versions fv WHERE fv.flow_id = f.id AND fv.status = 'published') as published_version
             FROM flows f
             WHERE f.tenant_id = ?
             ORDER BY CASE WHEN f.parent_flow_id IS NULL THEN 0 ELSE 1 END ASC, f.parent_flow_id ASC, f.display_order ASC, f.name ASC",
            [(int)$store->sto_id]
        )->result();

        $this->json_response(["status" => true, "data" => $flows]);
    }

    /**
     * API: Set parent flow
     * POST: { flow_id: X, parent_flow_id: Y }
     */
    public function api_set_parent()
    {
        $this->require_auth();
        $store = $this->get_store();
        if (!$store) {
            $this->json_response(["status" => false, "message" => "No store"], 400);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $flow_id = (int)($input['flow_id'] ?? 0);
        $parent_id = (int)($input['parent_flow_id'] ?? 0);

        if (!$flow_id) {
            $this->json_response(["status" => false, "message" => "flow_id requerido"], 422);
        }

        $flow = $this->db->query(
            "SELECT id FROM flows WHERE id = ? AND tenant_id = ?",
            [$flow_id, (int)$store->sto_id]
        )->row();
        if (!$flow) {
            $this->json_response(["status" => false, "message" => "Flujo no encontrado"], 404);
        }

        if ($parent_id) {
            $parent = $this->db->query(
                "SELECT id FROM flows WHERE id = ? AND tenant_id = ?",
                [$parent_id, (int)$store->sto_id]
            )->row();
            if (!$parent) {
                $this->json_response(["status" => false, "message" => "Flujo padre no encontrado"], 404);
            }
            if ($parent_id === $flow_id) {
                $this->json_response(["status" => false, "message" => "Un flujo no puede ser padre de si mismo"], 422);
            }
        }

        $this->db->where("id", $flow_id)->update("flows", [
            "parent_flow_id" => $parent_id ?: null,
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        if ($this->db->affected_rows() === 0 && $this->db->error()['code']) {
            $err = $this->db->error();
            log_message('error', 'FlowBuilder api_set_parent failed us_id='.(int)$this->session->userdata('us_id').' tenant_id='.(int)$store->sto_id.' flow_id='.(int)$flow_id.' db_error='.$err['message']);
            $this->json_response(["status" => false, "message" => "Error actualizando flujo"], 500);
        }

        $this->json_response(["status" => true, "message" => "Flujo actualizado"]);
    }

}

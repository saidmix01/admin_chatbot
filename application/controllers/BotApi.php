<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BotApi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('general_helper');
        header('Content-Type: application/json; charset=UTF-8');
    }

    private function json($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    private function input()
    {
        if ($this->input->method() === 'post') {
            $raw = file_get_contents('php://input');
            return json_decode($raw, true) ?: [];
        }
        return [];
    }

    private function store_val($store, $prop, $default = null)
    {
        if (!$store) return $default;
        if (!property_exists($store, $prop)) return $default;
        if ($store->$prop === null) return $default;
        return $store->$prop;
    }

    private function normalize_choice_payload($payload)
    {
        $p = is_array($payload) ? $payload : [];

        if (isset($p['options_source']) && is_array($p['options_source'])) {
            return $p;
        }

        if (!empty($p['catalog_source'])) {
            $itemType = $p['catalog_source'] === 'servicio' ? 'service' : 'product';
            return [
                'text' => $p['text'] ?? '',
                'options_source' => [
                    'kind' => 'catalog',
                    'item_type' => $itemType,
                    'limit' => 30,
                    'only_active' => true,
                    'save_items_to' => 'catalog_shown',
                    'label_template' => '{{name}} — {{price}}'
                ],
                'save_to' => 'selected_option',
                'save_selected_id_to' => 'producto_id',
                'save_selected_name_to' => 'producto_nombre',
                'save_selected_price_to' => 'producto_precio',
                'retry_text' => 'Opción inválida. Responde con un número de la lista.'
            ];
        }

        if (!empty($p['options']) && is_array($p['options'])) {
            $normalized = [];
            foreach ($p['options'] as $o) {
                if (!is_array($o)) continue;
                $label = isset($o['label']) ? (string)$o['label'] : (isset($o['text']) ? (string)$o['text'] : '');
                $value = $o['value'] ?? ($o['id'] ?? '');
                if (is_string($value) && trim($value) !== '' && is_numeric($value)) $value = (int)$value;
                $normalized[] = ['label' => $label, 'value' => $value];
            }
            $p['options'] = $normalized;
        }

        return $p;
    }

    private function normalize_condition_payload($payload)
    {
        $p = is_array($payload) ? $payload : [];
        $cond = (isset($p['if']) && is_array($p['if'])) ? $p['if'] : [];
        $value = $cond['value'] ?? null;
        if (is_string($value) && trim($value) !== '' && is_numeric($value)) $value = (int)$value;
        return [
            'if' => [
                'op' => $cond['op'] ?? 'equals',
                'var' => $cond['var'] ?? '',
                'value' => $value
            ],
            'true_to' => $p['true_to'] ?? null,
            'false_to' => $p['false_to'] ?? null
        ];
    }

    private function normalize_node_payload($type, $payload)
    {
        if ($type === 'choice') return $this->normalize_choice_payload($payload);
        if ($type === 'condition') return $this->normalize_condition_payload($payload);
        return is_array($payload) ? $payload : [];
    }

    private function get_store_for_user($us_id)
    {
        $store = $this->db->where('us_id', (int)$us_id)->get('stores')->row();
        if ($store) return $store;

        $store = $this->db->query(
            "SELECT s.*
             FROM user_store us
             INNER JOIN stores s ON s.sto_id = us.sto_id
             WHERE us.us_id = ?
             ORDER BY us.created_at DESC
             LIMIT 1",
            [(int)$us_id]
        )->row();

        return $store;
    }

    private function get_effective_bot_status($session)
    {
        if (!$session) return 'disconnected';
        $status = $session->bs_status ?? 'disconnected';
        $last = $session->bs_last_activity ?? null;
        if (!$last && property_exists($session, 'updated_at')) $last = $session->updated_at;
        if ($status === 'connected' && $last) {
            $ts = strtotime($last);
            if ($ts !== false && (time() - $ts) > 180) return 'disconnected';
        }
        return $status;
    }

    private function require_cron_key()
    {
        $expected = getenv('BOT_CRON_KEY');
        if (!$expected) return;

        $key = $this->input->get('key');
        if (!$key || !hash_equals($expected, (string)$key)) {
            $this->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }
    }

    private function enqueue_notification($us_id, $type, $payload = [])
    {
        $json = json_encode(is_array($payload) ? $payload : [], JSON_UNESCAPED_UNICODE);
        $this->db->set('us_id', (int)$us_id);
        $this->db->set('bn_type', (string)$type);
        $this->db->set('bn_status', 'pending');
        $this->db->set('bn_payload', $this->db->escape($json) . '::jsonb', false);
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('bot_notifications');
    }

    private function absolute_url($path)
    {
        if (!$path) return null;
        $p = (string)$path;
        if (strpos($p, 'data:') === 0) return $p;
        if (preg_match('~^https?://~', $p)) return $p;
        if (strpos($p, '/') === 0) $p = ltrim($p, '/');
        return base_url($p);
    }

    private function plan_info($store)
    {
        $start = $this->store_val($store, 'sto_plan_start', null);
        $end = $this->store_val($store, 'sto_plan_end', null);
        $daysLeft = null;
        $expiresSoon = false;
        if ($end) {
            $now = new DateTime('today');
            $endDt = new DateTime($end);
            $diff = (int)$now->diff($endDt)->format('%r%a');
            $daysLeft = $diff;
            $expiresSoon = $diff <= 7;
        }
        return [
            'plan_start' => $start,
            'plan_end' => $end,
            'plan_days_left' => $daysLeft,
            'plan_expires_soon' => $expiresSoon
        ];
    }

    private function check_session()
    {
        if (!$this->session->userdata('login')) {
            $this->json(['status' => false, 'message' => 'No autorizado'], 401);
        }
    }

    public function login()
    {
        $data = $this->input();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        if (!$email || !$password) $this->json(['status' => false, 'message' => 'email y password requeridos'], 400);

        $user = $this->db->where('us_email', $email)->get('users')->row();
        if (!$user) $this->json(['status' => false, 'message' => 'Credenciales inválidas'], 401);
        if (!password_verify($password, $user->us_password)) $this->json(['status' => false, 'message' => 'Credenciales inválidas'], 401);

        $store = $this->get_store_for_user($user->us_id);

        $this->json(['status' => true, 'data' => [
            'us_id' => (int)$user->us_id,
            'us_name' => $user->us_name,
            'us_email' => $user->us_email,
            'store' => $store ? [
                'sto_id' => (int)$store->sto_id,
                'sto_name' => $store->sto_name ?? '',
                'sto_phone' => $store->sto_phone ?? '',
                'sto_wellcome_message' => $store->sto_wellcome_message ?? '',
                'sto_inactivity_minutes' => (int)($store->sto_inactivity_minutes ?? 15),
                'sto_inactivity_message' => $store->sto_inactivity_message ?? ''
            ] : null
        ]]);
    }

    public function update_qr()
    {
        $data = $this->input();
        $us_id = $data['us_id'] ?? 0;
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $qr_base64 = $data['qr_base64'] ?? '';
        $exists = $this->db->where('us_id', (int)$us_id)->get('bot_sessions')->row();

        if ($exists) {
            $this->db->where('us_id', (int)$us_id)->update('bot_sessions', [
                'bs_qr_base64' => $qr_base64,
                'bs_status' => 'waiting_scan',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db->insert('bot_sessions', [
                'us_id' => (int)$us_id,
                'bs_qr_base64' => $qr_base64,
                'bs_status' => 'waiting_scan'
            ]);
        }
        $this->json(['status' => true, 'message' => 'QR actualizado']);
    }

    public function update_status()
    {
        $data = $this->input();
        $us_id = $data['us_id'] ?? 0;
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $status = mb_strtolower(trim((string)($data['status'] ?? 'disconnected')), 'UTF-8');
        $allowed = ['connected', 'disconnected', 'waiting_scan', 'expired', 'reconnecting'];
        if (!in_array($status, $allowed, true)) $status = 'disconnected';
        $exists = $this->db->where('us_id', (int)$us_id)->get('bot_sessions')->row();
        $prev_effective = mb_strtolower(trim((string)$this->get_effective_bot_status($exists)), 'UTF-8');

        $fields = [
            'bs_status' => $status,
            'bs_whatsapp_number' => $data['whatsapp_number'] ?? '',
            'bs_last_activity' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($status === 'connected') $fields['bs_qr_base64'] = '';

        if ($exists) {
            $this->db->where('us_id', (int)$us_id)->update('bot_sessions', $fields);
        } else {
            $fields['us_id'] = (int)$us_id;
            $this->db->insert('bot_sessions', $fields);
        }

        if ($prev_effective !== $status) {
            if ($status === 'connected') {
                $this->enqueue_notification($us_id, 'bot_connected', [
                    'previous_status' => $prev_effective,
                    'new_status' => $status,
                    'whatsapp_number' => $fields['bs_whatsapp_number'] ?? '',
                    'source' => 'update_status'
                ]);
            } elseif ($prev_effective === 'connected' && $status !== 'connected') {
                $this->enqueue_notification($us_id, 'bot_disconnected', [
                    'previous_status' => $prev_effective,
                    'new_status' => $status,
                    'whatsapp_number' => $fields['bs_whatsapp_number'] ?? '',
                    'source' => 'update_status'
                ]);
            }
        }

        $this->json(['status' => true, 'message' => 'Estado actualizado']);
    }

    public function cron_check_sessions()
    {
        $this->require_cron_key();

        $sessions = $this->db->get('bot_sessions')->result();
        $checked = 0;
        $updated = 0;
        $notified = 0;

        foreach ($sessions as $s) {
            $checked++;
            $effective = $this->get_effective_bot_status($s);
            $stored = $s->bs_status ?? 'disconnected';

            if ($stored === 'connected' && $effective === 'disconnected') {
                $this->db->where('us_id', (int)$s->us_id)->update('bot_sessions', [
                    'bs_status' => 'disconnected',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $updated++;
                $this->enqueue_notification((int)$s->us_id, 'bot_disconnected', [
                    'previous_status' => 'connected',
                    'new_status' => 'disconnected',
                    'whatsapp_number' => $s->bs_whatsapp_number ?? '',
                    'reason' => 'timeout',
                    'source' => 'cron_check_sessions'
                ]);
                $notified++;
            }
        }

        $this->json([
            'status' => true,
            'data' => [
                'checked' => $checked,
                'updated' => $updated,
                'notified' => $notified
            ]
        ]);
    }

    public function pending_notifications($us_id = 0)
    {
        $this->require_cron_key();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $rows = $this->db
            ->where('us_id', (int)$us_id)
            ->where('bn_status', 'pending')
            ->order_by('created_at', 'ASC')
            ->limit(50)
            ->get('bot_notifications')->result();

        $this->json(['status' => true, 'data' => $rows]);
    }

    public function mark_notification_processed()
    {
        $this->require_cron_key();
        $data = $this->input();
        $bn_id = (int)($data['bn_id'] ?? 0);
        if (!$bn_id) $this->json(['status' => false, 'message' => 'bn_id requerido'], 400);

        $this->db->where('bn_id', $bn_id)->update('bot_notifications', [
            'bn_status' => 'processed',
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        $this->json(['status' => true, 'message' => 'OK']);
    }

    public function new_order()
    {
        $data = $this->input();
        $us_id = $data['us_id'] ?? 0;
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $this->db->insert('bot_orders', [
            'us_id' => (int)$us_id,
            'bo_customer_name' => $data['customer_name'] ?? '',
            'bo_customer_phone' => $data['customer_phone'] ?? '',
            'bo_product_name' => $data['product_name'] ?? '',
            'bo_quantity' => $data['quantity'] ?? 1,
            'bo_message' => $data['message'] ?? '',
            'bo_status' => 'nuevo'
        ]);
        $this->json(['status' => true, 'message' => 'Pedido registrado']);
    }

    public function log_message()
    {
        $data = $this->input();
        $us_id = $data['us_id'] ?? 0;
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $this->db->insert('bot_messages', [
            'us_id' => (int)$us_id,
            'bm_from' => $data['from'] ?? 'customer',
            'bm_customer_phone' => $data['customer_phone'] ?? '',
            'bm_customer_name' => $data['customer_name'] ?? '',
            'bm_message' => $data['message'] ?? ''
        ]);
        $this->json(['status' => true, 'message' => 'Mensaje registrado']);
    }

    public function update_order_status()
    {
        $data = $this->input();
        $bo_id = $data['bo_id'] ?? 0;
        if (!$bo_id) $this->json(['status' => false, 'message' => 'bo_id requerido'], 400);

        $this->db->where('bo_id', (int)$bo_id)->update('bot_orders', [
            'bo_status' => $data['status'] ?? 'completado'
        ]);
        $this->json(['status' => true, 'message' => 'Estado del pedido actualizado']);
    }

    public function get_bot_config($us_id = 0)
    {
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $store = $this->get_store_for_user($us_id);
        $session = $this->db->where('us_id', (int)$us_id)->get('bot_sessions')->row();
        $effective_status = $this->get_effective_bot_status($session);
        $starters = json_decode($this->store_val($store, 'sto_starters', '[]'), true) ?: [];

        $welcome = $this->store_val($store, 'sto_wellcome_message');
        $menu = $this->store_val($store, 'sto_menu_message');
        $offhours = $this->store_val($store, 'sto_offhours_message');
        $goodbye = $this->store_val($store, 'sto_goodbye_message');

        $this->json(['status' => true, 'data' => [
            'business_name' => $this->store_val($store, 'sto_name', ''),
            'welcome_msg' => $welcome ?: '¡Hola! Bienvenido a {business}. ¿En qué podemos ayudarte?',
            'menu_msg' => $menu ?: "Elige una opción:\n1. Ver productos\n2. Horario\n3. Ubicación\n4. Hablar con un asesor",
            'offhours_msg' => $offhours ?: 'Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.',
            'goodbye_msg' => $goodbye ?: '¡Gracias por contactarnos! Que tengas un excelente día.',
            'inactivity_minutes' => (int)$this->store_val($store, 'sto_inactivity_minutes', 15),
            'inactivity_msg' => ($this->store_val($store, 'sto_inactivity_message') ?: 'Cerraremos la sesión por inactividad, hasta luego.'),
            'schedule_enabled' => (int)$this->store_val($store, 'sto_schedule_enabled', 0),
            'schedule_open' => $this->store_val($store, 'sto_schedule_open', '09:00') ?: '09:00',
            'schedule_close' => $this->store_val($store, 'sto_schedule_close', '18:00') ?: '18:00',
            'schedule_days' => $this->store_val($store, 'sto_schedule_days', '1,2,3,4,5') ?: '1,2,3,4,5',
            'timezone' => $this->store_val($store, 'sto_timezone', 'America/Bogota') ?: 'America/Bogota',
            'whatsapp_number' => $session->bs_whatsapp_number ?? '',
            'bot_status' => $effective_status,
            'starters' => $starters
        ]]);
    }

    public function get_business($us_id = 0)
    {
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $store = $this->get_store_for_user($us_id);
        if (!$store) $this->json(['status' => false, 'message' => 'Negocio no encontrado'], 404);

        $starters = json_decode($this->store_val($store, 'sto_starters', '[]'), true) ?: [];

        $data = [
            'sto_id' => (int)$this->store_val($store, 'sto_id', 0),
            'sto_name' => $this->store_val($store, 'sto_name', ''),
            'sto_email' => $this->store_val($store, 'sto_email', ''),
            'sto_phone' => $this->store_val($store, 'sto_phone', ''),
            'sto_direction' => $this->store_val($store, 'sto_direction', ''),
            'sto_wellcome_message' => $this->store_val($store, 'sto_wellcome_message', ''),
            'sto_description' => $this->store_val($store, 'sto_description', ''),
            'sto_slug' => $this->store_val($store, 'sto_slug', ''),
            'sto_profile_image' => $this->absolute_url($this->store_val($store, 'sto_profile_image', '')),
            'sto_cover_image' => $this->absolute_url($this->store_val($store, 'sto_cover_image', '')),
            'preview_url' => $this->store_val($store, 'sto_slug', '') ? base_url('preview/store/' . $this->store_val($store, 'sto_slug', '')) : base_url('preview/' . (int)$this->store_val($store, 'sto_id', 0)),
            'subscription' => $this->plan_info($store),
            'starters' => $starters,
            'bot' => [
                'welcome_msg' => ($this->store_val($store, 'sto_wellcome_message') ?: '¡Hola! Bienvenido a {business}. ¿En qué podemos ayudarte?'),
                'menu_msg' => ($this->store_val($store, 'sto_menu_message') ?: "Elige una opción:\n1. Ver productos\n2. Horario\n3. Ubicación\n4. Hablar con un asesor"),
                'offhours_msg' => ($this->store_val($store, 'sto_offhours_message') ?: 'Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.'),
                'goodbye_msg' => ($this->store_val($store, 'sto_goodbye_message') ?: '¡Gracias por contactarnos! Que tengas un excelente día.'),
                'inactivity_minutes' => (int)$this->store_val($store, 'sto_inactivity_minutes', 15),
                'inactivity_msg' => ($this->store_val($store, 'sto_inactivity_message') ?: 'Cerraremos la sesión por inactividad, hasta luego.'),
                'schedule_enabled' => (int)$this->store_val($store, 'sto_schedule_enabled', 0),
                'schedule_open' => $this->store_val($store, 'sto_schedule_open', '09:00') ?: '09:00',
                'schedule_close' => $this->store_val($store, 'sto_schedule_close', '18:00') ?: '18:00',
                'schedule_days' => $this->store_val($store, 'sto_schedule_days', '1,2,3,4,5') ?: '1,2,3,4,5',
                'timezone' => $this->store_val($store, 'sto_timezone', 'America/Bogota') ?: 'America/Bogota'
            ]
        ];

        $this->json(['status' => true, 'data' => $data]);
    }

    public function get_products($us_id = 0)
    {
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $products = $this->db
            ->select('s.*')
            ->from('services s')
            ->join('service_user su', 's.ser_id = su.ser_id')
            ->where('su.us_id', (int)$us_id)
            ->where('s.ser_status', 1)
            ->get()->result();

        $this->json(['status' => true, 'data' => $products]);
    }

    public function get_qr($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $session = $this->db->where('us_id', (int)$us_id)->get('bot_sessions')->row();
        $effective_status = $this->get_effective_bot_status($session);
        $this->json([
            'status' => (bool)$session,
            'qr' => $session->bs_qr_base64 ?? null,
            'status_text' => $effective_status ?: 'no_session'
        ]);
    }

    public function get_status($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $session = $this->db->where('us_id', (int)$us_id)->get('bot_sessions')->row();
        if (!$session) $this->json(['status' => true, 'data' => (object)[]]);
        $session->bs_status = $this->get_effective_bot_status($session);
        $this->json(['status' => true, 'data' => $session]);
    }

    public function refresh_qr($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $this->db->where('us_id', (int)$us_id)->update('bot_sessions', [
            'bs_qr_base64' => '',
            'bs_status' => 'waiting_scan',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $this->json(['status' => true, 'message' => 'QR reset. El bot generará uno nuevo.']);
    }

    public function get_orders($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $orders = $this->db
            ->where('us_id', (int)$us_id)
            ->order_by('created_at', 'DESC')
            ->limit(50)
            ->get('bot_orders')->result();

        $this->json(['status' => true, 'data' => $orders]);
    }

    public function get_messages($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $messages = $this->db
            ->where('us_id', (int)$us_id)
            ->order_by('created_at', 'DESC')
            ->limit(50)
            ->get('bot_messages')->result();

        $this->json(['status' => true, 'data' => $messages]);
    }

    public function get_initial_menu($us_id = 0)
    {
        if (!$us_id) $this->json(["status" => false, "message" => "us_id requerido"], 400);
        $store = $this->get_store_for_user($us_id);
        if (!$store) $this->json(["status" => false, "data" => []]);

        $flows = $this->db->where("tenant_id", (int)$store->sto_id)->where("is_active", 1)->order_by("display_order", "ASC")->get("flows")->result();
        $result = [];
        foreach ($flows as $f) {
            $triggers = $this->db->where("flow_id", $f->id)->get("flow_triggers")->result();
            $result[] = [
                "id" => (int)$f->id,
                "name" => $f->name,
                "description" => $f->description,
                "trigger" => !empty($triggers) ? $triggers[0]->trigger_value : "",
                "display_order" => $f->display_order
            ];
        }
        $this->json(["status" => true, "data" => $result]);
    }

    public function get_flow($us_id = 0)
    {
        if (!$us_id) $this->json(["status" => false, "message" => "us_id requerido"], 400);

        $store = $this->db->query("SELECT sto_id, sto_name FROM stores WHERE us_id = " . intval($us_id))->row();
        if (!$store) $this->json(["status" => false, "message" => "Tienda no encontrada"], 404);

        $flows = $this->db->query("
            SELECT f.id, f.name, f.description, f.is_active,
                   fv.id as version_id, fv.version, fv.published_at
            FROM flows f
            JOIN flow_versions fv ON fv.flow_id = f.id
            WHERE f.tenant_id = " . intval($store->sto_id) . "
              AND f.is_active = 1
              AND fv.status = 'published'
            ORDER BY fv.published_at DESC
        ")->result();

        $result = [];
        foreach ($flows as $f) {
            $triggers = $this->db->query(
                "SELECT trigger_type, trigger_value FROM flow_triggers WHERE flow_id = ?",
                [$f->id]
            )->result();

            $nodes = $this->db->query(
                "SELECT node_key, type, payload_json FROM flow_nodes WHERE flow_version_id = ? ORDER BY id",
                [$f->version_id]
            )->result();

            $edges = $this->db->query(
                "SELECT from_node_key, to_node_key, rule_json FROM flow_edges WHERE flow_version_id = ? ORDER BY id",
                [$f->version_id]
            )->result();

            $result[] = [
                "id" => (int)$f->id,
                "name" => $f->name,
                "description" => $f->description,
                "version" => (int)$f->version,
                "published_at" => $f->published_at,
                "triggers" => array_map(function($t) {
                    return ["type" => $t->trigger_type, "value" => $t->trigger_value];
                }, $triggers),
                "nodes" => array_map(function($n) {
                    return [
                        "key" => $n->node_key,
                        "type" => $n->type,
                        "payload" => json_decode($n->payload_json, true) ?: new stdClass()
                    ];
                }, $nodes),
                "edges" => array_map(function($e) {
                    return [
                        "from" => $e->from_node_key,
                        "to" => $e->to_node_key,
                        "rule" => $e->rule_json ? json_decode($e->rule_json, true) : null
                    ];
                }, $edges)
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode([
            "status" => true,
            "store" => $store->sto_name,
            "data" => $result
        ]);
    }

    public function get_flow_v2($us_id = 0)
    {
        if (!$us_id) $this->json(["status" => false, "message" => "us_id requerido"], 400);

        $store = $this->get_store_for_user($us_id);
        if (!$store) $this->json(["status" => false, "message" => "Tienda no encontrada"], 404);

        $session = $this->db->where('us_id', (int)$us_id)->get('bot_sessions')->row();
        $effective_status = $this->get_effective_bot_status($session);
        $starters = json_decode($this->store_val($store, 'sto_starters', '[]'), true) ?: [];

        $flows = $this->db->query(
            "SELECT f.id, f.name, f.description
             FROM flows f
             WHERE f.tenant_id = ? AND f.is_active = 1
             ORDER BY f.display_order ASC, f.id ASC",
            [(int)$store->sto_id]
        )->result();

        $result = [];
        foreach ($flows as $f) {
            $version = $this->db->query(
                "SELECT id, version, published_at
                 FROM flow_versions
                 WHERE flow_id = ? AND status = 'published'
                 ORDER BY published_at DESC NULLS LAST, version DESC
                 LIMIT 1",
                [(int)$f->id]
            )->row();
            if (!$version) {
                $version = $this->db->query(
                    "SELECT id, version, published_at
                     FROM flow_versions
                     WHERE flow_id = ? AND status = 'draft'
                     ORDER BY version DESC
                     LIMIT 1",
                    [(int)$f->id]
                )->row();
            }
            if (!$version) continue;

            $triggers = $this->db->query(
                "SELECT trigger_type, trigger_value FROM flow_triggers WHERE flow_id = ?",
                [(int)$f->id]
            )->result();

            $nodes = $this->db->query(
                "SELECT node_key, type, payload_json FROM flow_nodes WHERE flow_version_id = ? ORDER BY id",
                [(int)$version->id]
            )->result();

            $edges = $this->db->query(
                "SELECT from_node_key, to_node_key, rule_json FROM flow_edges WHERE flow_version_id = ? ORDER BY id",
                [(int)$version->id]
            )->result();

            $result[] = [
                "id" => (int)$f->id,
                "name" => $f->name,
                "description" => $f->description,
                "version" => (int)$version->version,
                "published_at" => $version->published_at,
                "triggers" => array_map(function($t) {
                    return ["type" => $t->trigger_type, "value" => $t->trigger_value];
                }, $triggers),
                "nodes" => array_map(function($n) {
                    $payload = json_decode($n->payload_json, true) ?: [];
                    $normalized = $this->normalize_node_payload($n->type, $payload);
                    if (is_array($normalized) && empty($normalized)) $normalized = new stdClass();
                    return [
                        "key" => $n->node_key,
                        "type" => $n->type,
                        "payload" => $normalized
                    ];
                }, $nodes),
                "edges" => array_map(function($e) {
                    return [
                        "from" => $e->from_node_key,
                        "to" => $e->to_node_key,
                        "rule" => $e->rule_json ? json_decode($e->rule_json, true) : null
                    ];
                }, $edges)
            ];
        }

        $this->json([
            "status" => true,
            "business" => [
                "sto_id" => (int)$this->store_val($store, 'sto_id', 0),
                "sto_name" => $this->store_val($store, 'sto_name', ''),
                "sto_email" => $this->store_val($store, 'sto_email', ''),
                "sto_phone" => $this->store_val($store, 'sto_phone', ''),
                "sto_direction" => $this->store_val($store, 'sto_direction', ''),
                "sto_wellcome_message" => $this->store_val($store, 'sto_wellcome_message', ''),
                "sto_description" => $this->store_val($store, 'sto_description', ''),
                "sto_slug" => $this->store_val($store, 'sto_slug', ''),
                "sto_profile_image" => $this->absolute_url($this->store_val($store, 'sto_profile_image', '')),
                "sto_cover_image" => $this->absolute_url($this->store_val($store, 'sto_cover_image', '')),
                "preview_url" => $this->store_val($store, 'sto_slug', '') ? base_url('preview/store/' . $this->store_val($store, 'sto_slug', '')) : base_url('preview/' . (int)$this->store_val($store, 'sto_id', 0)),
                "subscription" => $this->plan_info($store),
                "starters" => $starters
            ],
            "bot_config" => [
                "business_name" => $this->store_val($store, 'sto_name', ''),
                "welcome_msg" => ($this->store_val($store, 'sto_wellcome_message') ?: '¡Hola! Bienvenido a {business}. ¿En qué podemos ayudarte?'),
                "menu_msg" => ($this->store_val($store, 'sto_menu_message') ?: "Elige una opción:\n1. Ver productos\n2. Horario\n3. Ubicación\n4. Hablar con un asesor"),
                "offhours_msg" => ($this->store_val($store, 'sto_offhours_message') ?: 'Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.'),
                "goodbye_msg" => ($this->store_val($store, 'sto_goodbye_message') ?: '¡Gracias por contactarnos! Que tengas un excelente día.'),
                "inactivity_minutes" => (int)$this->store_val($store, 'sto_inactivity_minutes', 15),
                "inactivity_msg" => ($this->store_val($store, 'sto_inactivity_message') ?: 'Cerraremos la sesión por inactividad, hasta luego.'),
                "schedule_enabled" => (int)$this->store_val($store, 'sto_schedule_enabled', 0),
                "schedule_open" => $this->store_val($store, 'sto_schedule_open', '09:00') ?: '09:00',
                "schedule_close" => $this->store_val($store, 'sto_schedule_close', '18:00') ?: '18:00',
                "schedule_days" => $this->store_val($store, 'sto_schedule_days', '1,2,3,4,5') ?: '1,2,3,4,5',
                "timezone" => $this->store_val($store, 'sto_timezone', 'America/Bogota') ?: 'America/Bogota',
                "whatsapp_number" => $session->bs_whatsapp_number ?? '',
                "bot_status" => $effective_status,
                "starters" => $starters
            ],
            "data" => $result
        ]);
    }

    public function get_catalog($us_id = 0)
    {
        if (!$us_id) $this->json(["status" => false, "message" => "us_id requerido"], 400);

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

        $this->json([
            "status" => true,
            "data" => [
                "products" => $products,
                "services" => $services
            ]
        ]);
    }
}

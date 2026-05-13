<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BotApi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
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

    public function login()
    {
        $data = $this->input();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        if (!$email || !$password) {
            $this->json(['status' => false, 'message' => 'email y password requeridos'], 400);
        }

        $user = $this->db->where('us_email', $email)->get('users')->row();
        if (!$user) {
            $this->json(['status' => false, 'message' => 'Credenciales inválidas'], 401);
        }

        if (!password_verify($password, $user->us_password)) {
            $this->json(['status' => false, 'message' => 'Credenciales inválidas'], 401);
        }

        $store = $this->db->where('us_id', $user->us_id)->get('stores')->row();

        $this->json(['status' => true, 'data' => [
            'us_id' => $user->us_id,
            'us_name' => $user->us_name,
            'us_email' => $user->us_email,
            'store' => $store ? [
                'sto_id' => $store->sto_id,
                'sto_name' => $store->sto_name,
                'sto_phone' => $store->sto_phone,
                'sto_wellcome_message' => $store->sto_wellcome_message
            ] : null
        ]]);
    }

    public function update_qr()
    {
        $data = $this->input();
        $us_id = $data['us_id'] ?? 0;
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $qr_base64 = $data['qr_base64'] ?? '';
        $exists = $this->db->where('us_id', $us_id)->get('bot_sessions')->row();

        if ($exists) {
            $this->db->where('us_id', $us_id)->update('bot_sessions', [
                'bs_qr_base64' => $qr_base64,
                'bs_status' => 'waiting_scan',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->db->insert('bot_sessions', [
                'us_id' => $us_id,
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

        $fields = [
            'bs_status' => $data['status'] ?? 'disconnected',
            'bs_whatsapp_number' => $data['whatsapp_number'] ?? '',
            'bs_last_activity' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $exists = $this->db->where('us_id', $us_id)->get('bot_sessions')->row();
        if ($exists) {
            $this->db->where('us_id', $us_id)->update('bot_sessions', $fields);
        } else {
            $fields['us_id'] = $us_id;
            $this->db->insert('bot_sessions', $fields);
        }
        $this->json(['status' => true, 'message' => 'Estado actualizado']);
    }

    public function new_order()
    {
        $data = $this->input();
        $us_id = $data['us_id'] ?? 0;
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $this->db->insert('bot_orders', [
            'us_id' => $us_id,
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
            'us_id' => $us_id,
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

        $this->db->where('bo_id', $bo_id)->update('bot_orders', [
            'bo_status' => $data['status'] ?? 'completado'
        ]);
        $this->json(['status' => true, 'message' => 'Estado del pedido actualizado']);
    }

    public function get_bot_config($us_id = 0)
    {
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $store = $this->db->where('us_id', $us_id)->get('stores')->row();
        $session = $this->db->where('us_id', $us_id)->get('bot_sessions')->row();

        $this->json(['status' => true, 'data' => [
            'business_name' => $store->sto_name ?? '',
            'welcome_msg' => $store->sto_wellcome_message ?? '¡Bienvenido!',
            'menu_msg' => 'Elige una opción:',
            'offhours_msg' => 'Estamos fuera de horario. Escríbenos y te atenderemos en la mañana.',
            'goodbye_msg' => '¡Gracias por contactarnos!',
            'whatsapp_number' => $session->bs_whatsapp_number ?? '',
            'bot_status' => $session->bs_status ?? 'disconnected',
            'starters' => json_decode($store->sto_starters ?? '[]', true) ?: []
        ]]);
    }

    public function get_products($us_id = 0)
    {
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $products = $this->db
            ->select('s.*')
            ->from('services s')
            ->join('service_user su', 's.ser_id = su.ser_id')
            ->where('su.us_id', $us_id)
            ->where('s.ser_status', 1)
            ->get()->result();

        $this->json(['status' => true, 'data' => $products]);
    }

    public function get_business($us_id = 0)
    {
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $store = $this->db->where('us_id', $us_id)->get('stores')->row();
        $this->json(['status' => true, 'data' => $store ?: (object)[]]);
    }

    private function check_session()
    {
        if (!$this->session->userdata('login')) {
            $this->json(['status' => false, 'message' => 'No autorizado'], 401);
        }
    }

    public function get_qr($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $session = $this->db->where('us_id', $us_id)->get('bot_sessions')->row();
        $this->json([
            'status' => (bool)$session,
            'qr' => $session->bs_qr_base64 ?? null,
            'status_text' => $session->bs_status ?? 'no_session'
        ]);
    }

    public function get_status($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $session = $this->db->where('us_id', $us_id)->get('bot_sessions')->row();
        $this->json(['status' => true, 'data' => $session ?: (object)[]]);
    }

    public function refresh_qr($us_id = 0)
    {
        $this->check_session();
        if (!$us_id) $this->json(['status' => false, 'message' => 'us_id requerido'], 400);

        $this->db->where('us_id', $us_id)->update('bot_sessions', [
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
            ->where('us_id', $us_id)
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
            ->where('us_id', $us_id)
            ->order_by('created_at', 'DESC')
            ->limit(50)
            ->get('bot_messages')->result();

        $this->json(['status' => true, 'data' => $messages]);
    }
}

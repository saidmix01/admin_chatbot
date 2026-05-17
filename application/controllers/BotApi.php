<?php
class BotApi extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
	}

	// Bot service reports QR code (base64)
	public function update_qr() {
		$input = json_decode(file_get_contents("php://input"), true);
		$us_id = $input["us_id"] ?? null;
		$qr = $input["qr_base64"] ?? '';

		if (!$us_id) {
			http_response_code(400);
			echo json_encode(["status" => false, "message" => "us_id requerido"]);
			return;
		}

		$exists = $this->db->query("SELECT bs_id FROM bot_sessions WHERE us_id = " . intval($us_id));
		if ($exists->num_rows() > 0) {
			$this->db->where("us_id", $us_id);
			$this->db->update("bot_sessions", [
				"bs_qr_base64" => $qr, "bs_status" => "waiting_scan", "updated_at" => date("Y-m-d H:i:s")
			]);
		} else {
			$this->db->insert("bot_sessions", ["us_id" => $us_id, "bs_qr_base64" => $qr, "bs_status" => "waiting_scan"]);
		}
		echo json_encode(["status" => true, "message" => "QR actualizado"]);
	}

	// Bot service reports connection status
	public function update_status() {
		$input = json_decode(file_get_contents("php://input"), true);
		$us_id = $input["us_id"] ?? null;
		$status = $input["status"] ?? 'disconnected';
		$number = $input["whatsapp_number"] ?? '';

		if (!$us_id) {
			http_response_code(400);
			echo json_encode(["status" => false, "message" => "us_id requerido"]);
			return;
		}

		$data = [
			"bs_status" => $status,
			"bs_whatsapp_number" => $number,
			"bs_last_activity" => date("Y-m-d H:i:s"),
			"updated_at" => date("Y-m-d H:i:s")
		];
		if ($status === 'connected') $data["bs_qr_base64"] = '';

		$exists = $this->db->query("SELECT bs_id FROM bot_sessions WHERE us_id = " . intval($us_id));
		if ($exists->num_rows() > 0) {
			$this->db->where("us_id", $us_id);
			$this->db->update("bot_sessions", $data);
		} else {
			$data["us_id"] = $us_id;
			$this->db->insert("bot_sessions", $data);
		}
		echo json_encode(["status" => true, "message" => "Estado actualizado"]);
	}

	// Admin requests new QR from bot
	public function refresh_qr($us_id) {
		$this->db->where("us_id", intval($us_id));
		$this->db->update("bot_sessions", [
			"bs_qr_base64" => '', "bs_status" => 'waiting', "updated_at" => date("Y-m-d H:i:s")
		]);
		echo json_encode(["status" => true, "message" => "QR reset. El bot generara uno nuevo."]);
	}

	// Get QR and status for a user
	public function get_qr($us_id) {
		$q = $this->db->query("SELECT bs_qr_base64, bs_status FROM bot_sessions WHERE us_id = " . intval($us_id));
		if ($q->num_rows() > 0) {
			$row = $q->row();
			echo json_encode(["status" => true, "qr" => $row->bs_qr_base64, "status_text" => $row->bs_status]);
		} else {
			echo json_encode(["status" => false, "qr" => null, "status_text" => "no_session"]);
		}
	}

	// Get full status for dashboard
	public function get_status($us_id) {
		$q = $this->db->query("SELECT * FROM bot_sessions WHERE us_id = " . intval($us_id));
		if ($q->num_rows() > 0) {
			echo json_encode(["status" => true, "data" => $q->row()]);
		} else {
			echo json_encode(["status" => false, "data" => null, "message" => "Sin sesion"]);
		}
	}

	// Bot reports a new order
	public function new_order() {
		$input = json_decode(file_get_contents("php://input"), true);
		$us_id = $input["us_id"] ?? null;
		if (!$us_id) {
			http_response_code(400);
			echo json_encode(["status" => false, "message" => "us_id requerido"]);
			return;
		}
		$this->db->insert("bot_orders", [
			"us_id" => $us_id,
			"bo_customer_name" => $input["customer_name"] ?? '',
			"bo_customer_phone" => $input["customer_phone"] ?? '',
			"bo_product_name" => $input["product_name"] ?? '',
			"bo_quantity" => $input["quantity"] ?? 1,
			"bo_message" => $input["message"] ?? '',
			"bo_status" => "nuevo"
		]);
		echo json_encode(["status" => true, "message" => "Pedido registrado"]);
	}

	// Bot logs a message
	public function log_message() {
		$input = json_decode(file_get_contents("php://input"), true);
		$us_id = $input["us_id"] ?? null;
		if (!$us_id) {
			http_response_code(400);
			echo json_encode(["status" => false, "message" => "us_id requerido"]);
			return;
		}
		$this->db->insert("bot_messages", [
			"us_id" => $us_id,
			"bm_from" => $input["from"] ?? 'customer',
			"bm_customer_phone" => $input["customer_phone"] ?? '',
			"bm_customer_name" => $input["customer_name"] ?? '',
			"bm_message" => $input["message"] ?? ''
		]);
		echo json_encode(["status" => true, "message" => "Mensaje registrado"]);
	}

	// Get orders for a user
	public function get_orders($us_id = null) {
		if (!$us_id) {
			if (!validate_session()) {
				http_response_code(401);
				echo json_encode(["status" => false, "message" => "Unauthorized"]);
				return;
			}
			$us_id = $this->session->userdata('us_id');
		}
		$q = $this->db->query("SELECT bo.*, u.us_name as business_name 
			FROM bot_orders bo JOIN users u ON u.us_id = bo.us_id 
			WHERE bo.us_id = " . intval($us_id) . " ORDER BY bo.created_at DESC LIMIT 50");
		echo json_encode(["status" => true, "data" => $q->result()]);
	}

	// Get messages for a user
	public function get_messages($us_id = null) {
		if (!$us_id) {
			if (!validate_session()) {
				http_response_code(401);
				echo json_encode(["status" => false, "message" => "Unauthorized"]);
				return;
			}
			$us_id = $this->session->userdata('us_id');
		}
		$q = $this->db->query("SELECT bm.* FROM bot_messages bm 
			WHERE bm.us_id = " . intval($us_id) . " ORDER BY bm.created_at DESC LIMIT 50");
		echo json_encode(["status" => true, "data" => $q->result()]);
	}

	// Bot fetches message config for a business
	public function get_bot_config($us_id) {
		$store = $this->db->query("SELECT sto_name, sto_wellcome_message, sto_menu_message, sto_offhours_message, sto_goodbye_message, sto_schedule_enabled, sto_schedule_open, sto_schedule_close, sto_schedule_days, sto_timezone FROM stores WHERE us_id = " . intval($us_id));
		$session = $this->db->query("SELECT bs_status, bs_whatsapp_number FROM bot_sessions WHERE us_id = " . intval($us_id));

		$store_row = $store->num_rows() > 0 ? $store->row() : null;

		echo json_encode([
			"status" => true,
			"data" => [
				"business_name" => $store_row ? $store_row->sto_name : 'Mi Negocio',
				"welcome_msg" => $store_row ? ($store_row->sto_wellcome_message ?: '¡Hola! Bienvenido a {business}. ¿En qué podemos ayudarte?') : '',
				"menu_msg" => $store_row ? ($store_row->sto_menu_message ?: "Elige una opción:\n1. Ver productos\n2. Horario\n3. Ubicación\n4. Hablar con un asesor") : "Elige una opción:\n1. Ver productos\n2. Horario\n3. Ubicación\n4. Hablar con un asesor",
				"offhours_msg" => $store_row ? ($store_row->sto_offhours_message ?: "Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.") : "Actualmente estamos fuera de nuestro horario de atención. Te atenderemos en cuanto abramos.",
				"goodbye_msg" => $store_row ? ($store_row->sto_goodbye_message ?: "¡Gracias por contactarnos! Que tengas un excelente día.") : "¡Gracias por contactarnos! Que tengas un excelente día.",
				// Schedule
				"schedule_enabled" => $store_row ? intval($store_row->sto_schedule_enabled) : 0,
				"schedule_open" => $store_row ? ($store_row->sto_schedule_open ?: '09:00') : '09:00',
				"schedule_close" => $store_row ? ($store_row->sto_schedule_close ?: '18:00') : '18:00',
				"schedule_days" => $store_row ? ($store_row->sto_schedule_days ?: '1,2,3,4,5') : '1,2,3,4,5',
				"timezone" => $store_row ? ($store_row->sto_timezone ?: 'America/Bogota') : 'America/Bogota',
				// Connection
				"whatsapp_number" => $session->num_rows() > 0 ? $session->row()->bs_whatsapp_number : '',
				"bot_status" => $session->num_rows() > 0 ? $session->row()->bs_status : 'disconnected'
			]
		]);
	}

	// Bot fetches products for a business (public)
	public function get_products($us_id) {
		$q = $this->db->query("SELECT s.ser_id, s.ser_name, s.ser_price, s.ser_description, s.ser_imagen, s.ser_type
			FROM services s
			INNER JOIN service_user su ON su.ser_id = s.ser_id
			WHERE su.us_id = " . intval($us_id) . " AND s.ser_status = 1
			ORDER BY s.ser_name");

		echo json_encode(["status" => true, "data" => $q->result()]);
	}

	// Bot fetches business info (public)
	public function get_business($us_id) {
		$q = $this->db->query("SELECT sto_name, COALESCE(sto_description, sto_wellcome_message) as description, sto_direction as address, sto_phone as phone
			FROM stores WHERE us_id = " . intval($us_id));

		if ($q->num_rows() > 0) {
			$business = $q->row();
			echo json_encode(["status" => true, "data" => $business]);
		} else {
			echo json_encode(["status" => false, "data" => null, "message" => "Negocio no encontrado"]);
		}
	}

	// Bot updates order status (e.g. when customer confirms)
	public function update_order_status() {
		$input = json_decode(file_get_contents("php://input"), true);
		$bo_id = $input["bo_id"] ?? null;
		$status = $input["status"] ?? '';

		if (!$bo_id || !$status) {
			http_response_code(400);
			echo json_encode(["status" => false, "message" => "bo_id y status requeridos"]);
			return;
		}

		$this->db->where("bo_id", $bo_id);
		$this->db->update("bot_orders", ["bo_status" => $status]);
		echo json_encode(["status" => true, "message" => "Estado del pedido actualizado"]);
	}
}

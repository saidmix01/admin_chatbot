<?php
class Orders extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			$us_id = $this->session->userdata('us_id');

			$q = $this->db->query("SELECT bo.*, u.us_name as business_name 
				FROM bot_orders bo 
				JOIN users u ON u.us_id = bo.us_id 
				WHERE bo.us_id = " . intval($us_id) . " 
				ORDER BY bo.created_at DESC LIMIT 50");

			$data = array(
				"title" => "Pedidos",
				"active_menu" => "pedidos",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"orders" => $q->result(),
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('orders/orders_view');
			$data_footer = array("scripts" => ["js/general.js"]);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function update_status() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			$bo_id = $input["bo_id"] ?? null;
			$status = $input["status"] ?? '';

			if (!$bo_id || !$status) throw new Exception("Datos incompletos", 1);

			$this->db->where("bo_id", $bo_id);
			$this->db->update("bot_orders", ["bo_status" => $status]);

			$response["status"] = true;
			$response["message"] = "Estado actualizado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

<?php
class BotConfig extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		$this->load->model('Store/Store_model', 'Store_model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			$this->Store_model->data = array("s.us_id" => $this->session->userdata('us_id'));
			$store = $this->Store_model->get_stores();

			$data = array(
				"title" => "Bot Mensajes",
				"active_menu" => "bot",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"store" => !empty($store["data"]) ? $store["data"][0] : null,
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('bot_config/config_view');
			$data_footer = array("scripts" => ["js/general.js"]);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function save() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			if (empty($input)) throw new Exception("Datos vacíos", 1);

			$us_id = $this->session->userdata('us_id');

			$this->Store_model->data = array("s.us_id" => $us_id);
			$store = $this->Store_model->get_stores();

			if (!empty($store["data"])) {
				$update = array();
				if (!empty($input["welcome_msg"])) $update["sto_wellcome_message"] = $input["welcome_msg"];
				if (!empty($update)) {
					$this->db->where("sto_id", $store["data"][0]->sto_id);
					$this->db->update("stores", $update);
				}
				if (isset($input["starters"])) {
					$this->db->where("sto_id", $store["data"][0]->sto_id);
					$this->db->update("stores", array("sto_starters" => json_encode($input["starters"])));
				}
				}
			}

			$response["status"] = true;
			$response["message"] = "Configuración guardada correctamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

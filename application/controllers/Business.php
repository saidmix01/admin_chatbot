<?php
class Business extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->helper('website_helper');
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
			$store_data = !empty($store["data"]) ? $store["data"][0] : null;

			// Auto-generate slug if missing
			if ($store_data && empty($store_data->sto_slug)) {
				$slug = unique_slug(slugify($store_data->sto_name));
				$this->db->where("sto_id", $store_data->sto_id);
				$this->db->update("stores", array("sto_slug" => $slug));
				$store_data->sto_slug = $slug;
			}

			$data = array(
				"title" => "Mi Negocio",
				"active_menu" => "negocio",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"store" => $store_data,
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('business/config_view');
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

			$update = array();
			if (!empty($input["business_name"])) $update["sto_name"] = $input["business_name"];
			if (isset($input["description"])) $update["sto_wellcome_message"] = $input["description"];
			if (isset($input["address"])) $update["sto_direction"] = $input["address"];
			if (isset($input["whatsapp"])) $update["sto_phone"] = $input["whatsapp"];

			// Handle slug
			if (!empty($input["slug"])) {
				$slug = slugify($input["slug"]);
				$existing_id = !empty($store["data"]) ? $store["data"][0]->sto_id : null;
				$slug = unique_slug($slug, $existing_id);
				$update["sto_slug"] = $slug;
				$response["slug"] = $slug;
			}

			if (!empty($store["data"])) {
				if (!empty($update)) {
					$this->db->where("sto_id", $store["data"][0]->sto_id);
					$this->db->update("stores", $update);
				}
			} else {
				if (empty($update["sto_slug"]) && !empty($update["sto_name"])) {
					$update["sto_slug"] = unique_slug(slugify($update["sto_name"]));
				}
				$this->db->insert("stores", array_merge($update, array(
					"us_id" => $us_id,
					"sto_status" => 1
				)));
			}

			$response["status"] = true;
			$response["message"] = "Negocio actualizado correctamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

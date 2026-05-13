<?php
class Clients extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->helper('website_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		$this->load->model('General_Model/General_Model', 'General_Model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			$query = $this->db->query("SELECT u.us_id, u.us_name, u.us_email, u.us_status, p.pro_description,
					(SELECT sto_name FROM stores WHERE us_id = u.us_id LIMIT 1) as store_name
				FROM users u
				JOIN profiles p ON p.pro_id = u.pro_id
				WHERE u.pro_id = 2
				ORDER BY u.us_id");

			$data = array(
				"title" => "Clientes",
				"active_menu" => "clientes",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"clients" => $query->result(),
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('clients/clients_view');
			$data_footer = array("scripts" => ["js/general.js"]);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function create() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			if (empty($input["name"]) || empty($input["email"]) || empty($input["password"]))
				throw new Exception("Todos los campos son requeridos", 1);

			$check = $this->db->query("SELECT us_id FROM users WHERE us_email = '" . $input["email"] . "'");
			if ($check->num_rows() > 0) throw new Exception("El email ya está registrado", 1);

			$this->db->insert("users", array(
				"us_name" => $input["name"],
				"us_email" => $input["email"],
				"us_password" => hash_pass($input["password"]),
				"pro_id" => 2,
				"us_status" => 1
			));
			$user_id = $this->db->insert_id();
			$this->db->insert("stores", array("sto_name" => $input["name"], "sto_status" => 1, "us_id" => $user_id));

			$response["status"] = true;
			$response["message"] = "Cliente creado exitosamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function update() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			if (empty($input["us_id"])) throw new Exception("ID requerido", 1);

			$update = array();
			if (!empty($input["name"])) $update["us_name"] = $input["name"];
			if (!empty($input["email"])) $update["us_email"] = $input["email"];
			if (!empty($input["password"])) $update["us_password"] = hash_pass($input["password"]);
			if (isset($input["status"])) $update["us_status"] = $input["status"] ? 1 : 0;

			if (!empty($update)) {
				$this->db->where("us_id", $input["us_id"]);
				$this->db->update("users", $update);
			}

			$response["status"] = true;
			$response["message"] = "Cliente actualizado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function delete() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			if (empty($input["us_id"])) throw new Exception("ID requerido", 1);

			$this->db->where("us_id", $input["us_id"]);
			$this->db->delete("users");

			$response["status"] = true;
			$response["message"] = "Cliente eliminado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

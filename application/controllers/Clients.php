<?php
class Clients extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		$this->load->model('General_Model/General_Model', 'General_Model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			// Get all client users
			$this->General_Model->table_name = "users";
			$sql = "SELECT u.us_id, u.us_name, u.us_email, u.us_status, p.pro_description,
						   (SELECT sto_name FROM stores WHERE us_id = u.us_id LIMIT 1) as store_name
					FROM users u
					JOIN profiles p ON p.pro_id = u.pro_id
					WHERE u.pro_id = 2
					ORDER BY u.us_id";
			$query = $this->db->query($sql);
			$clients = $query->result();

			$data = array(
				"title" => "Clientes",
				"active_menu" => "clientes",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"clients" => $clients,
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('clients/clients_view');
			$this->load->view('includes/footer');
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function create() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			
			// Validate
			if (empty($input["name"]) || empty($input["email"]) || empty($input["password"]))
				throw new Exception("Todos los campos son requeridos", 1);

			// Check if email exists
			$check = $this->db->query("SELECT us_id FROM users WHERE us_email = '" . $input["email"] . "'");
			if ($check->num_rows() > 0)
				throw new Exception("El email ya está registrado", 1);

			// Create user with perfil Cliente (pro_id=2)
			$this->load->helper('website_helper');
			$this->db->insert("users", array(
				"us_name" => $input["name"],
				"us_email" => $input["email"],
				"us_password" => hash_pass($input["password"]),
				"pro_id" => 2,
				"us_status" => 1
			));
			$user_id = $this->db->insert_id();

			// Create store for client
			$this->db->insert("stores", array(
				"sto_name" => $input["name"],
				"sto_status" => 1,
				"us_id" => $user_id
			));

			$response["status"] = true;
			$response["message"] = "Cliente creado exitosamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

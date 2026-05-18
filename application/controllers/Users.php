<?php 

class Users extends CI_Controller 
{
	public function __construct() {
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model','General_Model');
		$this->load->model('Users/Users_model','Users_model');
		$this->load->model('Profiles/Profile_model','Profile_model');
		//Helpers
		$this->load->helper('general_helper');
		$this->load->helper('website_helper');
	}

	public function index(){
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);

			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if($user_data["status"] == false) throw new Exception($user_data["message"], 1);

			$query = $this->db->query("SELECT u.us_id, u.us_name, u.us_email, u.us_status, p.pro_description,
					(COALESCE(
						(SELECT s.sto_name
						 FROM user_store us
						 INNER JOIN stores s ON s.sto_id = us.sto_id
						 WHERE us.us_id = u.us_id
						 ORDER BY us.created_at DESC
						 LIMIT 1),
						(SELECT sto_name FROM stores WHERE us_id = u.us_id LIMIT 1)
					)) as store_name
				FROM users u
				JOIN profiles p ON p.pro_id = u.pro_id
				WHERE u.pro_id = 2
				ORDER BY u.us_id");

			$data_header = array(
				"title" => "Usuarios",
				"active_menu" => "users",
				"menus" => get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"],
				"user_data" => $user_data["data"],
				"scripts" => ["js/general.js"]
			);
			$profiles = $this->db->where('pro_status', 1)->order_by('pro_id', 'ASC')->get('profiles')->result();
			$data_view = array("users" => $query->result(), "profiles" => $profiles);
			$data_footer = array("scripts" => ["js/general.js"]);

			$this->load->view('includes/header',$data_header);
			$this->load->view('users/users_saas_view', $data_view);
			$this->load->view('includes/footer',$data_footer);
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

			$pro_id = !empty($input["pro_id"]) ? intval($input["pro_id"]) : 2;

			$plan_start = !empty($input["plan_start"]) ? $input["plan_start"] : date('Y-m-d');
			$plan_start_ts = strtotime($plan_start);
			if ($plan_start_ts === false) $plan_start = date('Y-m-d');
			$plan_end = date('Y-m-d', strtotime($plan_start . ' +30 days'));

			$this->db->insert("users", array(
				"us_name" => $input["name"],
				"us_email" => $input["email"],
				"us_password" => hash_pass($input["password"]),
				"pro_id" => $pro_id,
				"us_status" => 1
			));
			$user_id = $this->db->insert_id();

			$store_data = array("sto_name" => $input["name"], "sto_status" => 1, "us_id" => $user_id);
			if ($this->db->field_exists('sto_plan_start', 'stores')) $store_data["sto_plan_start"] = $plan_start;
			if ($this->db->field_exists('sto_plan_end', 'stores')) $store_data["sto_plan_end"] = $plan_end;
			$this->db->insert("stores", $store_data);
			$sto_id = $this->db->insert_id();

			$this->db->insert("user_store", array("us_id" => $user_id, "sto_id" => $sto_id));

			$response["status"] = true;
			$response["message"] = "Usuario creado exitosamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function update_user() {
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

			if (!empty($input["plan_start"]) && $this->db->field_exists('sto_plan_start', 'stores')) {
				$plan_start = $input["plan_start"];
				$ts = strtotime($plan_start);
				if ($ts !== false) {
					$plan_end = date('Y-m-d', strtotime($plan_start . ' +30 days'));
					$sto_id = null;
					$q = $this->db->query("SELECT sto_id FROM user_store WHERE us_id = ? ORDER BY created_at DESC LIMIT 1", [intval($input["us_id"])]);
					if ($q->num_rows() > 0) $sto_id = $q->row()->sto_id;
					if (!$sto_id) {
						$q2 = $this->db->query("SELECT sto_id FROM stores WHERE us_id = ? ORDER BY sto_id DESC LIMIT 1", [intval($input["us_id"])]);
						if ($q2->num_rows() > 0) $sto_id = $q2->row()->sto_id;
					}
					if ($sto_id) {
						$sup = array("sto_plan_start" => $plan_start);
						if ($this->db->field_exists('sto_plan_end', 'stores')) $sup["sto_plan_end"] = $plan_end;
						$this->db->where("sto_id", $sto_id)->update("stores", $sup);
					}
				}
			}

			$response["status"] = true;
			$response["message"] = "Usuario actualizado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function delete_user() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			if (empty($input["us_id"])) throw new Exception("ID requerido", 1);

			$this->db->where("us_id", $input["us_id"]);
			$this->db->delete("users");

			$response["status"] = true;
			$response["message"] = "Usuario eliminado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function your_profile(){
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);

			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if($user_data["status"] == false) throw new Exception($user_data["message"], 1);
			
			$data_header["menus"] = get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"];
			$data_header["user_data"] = $user_data["data"];
			$data_footer["scripts"] = [
				"js/users/profile.js"
			];
			$data_view["us_id"] = $this->session->userdata('us_id');
			$this->load->view('includes/header',$data_header);
			$this->load->view('users/profile_user_view',$data_view);
			$this->load->view('includes/footer',$data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function load_profiles(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			header("Content-Type: application/json; charset=UTF-8");
			$data_response = $this->Profile_model->get_profiles();
			if($data_response["status"] == false) throw new Exception($data_response["message"], 1);
			$response["status"] = true;
			$response["data"] = $data_response["data"];
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	/**
	 * The function `save` in PHP attempts to save user data to a database table and returns a JSON
	 * response indicating success or failure.
	 */
	public function save() {
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			if(empty($this->input->POST())) throw new Exception("There is empty data", 1);
			$data_send = $this->input->POST();
			$data_send["us_password"] = hash_pass($data_send["us_password"]);
			$this->General_Model->table_name = "users";
			$this->General_Model->data = $data_send;
			$data_insert = $this->General_Model->insert();
			if(!$data_insert["status"]) throw new Exception($data_insert["message"], 1);
			
			$response["status"] = true;
			$response["message"] = "data created successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	/**
	 * The function `update` in PHP updates user data in a database table and returns a JSON response
	 * indicating success or failure.
	 */
	public function update(){
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			if(empty($this->input->POST())) throw new Exception("There is empty data", 1);
			$data_send = $this->input->POST();
			$data_send["us_password"] = hash_pass($data_send["us_password"]);
			$this->General_Model->table_name = "users";
			$this->General_Model->data = $data_send;
			$this->General_Model->where = array("us_id"=>$this->input->POST('us_id'));
			$data_update = $this->General_Model->update();
			if(!$data_update["status"]) throw new Exception($data_update["message"], 1);
			
			$response["status"] = true;
			$response["message"] = "data updated successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function get_users(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			header("Content-Type: application/json; charset=UTF-8");
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$input = file_get_contents("php://input");
				$data = json_decode($input, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					$this->Users_model->data = $data;
					$data_response = $this->Users_model->get_users();
					if(!$data_response["status"]) throw new Exception($data_response["message"], 1);
					$response["status"] = true;
					$response["data"] = $data_response["data"];
					$response["message"] = "Query executed correctly";
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}


?>

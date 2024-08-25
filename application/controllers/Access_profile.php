<?php 

class Access_profile extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		$this->load->model('Profiles/Profile_model', 'Profile_model');
		$this->load->model('Menus/Menus_model', 'Menus_model');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		//Helpers
		$this->load->helper('general_helper');
	}

	public function index() {
		try {
			if(empty($this->input->post())) throw new Exception("Incorrect paramn", 1);
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if ($user_data["status"] == false) throw new Exception($user_data["message"], 1);

			$data_header["menus"] = get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"];
			$data_header["user_data"] = $user_data["data"];
			$data_body["pro_id"] = $this->input->post('pro_id');
			$data_footer["scripts"] = [
				"js/menu_profile/menu_profile.js"
			];
			$this->load->view('includes/header', $data_header);
			$this->load->view('profiles/menu_profile_view',$data_body);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
		}
	}

	public function get_menus() {
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			header("Content-Type: application/json; charset=UTF-8");
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$input = file_get_contents("php://input");
				$data = json_decode($input, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					$this->Menus_model->where = $data;
					$data_model = $this->Menus_model->get_menus();
					if($data_model["status"] == false) throw new Exception($data_model["message"], 1);
					$response = $data_model;
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th;
		}
		echo json_encode($response);
	}

	public function validate_menu_profile() {
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			header("Content-Type: application/json; charset=UTF-8");
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$input = file_get_contents("php://input");
				$data = json_decode($input, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					$this->Menus_profile_model->data = $data;
					$data_model = $this->Menus_profile_model->get_menu_profile();
					if($data_model["status"] == false) throw new Exception($data_model["message"], 1);
					$response["status"] = true;
					$response["data"] = count((array) $data_model["data"]);
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th;
		}
		echo json_encode($response);
	}

	public function save_delete() {
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			header("Content-Type: application/json; charset=UTF-8");
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$input = file_get_contents("php://input");
				$data = json_decode($input, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					$i = 0;
					foreach ($data as $key) {
						if($key["action"] == "save"){
							//Validate if exists
							$this->Menus_profile_model->data = array(
								"us_id" => $this->session->userdata('us_id'),
								"m.men_id" => $key["men_id"]
							);
							$response_validation = $this->Menus_profile_model->get_menu_user();
							if($response_validation["status"] == false) throw new Exception($response_validation["message"], 1);
							if(count((array) $response_validation["data"]) <= 0 ){
								$this->General_Model->table_name = "menu_profile";
								$this->General_Model->data = array(
									"men_id" => $key["men_id"],
									"pro_id" => $key["pro_id"],
									"men_pro_create_date" => $key["men_pro_create_date"]
								);
								$this->General_Model->insert();
							}
							$i++;
						}elseif($key["action"] == "delete"){
							$this->General_Model->table_name = "menu_profile";
							$this->General_Model->where = array(
								"men_id" => $key["men_id"],
								"pro_id" => $key["pro_id"]
							);
							$this->General_Model->delete();
							$i++;
						}
					}
					if(count($data) == $i){
						$response["status"] = "true";
					}else{
						$response["message"] = "Error";
					}
				}
			}
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
			$response["message"] = $th;
		}
		echo json_encode($response);
	}
}


?>

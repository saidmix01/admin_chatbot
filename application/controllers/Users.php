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
	}

	public function index(){
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);

			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if($user_data["status"] == false) throw new Exception($user_data["message"], 1);

			$data_header["user_data"] = $user_data["data"];
			$data_footer["scripts"] = [
				"js/users/users.js"
			];
			$this->load->view('includes/header',$data_header);
			$this->load->view('users/user_view');
			$this->load->view('includes/footer',$data_footer);
		} catch (\Throwable $th) {
			//throw $th;
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

			$this->General_Model->table_name = "users";
			$this->General_Model->data = $this->input->POST();
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

			$this->General_Model->table_name = "users";
			$this->General_Model->data = $this->input->POST();
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

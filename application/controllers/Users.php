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
}


?>

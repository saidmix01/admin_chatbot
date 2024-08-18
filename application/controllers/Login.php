<?php

class Login extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model('Login_Model');
	}

	public function index(){
		$this->load->view('includes_login/head');
		$this->load->view('login/login_view');
		$this->load->view('includes_login/footer');
	}

	public function login(){
		$response = array(
			"status"=>false,
			"message"=>""
		);
		try {
			if(empty($this->input->POST())) throw new Exception("There is empty data", 1);
			
			$this->Login_Model->us_email = $this->input->post('us_email');
			$this->Login_Model->us_password = $this->input->post('us_password');
			$response_model = $this->Login_Model->get_user_data();
			if($response_model["status"] == true && !empty($response_model["data"])){
				if($response_model["data"]->us_status == 1){
					$this->session->set_userdata('us_email', $response_model["data"]->us_email);
					$this->session->set_userdata('us_id', $response_model["data"]->us_id);
					$this->session->set_userdata('login', true);
					$response["status"] = true;
				}else{
					throw new Exception("The user is inactive", 1);
				}
			}else{
				throw new Exception($response_model["message"], 1);
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}


?>

<?php

final class Questions extends CI_Controller 
{
    private $module_name = "Questions";
    public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		$this->load->model('Questions/Question_model', 'Question_model');
		//Helpers
		$this->load->helper('general_helper');
	}

    public function index()
	{
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if ($user_data["status"] == false) throw new Exception($user_data["message"], 1);

			$data_header["user_data"] = $user_data["data"];
			$data_header["menus"] = get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"];

            $data_view['module_name'] = $this->module_name;

			$data_footer["scripts"] = [
               // "js/general.js",
				"js/questions/questions.js?cv=".control_version()
			];

			$this->load->view('includes/header', $data_header);
			$this->load->view('questions/questions_view',$data_view);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function save_question(){
		try {
			$response = array(
				"status" => false,
				"message" => ""
			);
			//Validate sesion
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			//Validate data
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);
			$data_send = $this->input->POST();
			$data_send["us_id"] = $this->session->userdata('us_id');
			$this->General_Model->table_name = "questions";
			$this->General_Model->data = $data_send;
			//Insert into db
			$data_insert = $this->General_Model->insert();
			if (!$data_insert["status"]) throw new Exception($data_insert["message"], 1);
			$response["status"] = true;
			$response["message"] = "data created successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function save_answer(){
		try {
			$response = array(
				"status" => false,
				"message" => ""
			);
			//Validate sesion
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			//Validate data
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);
			$data_send = $this->input->POST();
			$data_send["us_id"] = $this->session->userdata('us_id');
			$this->General_Model->table_name = "answer";
			$this->General_Model->data = $data_send;
			//Insert into db
			$data_insert = $this->General_Model->insert();
			if (!$data_insert["status"]) throw new Exception($data_insert["message"], 1);
			$response["status"] = true;
			$response["message"] = "data created successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function get_questions(){
		try {
			$response = array(
				"status" => false,
				"message" => ""
			);
			//Validate sesion
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			$data_send = $this->input->POST();
			$data_send["us_id"] = $this->session->userdata('us_id');
			$response["status"] = true;
			$response["data"] = $this->Question_model->get_questions(); 
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}


?>
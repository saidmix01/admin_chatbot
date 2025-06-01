<?php

class Services extends CI_Controller
{
    public function __construct() {
		parent::__construct();
        //Helpers
		$this->load->helper('general_helper');
        //Models
        $this->load->model('General_Model/General_Model', 'General_Model');
		$this->load->model('Page/Services_model','Services_model');
        $this->load->model('Users/Users_model','Users_model');
    }

    public function get_services_by_user(){
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
					$data["su.us_id"] = $this->session->userdata('us_id');
					$this->Services_model->data = $data;
					$data_response = $this->Services_model->get_service_user();
					if (!$data_response["status"]) throw new Exception($data_response["message"], 1);
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
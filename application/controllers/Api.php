<?php

class Api extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		//Helpers
		$this->load->helper('general_helper');
	}

	public function index()
	{
		$this->load->view('error_pages/404');
	}

	public function get_store_information()
	{
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			header("Content-Type: application/json; charset=UTF-8");
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$input = file_get_contents("php://input");
				$data = json_decode($input, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					//Load model
					$this->load->model('Store/Store_model', 'Store_model');
					$this->Store_model->data = array(
						"u.us_id" => $data["us_id"],
						"u.us_status" => 1,
						"s.sto_status" => 1,
						"s.sto_id" => $data["sto_id"]
					);
					$data_stores = $this->Store_model->get_stores();
					if($data_stores["status"] == false){
						http_response_code(400);
						throw new Exception($data_stores["message"], 1);
					}
					$response["status"] = true;
					$response["data"] = $data_stores["data"];
				} else {
					http_response_code(500);
					throw new Exception("Internal server error", 1);
				}
			} else {
				http_response_code(400);
				throw new Exception("Bad request", 1);
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function get_questions(){
		{
			$response = array(
				"status" => false,
				"data" => array(),
				"message" => ""
			);
			try {
				header("Content-Type: application/json; charset=UTF-8");
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$input = file_get_contents("php://input");
					$data = json_decode($input, true);
					if (json_last_error() === JSON_ERROR_NONE) {
						//Load model
						$this->load->model('Chat/Chat_model', 'Chat_model');
						$this->Chat_model->data = array(
							"cq.us_id" => $data["us_id"],
							"cq.chq_status" => 1,
						);
						$data_stores = $this->Chat_model->get_questions();
						if($data_stores["status"] == false){
							http_response_code(400);
							throw new Exception($data_stores["message"], 1);
						}
						$response["status"] = true;
						$response["data"] = $data_stores["data"];
					} else {
						http_response_code(500);
						throw new Exception("Internal server error", 1);
					}
				} else {
					http_response_code(400);
					throw new Exception("Bad request", 1);
				}
			} catch (\Throwable $th) {
				$response["message"] = $th->getMessage();
			}
			echo json_encode($response);
		}
	}
}

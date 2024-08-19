<?php

class Menu extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model','General_Model');
		$this->load->model('Menus/Menus_model','Menus_model');
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
				"js/menu/menu.js"
			];
			$this->load->view('includes/header',$data_header);
			$this->load->view('menu/menu_view');
			$this->load->view('includes/footer',$data_footer);
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	public function save(){
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			if(empty($this->input->POST())) throw new Exception("There is empty data", 1);

			$this->General_Model->table_name = "menus";
			$this->General_Model->data = $this->input->POST();
			$data_insert = $this->General_Model->insert();
			if(!$data_insert["status"]) throw new Exception($data_insert["message"], 1);
			
			$response["status"] = true;
			$response["message"] = "Registro guardado correctamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function get_menus(){
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
					$men_status = $data["men_status"];
					$this->Menus_model->data = array("men_status"=>$men_status);
					$data_response = $this->Menus_model->get_menus();
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

	public function delete(){
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
					$men_id = $data["men_id"];
					$this->General_Model->where = array("men_id"=>$men_id);
					$this->General_Model->table_name = "menus";
					$data_response = $this->General_Model->delete();
					if(!$data_response["status"]) throw new Exception($data_response["message"], 1);
					$response["status"] = true;
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

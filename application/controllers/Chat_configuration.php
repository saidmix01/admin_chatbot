<?php

class Chat_configuration extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		$this->load->model('Chat/Chat_model', 'Chat_model');
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
			$data_footer["scripts"] = [
				"js/chat/chat_configuration.js"
			];
			$this->load->view('includes/header', $data_header);
			$this->load->view('chat/chat_configuration_view');
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	/**
	 * The `save()` function in PHP validates session and form data before inserting into a database table
	 * and returns a JSON response indicating success or failure.
	 */
	public function save() {
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			//Validate session
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			//Validate form data
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);

			$this->General_Model->table_name = "chat_questions";
			$this->General_Model->data = $this->input->POST();
			$data_insert = $this->General_Model->insert();
			if (!$data_insert["status"]) throw new Exception($data_insert["message"], 1);

			$response["status"] = true;
			$response["message"] = "data created successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	/**
	 * The function `get_questions` retrieves questions from a chat model and returns a JSON response with
	 * the status, data, and message.
	 */
	public function get_questions(){
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
					$this->Chat_model->data = $data;
					$data_response = $this->Chat_model->get_questions();
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

	/**
	 * The function `update` in PHP updates a record in the `chat_questions` table based on the provided
	 * data and session validation.
	 */
	public function update()
	{
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);

			$this->General_Model->table_name = "chat_questions";
			$this->General_Model->data = $this->input->POST();
			$this->General_Model->where = array("chq_id" => $this->input->POST('chq_id'));
			$data_update = $this->General_Model->update();
			if (!$data_update["status"]) throw new Exception($data_update["message"], 1);

			$response["status"] = true;
			$response["message"] = "data updated successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function delete()
	{
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
					$chq_id = $data["chq_id"];
					$this->General_Model->where = array("chq_id" => $chq_id);
					$this->General_Model->table_name = "chat_questions";
					$data_response = $this->General_Model->delete();
					if (!$data_response["status"]) throw new Exception($data_response["message"], 1);
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

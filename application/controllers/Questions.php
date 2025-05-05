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
                //"",
				"js/questions/questions.js?cv=".control_version()
			];

			$this->load->view('includes/header', $data_header);
			$this->load->view('questions/questions_view',$data_view);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	/**
	 * The `save_question` function in PHP validates session and input data, inserts a new question into
	 * the database, and returns a JSON response indicating success or failure.
	 */
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

	/**
	 * The `save_answer` function in PHP validates session and input data, inserts data into the database,
	 * and returns a JSON response indicating success or failure.
	 */
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
			//get Order
			$this->Question_model->que_id = $data_send["que_id"];
			$answer_data_order = $this->Question_model->get_order_answer();
			if ($answer_data_order["status"] == false) throw new Exception($answer_data_order["message"], 1);
			$data_send["ans_order"] = $answer_data_order["data"];
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

	/**
	 * The function `get_questions` retrieves questions from the Question_model after validating the
	 * session and returns the result in JSON format.
	 */
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

	/**
	 * The function `get_answers` in PHP retrieves answers from the Question_model after validating the
	 * session and returns the response in JSON format.
	 */
	public function get_answers(){
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
					$this->Question_model->data = $data;
					$data_response = $this->Question_model->get_answers();
					if (!$data_response["status"]) throw new Exception($data_response["message"], 1);
					$response["status"] = true;
					$response["data"] = $data_response;
					$response["message"] = "Query executed correctly";
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	/**
	 * The function `delete_answer` in PHP deletes a specific answer from a database table based on the
	 * provided answer ID.
	 */
	public function delete_answer(){
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
					$ans_id = $data["ans_id"];
					$this->General_Model->where = array("ans_id" => $ans_id);
					$this->General_Model->table_name = "answer";
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

	/**
	 * The function `update_order_question` updates the order of questions in a database table based on
	 * input data received via POST request.
	 */
	public function update_order_question(){
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
					foreach ($data["data_send"] as $value) {
						$this->General_Model->table_name = "questions";
						$this->General_Model->where = array("que_id" => $value["que_id"]);
						$this->General_Model->data = array("que_order" => $value["que_order"]);
						$data_response = $this->General_Model->update();
						if (!$data_response["status"]) throw new Exception($data_response["message"], 1);
					}
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

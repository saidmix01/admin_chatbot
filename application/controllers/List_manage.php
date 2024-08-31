<?php

class List_manage extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		$this->load->model('List_manage/List_model', 'List_model');
		//Helpers
		$this->load->helper('general_helper');
	}

	/**
	 * The index function checks for a valid session, retrieves user data, and loads views for the header,
	 * menu, and footer in a PHP codebase.
	 */
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
				"js/list/list.js"
			];
			$this->load->view('includes/header', $data_header);
			$this->load->view('list/list_view');
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	public function options_list() {
		try {
			if(empty($this->input->post())) throw new Exception("Incorrect paramn", 1);
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if ($user_data["status"] == false) throw new Exception($user_data["message"], 1);

			$data_header["user_data"] = $user_data["data"];
			$data_header["menus"] = get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"];
			$data_body["lis_id"] = $this->input->post('lis_id');
			$data_footer["scripts"] = [
				"js/list/option.js"
			];
			$this->load->view('includes/header', $data_header);
			$this->load->view('list/options_view',$data_body);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	/**
	 * The `save` function in PHP attempts to save data to a database table and returns a JSON response
	 * indicating success or failure.
	 */
	public function save()
	{
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);

			$this->General_Model->table_name = "lists";
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
	 * The function `update` in PHP updates data in the "menus" table based on input POST data and returns
	 * a JSON response indicating success or failure.
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

			$this->General_Model->table_name = "lists";
			$this->General_Model->data = $this->input->POST();
			$this->General_Model->where = array("lis_id" => $this->input->POST('lis_id'));
			$data_update = $this->General_Model->update();
			if (!$data_update["status"]) throw new Exception($data_update["message"], 1);

			$response["status"] = true;
			$response["message"] = "data updated successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	/**
	 * The function `get_list` in PHP retrieves menu data and returns a JSON response with status, data,
	 * and message.
	 */
	public function get_lists()
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
					//$men_status = $data["men_status"];
					$this->List_model->data = $data;
					$data_response = $this->List_model->get_lists();
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
	 * The `delete` function in PHP handles the deletion of a menu item based on the provided ID and
	 * returns a JSON response indicating the status of the operation.
	 */
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
					$lis_id = $data["lis_id"];
					$this->General_Model->where = array("lis_id" => $lis_id);
					$this->General_Model->table_name = "lists";
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
	/**********************************OPTIONS ************************************* */

	public function get_options()
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
					//$men_status = $data["men_status"];
					$this->List_model->data = $data;
					$data_response = $this->List_model->get_options();
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
	 * The `save` function in PHP attempts to save data to a database table and returns a JSON response
	 * indicating success or failure.
	 */
	public function save_option()
	{
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);

			$this->General_Model->table_name = "options";
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
	 * The function `update` in PHP updates data in the "menus" table based on input POST data and returns
	 * a JSON response indicating success or failure.
	 */
	public function update_option()
	{
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if (!validate_session()) throw new Exception("The unauthenticated user", 1);
			if (empty($this->input->POST())) throw new Exception("There is empty data", 1);

			$this->General_Model->table_name = "options";
			$this->General_Model->data = $this->input->POST();
			$this->General_Model->where = array("lis_id" => $this->input->POST('lis_id'));
			$data_update = $this->General_Model->update();
			if (!$data_update["status"]) throw new Exception($data_update["message"], 1);

			$response["status"] = true;
			$response["message"] = "data updated successfully";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function delete_option()
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
					$opt_id = $data["opt_id"];
					$this->General_Model->where = array("opt_id" => $opt_id);
					$this->General_Model->table_name = "options";
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

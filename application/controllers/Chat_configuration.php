<?php

class Chat_configuration extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//Models
		
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
			//throw $th;
		}
	}
}


?>

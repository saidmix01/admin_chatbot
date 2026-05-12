<?php
class Whatsapp extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			$data = array(
				"title" => "WhatsApp QR",
				"active_menu" => "whatsapp",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"qr_status" => "waiting", // waiting | connected | expired
				"whatsapp_number" => ""
			);
			$this->load->view('includes/header', $data);
			$this->load->view('whatsapp/qr_view');
			$this->load->view('includes/footer');
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}
}

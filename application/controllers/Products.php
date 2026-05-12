<?php
class Products extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		$this->load->model('Page/Services_model', 'Services_model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			$services = $this->Services_model->get_services();

			$data = array(
				"title" => "Productos",
				"active_menu" => "productos",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"products" => $services["data"] ?? array()
			);
			$this->load->view('includes/header', $data);
			$this->load->view('products/products_view');
			$this->load->view('includes/footer');
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}
}

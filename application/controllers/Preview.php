<?php
class Preview extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Page/Services_model', 'Services_model');
	}

	public function index() {
		$services = $this->Services_model->get_services();
		$data = array(
			"business_name" => "Mi Negocio",
			"description" => "Descripción del negocio",
			"whatsapp_number" => "+57 300 000 0000",
			"products" => $services["data"] ?? array()
		);
		$this->load->view('preview/public_view', $data);
	}
}

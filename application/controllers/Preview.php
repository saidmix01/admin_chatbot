<?php
class Preview extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Page/Services_model', 'Services_model');
		$this->load->model('Store/Store_model', 'Store_model');
	}

	public function index($sto_id = null) {
		// If no store ID provided, try to get from session (for logged in users)
		if (!$sto_id) {
			$us_id = $this->session->userdata('us_id');
			if ($us_id) {
				$this->Store_model->data = array("s.us_id" => $us_id);
				$store = $this->Store_model->get_stores();
				if (!empty($store["data"])) {
					$sto_id = $store["data"][0]->sto_id;
				}
			}
		}

		// Get store info
		$store_data = null;
		if ($sto_id) {
			$this->Store_model->data = array("sto_id" => $sto_id);
			$store_result = $this->Store_model->get_stores();
			if (!empty($store_result["data"])) {
				$store_data = $store_result["data"][0];
			}
		}

		// Get products for this store's user
		$products = array();
		if ($store_data && !empty($store_data->us_id)) {
			$this->Services_model->data = array("su.us_id" => $store_data->us_id);
			$services = $this->Services_model->get_service_user();
			$products = $services["data"] ?? array();
		}

		$data = array(
			"business_name" => $store_data->sto_name ?? "Mi Negocio",
			"description" => $store_data->sto_wellcome_message ?? "WhatsApp Business para pequeños negocios",
			"whatsapp_number" => $store_data->sto_phone ?? "+57 300 000 0000",
			"products" => $products,
			"store_id" => $sto_id
		);
		$this->load->view('preview/public_view', $data);
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storefront extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Page/Services_model', 'Services_model');
        $this->load->model('Store/Store_model', 'Store_model');
    }

    /**
     * Main storefront — shows all products for a business
     * Accessed via: store.wapiapp.cloud/{slug}  or  /t/{slug}
     */
    public function index($slug = null) {
        if (!$slug) show_404();

        // Look up store by slug
        $this->Store_model->data = array("s.sto_slug" => $slug);
        $store_result = $this->Store_model->get_stores();

        if (empty($store_result["data"])) {
            show_404();
        }

        $store = $store_result["data"][0];

        // Get products for this store's user
        $products = array();
        if (!empty($store->us_id)) {
            $this->Services_model->data = array("su.us_id" => $store->us_id, "su.su_status" => 1);
            $services = $this->Services_model->get_service_user();
            $products = $services["data"] ?? array();
        }

        $data = array(
            "business_name" => $store->sto_name ?? "Mi Negocio",
            "description" => $store->sto_wellcome_message ?? "",
            "whatsapp_number" => $store->sto_phone ?? "",
            "products" => $products,
            "slug" => $store->sto_slug,
            "store_id" => $store->sto_id,
            "cover" => $store->sto_cover ?? "",
			"logo" => $store->sto_logo ?? ""
        );

        $this->load->view('storefront/public_view', $data);
    }
}

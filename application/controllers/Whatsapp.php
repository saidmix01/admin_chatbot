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

			$us_id = $this->session->userdata('us_id');

			// Load from bot_sessions
			$q = $this->db->query("SELECT * FROM bot_sessions WHERE us_id = " . intval($us_id));
			$session = $q->num_rows() > 0 ? $q->row() : null;

			$qr_status = $session ? ($session->bs_status ?? 'disconnected') : 'disconnected';
			$qr_status = mb_strtolower(trim((string)$qr_status), 'UTF-8');
			$last = $session ? ($session->bs_last_activity ?: ($session->updated_at ?? null)) : null;
			if ($qr_status === 'connected' && $last) {
				$ts = strtotime($last);
				if ($ts !== false && (time() - $ts) > 180) $qr_status = 'disconnected';
			}
			$qr_base64 = $session ? $session->bs_qr_base64 : '';
			$whatsapp_number = $qr_status === 'connected' ? ($session->bs_whatsapp_number ?? '') : '';

			$data = array(
				"title" => "WhatsApp QR",
				"active_menu" => "whatsapp",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"qr_status" => $qr_status,
				"qr_base64" => $qr_base64,
				"whatsapp_number" => $whatsapp_number,
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('whatsapp/qr_view');
			$data_footer = array("scripts" => ["js/general.js"]);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}
}

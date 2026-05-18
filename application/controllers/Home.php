<?php 

class Home extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
	}

	public function index(){
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if($user_data["status"] == false) throw new Exception($user_data["message"], 1);

			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus_result = $this->Menus_profile_model->get_menu_user();

			$data_header = array(
				"title" => "Dashboard",
				"active_menu" => "dashboard",
				"user_data" => $user_data["data"],
				"menus" => $menus_result["data"] ?? array(),
				"scripts" => ["js/dashboard/dashboard.js?v=" . time()],
				"whatsapp_status" => $this->get_whatsapp_status(),
				"whatsapp_number" => $this->get_whatsapp_number(),
				"bot_status" => $this->get_bot_status(),
				"products_count" => $this->get_products_count(),
				"last_activity" => $this->get_last_activity(),
				"plan_info" => $this->get_plan_info()
			);

			$this->load->view('includes/header', $data_header);
			$this->load->view('home/home_view');
			$data_footer = array(); if(isset($data_header['scripts'])) $data_footer['scripts'] = $data_header['scripts']; $this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	private function get_store_for_user($us_id) {
		$store = $this->db->where('us_id', (int)$us_id)->get('stores')->row();
		if ($store) return $store;
		$store = $this->db->query(
			"SELECT s.* FROM user_store us INNER JOIN stores s ON s.sto_id = us.sto_id WHERE us.us_id = ? ORDER BY us.created_at DESC LIMIT 1",
			[(int)$us_id]
		)->row();
		return $store;
	}

	private function get_plan_info() {
		$us_id = (int)$this->session->userdata('us_id');
		$store = $this->get_store_for_user($us_id);
		if (!$store) return null;
		$start = property_exists($store, 'sto_plan_start') ? $store->sto_plan_start : null;
		$end = property_exists($store, 'sto_plan_end') ? $store->sto_plan_end : null;
		$days_left = null;
		$expires_soon = false;
		if ($end) {
			$now = new DateTime('today');
			$endDt = new DateTime($end);
			$diff = (int)$now->diff($endDt)->format('%r%a');
			$days_left = $diff;
			$expires_soon = $diff <= 7;
		}
		return array(
			"plan_start" => $start,
			"plan_end" => $end,
			"plan_days_left" => $days_left,
			"plan_expires_soon" => $expires_soon
		);
	}

	private function get_whatsapp_status() {
		$q = $this->db->query("SELECT bs_status, bs_last_activity, updated_at FROM bot_sessions WHERE us_id = " . intval($this->session->userdata('us_id')));
		if ($q->num_rows() === 0) return 'disconnected';

		$row = $q->row();
		$status = mb_strtolower(trim((string)($row->bs_status ?? 'disconnected')), 'UTF-8');
		$last = $row->bs_last_activity ?: ($row->updated_at ?? null);
		if ($status === 'connected' && $last) {
			$ts = strtotime($last);
			if ($ts !== false && (time() - $ts) > 180) return 'disconnected';
		}
		return $status;
	}

	private function get_whatsapp_number() {
		$q = $this->db->query("SELECT bs_status, bs_whatsapp_number, bs_last_activity, updated_at FROM bot_sessions WHERE us_id = " . intval($this->session->userdata('us_id')));
		if ($q->num_rows() === 0) return '';

		$row = $q->row();
		$status = mb_strtolower(trim((string)($row->bs_status ?? 'disconnected')), 'UTF-8');
		$last = $row->bs_last_activity ?: ($row->updated_at ?? null);
		if ($status === 'connected' && $last) {
			$ts = strtotime($last);
			if ($ts !== false && (time() - $ts) > 180) return '';
		}
		return $row->bs_whatsapp_number ?: '';
	}

	private function get_bot_status() {
		$status = $this->get_whatsapp_status();
		return $status === 'connected' ? 'active' : 'inactive';
	}

	private function get_products_count() {
		$this->load->model('Page/Services_model', 'Services_model');
		$this->Services_model->data = array("su.us_id" => $this->session->userdata('us_id'));
		$services = $this->Services_model->get_service_user();
		return count($services["data"] ?? []);
	}

	private function get_last_activity() {
		$q = $this->db->query("SELECT bs_last_activity FROM bot_sessions WHERE us_id = " . intval($this->session->userdata('us_id')));
		if ($q->num_rows() > 0 && $q->row()->bs_last_activity) {
			return $q->row()->bs_last_activity;
		}
		return '';
	}
}

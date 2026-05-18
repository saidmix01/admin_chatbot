<?php
class Business extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		$this->load->model('Store/Store_model', 'Store_model');
	}

	private function slugify($s) {
		$s = mb_strtolower(trim((string)$s));
		$s = preg_replace('/[^a-z0-9]+/u', '-', $s);
		$s = trim($s, '-');
		return $s ?: 'store';
	}

	private function ensure_upload_dir($public_dir) {
		$public_dir = trim((string)$public_dir);
		$public_dir = trim($public_dir, '/');
		if ($public_dir === '') throw new Exception('Upload path inválido', 1);
		$abs = rtrim(FCPATH, '/') . '/' . $public_dir . '/';
		if (!is_dir($abs)) {
			@mkdir($abs, 0755, true);
		}
		if (!is_dir($abs) || !is_writable($abs)) {
			throw new Exception('No hay permisos de escritura en: ' . $abs, 1);
		}
		return $abs;
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			// Load store data
			$this->Store_model->data = array("s.us_id" => $this->session->userdata('us_id'));
			$store = $this->Store_model->get_stores();
			$store_data = !empty($store["data"]) ? $store["data"][0] : null;

			$data = array(
				"title" => "Mi Negocio",
				"active_menu" => "negocio",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"store" => $store_data,
				"scripts" => ["js/general.js"]
			);
			$this->load->view('includes/header', $data);
			$this->load->view('business/config_view');
			$data_footer = array("scripts" => ["js/general.js"]);
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function save() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			if (empty($input)) {
				$input = array(
					"business_name" => $this->input->post('business_name'),
					"description" => $this->input->post('description'),
					"address" => $this->input->post('address'),
					"whatsapp" => $this->input->post('whatsapp')
				);
			}

			$us_id = $this->session->userdata('us_id');

			$this->Store_model->data = array("s.us_id" => $us_id);
			$store = $this->Store_model->get_stores();
			$store_row = !empty($store["data"]) ? $store["data"][0] : null;

			$update = array();
			if (!empty($input["business_name"])) $update["sto_name"] = $input["business_name"];
			if (!empty($input["description"])) {
				if ($this->db->field_exists('sto_description', 'stores')) {
					$update["sto_description"] = $input["description"];
				} else {
					$update["sto_wellcome_message"] = $input["description"];
				}
			}
			if (!empty($input["address"])) $update["sto_direction"] = $input["address"];
			if (!empty($input["whatsapp"])) $update["sto_phone"] = $input["whatsapp"];

			$sto_id = null;
			if ($store_row) {
				$sto_id = $store_row->sto_id;
			} else {
				$this->db->insert("stores", array_merge($update, array(
					"us_id" => $us_id,
					"sto_status" => 1,
					"sto_name" => !empty($update["sto_name"]) ? $update["sto_name"] : "Mi Negocio"
				)));
				$sto_id = $this->db->insert_id();
				$this->db->insert("user_store", array("us_id" => $us_id, "sto_id" => $sto_id));
				$store_row = $this->db->where("sto_id", $sto_id)->get("stores")->row();
			}

			if ($sto_id && $this->db->field_exists('sto_slug', 'stores')) {
				$current_slug = $store_row->sto_slug ?? '';
				if (!$current_slug) {
					$base_slug = $this->slugify($update["sto_name"] ?? ($store_row->sto_name ?? 'store'));
					$update["sto_slug"] = $base_slug . '-' . $sto_id;
				}
			}

			$media_update = array();
			$this->load->library('upload');

			if (!empty($_FILES['profile_image']['name']) && $this->db->field_exists('sto_logo', 'stores')) {
				$config['upload_path'] = $this->ensure_upload_dir('uploads/logos');
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['max_size'] = 2048;
				$config['encrypt_name'] = true;
				$this->upload->initialize($config);
				if ($this->upload->do_upload('profile_image')) {
					$media_update["sto_logo"] = 'uploads/logos/' . $this->upload->data('file_name');
				} else {
					throw new Exception($this->upload->display_errors('', ''), 1);
				}
			}

			if (!empty($_FILES['cover_image']['name']) && $this->db->field_exists('sto_cover', 'stores')) {
				$config['upload_path'] = $this->ensure_upload_dir('uploads/covers');
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['max_size'] = 6144;
				$config['encrypt_name'] = true;
				$this->upload->initialize($config);
				if ($this->upload->do_upload('cover_image')) {
					$media_update["sto_cover"] = 'uploads/covers/' . $this->upload->data('file_name');
				} else {
					throw new Exception($this->upload->display_errors('', ''), 1);
				}
			}

			$final_update = array_merge($update, $media_update);
			if (!empty($final_update)) {
				$this->db->where("sto_id", $sto_id);
				$this->db->update("stores", $final_update);
				$err = $this->db->error();
				if (!empty($err["code"])) {
					throw new Exception($err["message"], 1);
				}
			}

			$response["status"] = true;
			$response["message"] = "Negocio actualizado correctamente";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

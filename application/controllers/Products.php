<?php
class Products extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->helper('website_helper');
		$this->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
		$this->load->model('Page/Services_model', 'Services_model');
	}

	public function index() {
		try {
			if(!validate_session()) throw new Exception("Unauthenticated", 1);
			$user_data = get_user_content(array("us_id" => $this->session->userdata('us_id')));
			$this->Menus_profile_model->data = array("us_id" => $this->session->userdata('us_id'));
			$menus = $this->Menus_profile_model->get_menu_user();

			$this->Services_model->data = array("su.us_id" => $this->session->userdata('us_id'));
			$services = $this->Services_model->get_service_user();

			$data = array(
				"title" => "Productos",
				"active_menu" => "productos",
				"user_data" => $user_data["data"],
				"menus" => $menus["data"] ?? array(),
				"products" => $services["data"] ?? array()
			);
			$this->load->view('includes/header', $data);
			$this->load->view('products/products_view');
			$data_footer = array();
			if(isset($data_header['scripts'])) $data_footer['scripts'] = $data_header['scripts'];
			$this->load->view('includes/footer', $data_footer);
		} catch (\Throwable $th) {
			$this->load->view('error_pages/500');
		}
	}

	public function save() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);

			$data = array(
				"ser_name" => $this->input->post('name'),
				"ser_price" => $this->input->post('price') ?: 0,
				"ser_description" => $this->input->post('description') ?: '',
				"ser_type" => $this->input->post('type') ?: 'producto',
				"ser_category" => $this->input->post('category') ?: '',
				"ser_status" => $this->input->post('available') ? 1 : 0
			);

			if (empty($data["ser_name"])) throw new Exception("El nombre es requerido", 1);

			if (!empty($_FILES['image']['name'])) {
				$config['upload_path'] = './uploads/products/';
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['max_size'] = 2048;
				$config['encrypt_name'] = true;
				if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0755, true);
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('image')) {
					$data["ser_imagen"] = 'uploads/products/' . $this->upload->data('file_name');
				} else {
					throw new Exception($this->upload->display_errors('', ''), 1);
				}
			}

			$this->db->insert("services", $data);
			$ser_id = $this->db->insert_id();

			$this->db->insert("service_user", array(
				"us_id" => $this->session->userdata('us_id'),
				"ser_id" => $ser_id
			));

			$response["status"] = true;
			$response["message"] = "Producto creado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function get() {
		$response = array("status" => false, "data" => null);
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			$ser_id = $input["ser_id"] ?? null;
			if (empty($ser_id)) throw new Exception("ID requerido", 1);

			$q = $this->db->query("SELECT s.* FROM services s
				INNER JOIN service_user su ON su.ser_id = s.ser_id
				WHERE s.ser_id = " . intval($ser_id) . "
				AND su.us_id = " . intval($this->session->userdata('us_id')));

			if ($q->num_rows() > 0) {
				$response["status"] = true;
				$response["data"] = $q->row();
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function update() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$ser_id = $this->input->post('ser_id');
			if (empty($ser_id)) throw new Exception("ID requerido", 1);

			$update = array(
				"ser_name" => $this->input->post('name'),
				"ser_price" => $this->input->post('price') ?: 0,
				"ser_description" => $this->input->post('description') ?: '',
				"ser_type" => $this->input->post('type') ?: 'producto',
				"ser_category" => $this->input->post('category') ?: '',
				"ser_status" => $this->input->post('available') ? 1 : 0
			);

			if (!empty($_FILES['image']['name'])) {
				$config['upload_path'] = './uploads/products/';
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['max_size'] = 2048;
				$config['encrypt_name'] = true;
				if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0755, true);
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('image')) {
					$update["ser_imagen"] = 'uploads/products/' . $this->upload->data('file_name');
				}
			}

			$this->db->where("ser_id", $ser_id);
			$this->db->update("services", $update);

			$response["status"] = true;
			$response["message"] = "Producto actualizado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}

	public function delete() {
		$response = array("status" => false, "message" => "");
		try {
			if (!validate_session()) throw new Exception("Unauthorized", 1);
			$input = json_decode(file_get_contents("php://input"), true);
			$ser_id = $input["ser_id"] ?? null;
			if (empty($ser_id)) throw new Exception("ID requerido", 1);

			$this->db->where("ser_id", $ser_id);
			$this->db->delete("services");

			$response["status"] = true;
			$response["message"] = "Producto eliminado";
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		echo json_encode($response);
	}
}

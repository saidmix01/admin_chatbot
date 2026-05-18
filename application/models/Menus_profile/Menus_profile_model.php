<?php

class Menus_profile_model extends CI_Model 
{

	protected $table_db = "menu_profile";
	protected $table_db_profiles = "profiles";
	protected $table_db_users = "users";
	protected $table_db_menus = "menus";

	public function get_menu_profile() {
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			$where = "";
			if (!empty($this->data)) {
				$conditions = array();
				foreach ($this->data as $key => $value) {
					$conditions[] = "$key = " . $this->db->escape($value) . "";
				}
				$where = " WHERE " . implode(" AND ", $conditions);
			}
	
			$sql = "SELECT * FROM {$this->table_db} $where";
			$query = $this->db->query($sql);
	
			if ($query) {
				if ($query->num_rows() > 0) {
					$response["status"] = true;
					$response["data"] = $query->row();
				}
			}
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
			$response["message"] = $th->getMessage();
		}
	
		return $response;
	}

	public function get_menu_user() {
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if (!empty($this->data) && isset($this->data['us_id'])) {
				$user = $this->db->where('us_id', (int)$this->data['us_id'])->get($this->table_db_users)->row();
				if ($user && (int)($user->pro_id ?? 0) === 1) {
					$has = $this->db->where('pro_id', 1)->limit(1)->get($this->table_db)->num_rows() > 0;
					if (!$has) {
						$this->db->query(
							"INSERT INTO {$this->table_db} (pro_id, men_id)
							 SELECT 1, m.men_id
							 FROM {$this->table_db_menus} m
							 WHERE m.men_status = 1
							 ON CONFLICT (pro_id, men_id) DO NOTHING"
						);
					}
				}
			}

			// $this->db->cache_on();
			$where = "";
			if (!empty($this->data)) {
				$conditions = array();
				foreach ($this->data as $key => $value) {
					$conditions[] = "$key = " . $this->db->escape($value) . "";
				}
				$where = "WHERE " . implode(" AND ", $conditions);
			}
			$sql = "SELECT m.men_id, m.men_description, m.men_status, m.men_url, m.men_icon FROM {$this->table_db} a
					INNER JOIN {$this->table_db_profiles} b
					ON a.pro_id = b.pro_id 
					INNER JOIN {$this->table_db_users} u ON u.pro_id = b.pro_id 
					INNER JOIN {$this->table_db_menus} m ON m.men_id = a.men_id AND m.men_status = 1 $where";
			$query = $this->db->query($sql);
			
			if ($query) {
				$response["status"] = true;
				if ($query->num_rows() > 0) {
					$response["data"] = $query->result();
				}
			}
			// $this->db->cache_off();
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
			$response["message"] = $th->getMessage();
		}
	
		return $response;
	}

	public function validate_menu_profile() {
		$response = array(
			"status" => false,
			"exits" => false,
			"message" => ""
		);
		try {
			// $this->db->cache_on();
			$where = "";
			if (!empty($this->data)) {
				$conditions = array();
				foreach ($this->data as $key => $value) {
					$conditions[] = "$key = " . $this->db->escape($value) . "";
				}
				$where = " WHERE " . implode(" AND ", $conditions);
			}
			$sql = "SELECT * FROM {$this->table_db} $where";
			$query = $this->db->query($sql);
			
			if ($query) {
				$response["status"] = true;
				if ($query->num_rows() > 0) {
					$response["exits"] = true;
				}
			}
			// $this->db->cache_off();
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
			$response["message"] = $th->getMessage();
		}
	
		return $response;
	}
	
}


?>

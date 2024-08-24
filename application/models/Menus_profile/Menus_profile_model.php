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
				$where = "WHERE " . implode(" AND ", $conditions);
			}
	
			$sql = "SELECT * FROM {$this->table_db} $where";
			$query = $this->db->query($sql);
	
			if ($query) {
				$response["status"] = true;
				if ($query->num_rows() > 0) {
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
					INNER JOIN {$this->table_db_menus} m ON m.men_id = a.men_id $where";
			$query = $this->db->query($sql);
			
			if ($query) {
				$response["status"] = true;
				if ($query->num_rows() > 0) {
					$response["data"] = $query->result();
				}
			}
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
			$response["message"] = $th->getMessage();
		}
	
		return $response;
	}
	
}


?>

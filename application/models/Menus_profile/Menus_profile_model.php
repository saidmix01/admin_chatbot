<?php

class Menus_profile_model extends CI_Model 
{

	protected $table_db = "menu_profile";

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
	
}


?>

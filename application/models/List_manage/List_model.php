<?php

class List_model extends CI_Model 
{
	protected $table_name = "lists";
	protected $table_name_options = "options";

	public function get_lists(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			$where = "";
			if(!empty($this->data)){
				$where = "WHERE ";
				foreach ($this->data as $key => $value) {
					$where .= $key . " = '" . $value . "' AND ";
				}
				$where = explode(' ', $where);
				array_pop($where);
				$where = implode(' ', $where);
				$where = rtrim($where, 'AND');
			}
			$sql = "SELECT * FROM {$this->table_name} {$where}";
			if($query = $this->db->query($sql)){
				$response["status"] = true;
				if($query->num_rows() > 0){
					$response["data"] = $query->result();
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}

	public function get_options(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			$where = "";
			if(!empty($this->data)){
				$where = "WHERE ";
				foreach ($this->data as $key => $value) {
					$where .= $key . " = '" . $value . "' AND ";
				}
				$where = explode(' ', $where);
				array_pop($where);
				$where = implode(' ', $where);
				$where = rtrim($where, 'AND');
			}
			$sql = "SELECT * FROM {$this->table_name_options} {$where}";
			if($query = $this->db->query($sql)){
				$response["status"] = true;
				if($query->num_rows() > 0){
					$response["data"] = $query->result();
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}
}


?>

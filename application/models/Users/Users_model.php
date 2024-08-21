<?php

class Users_model extends CI_Model 
{
	protected $table_name = "users";


	public function get_user_info(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if(empty($this->us_id)) throw new Exception("us_id is empty", 1);
			$sql = "SELECT * FROM {$this->table_name} WHERE us_id = {$this->us_id}";
			
			if($query = $this->db->query($sql)){
				if($query->num_rows() > 0){
					$response["status"] = true;
					$response["data"] = $query->row();
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}


	public function get_users(){
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
					$where .= $key . " = '" . $value . "' AND";
				}
				$where = explode(' ', $where);
				array_pop($where);
				$where = implode(' ', $where);
			}
			$sql = "SELECT * FROM {$this->table_name} u
					INNER JOIN profiles p ON p.pro_id = u.pro_id
				   {$where}";
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

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
}


?>

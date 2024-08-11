<?php

class Login_Model extends CI_Model
{
	protected $db_table_users = "users";

	public function get_user_data(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if(empty($this->us_email) || empty($this->us_password)){
				throw new Exception("Email or password is empty", 1);
			}
			$sql = "SELECT us_id, us_status,us_name,us_email,us_password
						FROM {$this->db_table_users}
						WHERE us_email = '{$this->us_email}'";
			if($result = $this->db->query($sql)){
				if($result && count($result->result()) > 0){
					$response["status"] = true;
					$response["data"] = $result->row();
				}else{
					$response["status"] = true;
					$response["message"] = "User {$this->us_email} no exists";
				}
			}else{
				$response["status"] = false;
				$response["message"] = "Error in Database";
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}
}


?>

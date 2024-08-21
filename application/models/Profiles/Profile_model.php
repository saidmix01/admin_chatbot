<?php 

class Profile_model extends CI_Model
{

	protected $table_db = "profiles";

	public function get_profiles(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			$sql = "SELECT * FROM {$this->table_db} WHERE pro_status = 1";
			if($query = $this->db->query($sql)){
				$response["status"] = true;
				if($query->num_rows() > 0){
					$response["data"] = $query->result();
				}
			}else{
				throw new Exception("Error Processing Query", 1);
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}
}


?>

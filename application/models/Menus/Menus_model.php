<?php 

class Menus_model extends CI_Model 
{

	protected $table_name = "menus";

	public function get_menus(){
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
}


?>

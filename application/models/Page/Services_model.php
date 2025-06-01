<?php

class Services_model extends CI_Model 
{
    protected $table_services = "services";
	protected $table_service_user = "service_user";


	public function get_service_user(){
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
			$sql = "SELECT * FROM {$this->table_service_user} su
					INNER JOIN {$this->table_services} s ON s.ser_id = su.ser_id
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

    public function get_services(){
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
			$sql = "SELECT * FROM {$this->table_services} s
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
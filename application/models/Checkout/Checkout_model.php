<?php

class Checkout_model extends CI_Model
{
    private $table_payment_log = "payment_log";

    public function get_payment_log(){
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
			$sql = "SELECT * FROM {$this->table_payment_log} s
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
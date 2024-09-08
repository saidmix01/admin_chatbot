<?php

class Chat_model extends CI_Model 
{
	protected $table_name = "chat_questions";
	protected $table_name_list = "lists";
	protected $table_name_options = "options";

	public function get_questions(){
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
			$sql = "SELECT * FROM {$this->table_name} cq
					LEFT JOIN {$this->table_name_list} l
					ON cq.chq_type = l.lis_id
					{$where}
					ORDER BY chq_order ASC";
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

	public function get_list_by_question(){
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
			$sql = "SELECT * FROM {$this->table_name} cq
					LEFT JOIN {$this->table_name_list} l
					ON cq.chq_type = l.lis_id
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
			$sql = "SELECT * FROM {$this->table_name_options} o
					LEFT JOIN {$this->table_name_list} l
					ON o.lis_id = l.lis_id
					{$where}
					ORDER BY o.opt_order ASC";
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

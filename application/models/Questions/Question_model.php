<?php

class Question_model extends CI_Model
{
    protected $table_question = "questions";
	protected $table_answer = "answer";


    
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
			$sql = "SELECT * FROM {$this->table_question} s
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

	public function get_answers() {
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
			$sql = "SELECT * FROM {$this->table_answer} s
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

	
	public function get_order_answer(){
		$response = array(
			"status" => false,
			"data" => null,
			"message" => ""
		);
	
		try {
			if (empty($this->que_id)) {
				throw new Exception("The question id is empty", 1);
			}
	
			$sql = "SELECT COALESCE(MAX(ans_order), 0) + 1 AS next_order
					FROM answer
					WHERE que_id = {$this->que_id}";
	
			if ($query = $this->db->query($sql)) {
				$response["status"] = true;
				if ($query->num_rows() > 0) {
					$row = $query->row(); // objeto con propiedad next_order
					$response["data"] = (int) $row->next_order; // solo el valor
				}
			}
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
	
		return $response;
	}
	
}


?>

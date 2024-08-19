<?php

/* The `General_Model` class in PHP provides methods for inserting, updating, and deleting data in a
database table with error handling and response messages. */
class General_Model extends CI_Model 
{

	/**
	 * The insert function attempts to insert data into a specified table in a database and returns a
	 * response indicating success or failure along with any relevant message.
	 * 
	 * @return The `insert()` function returns an array with keys "status", "data", and "message". The
	 * "status" key indicates whether the insertion was successful or not, the "data" key contains the
	 * inserted data or the insert ID, and the "message" key contains any error message if an exception
	 * occurred during the insertion process.
	 */
	public function insert(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if(empty($this->table_name)) throw new Exception("Database Error: table name is empty", 1);
			if(empty($this->data)) throw new Exception("Database Error: data is empty", 1);
			if(!$this->db->insert($this->table_name,$this->data)) throw new Exception("Database Error: Insert Error", 1);
			$response["status"] = true;
			$response["data"] = $this->db->insert_id();
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}

	/**
	 * The function `update` in PHP attempts to update data in a database table and returns a response
	 * indicating success or failure along with relevant messages.
	 * 
	 * @return The `update()` function is returning an array with the following structure:
	 * ```php
	 *  = array(
	 * 	"status" => false, // Boolean indicating the status of the update operation
	 * 	"data" => array(), // Array to hold any data related to the update operation
	 * 	"message" => "" // String message that may contain an error message if an exception is caught
	 * );
	 * ```
	 */
	public function update(){
		$response = array(
			"status" => false,
			"data" => array(),
			"message" => ""
		);
		try {
			if(empty($this->table_name)) throw new Exception("Database Error: table name is empty", 1);
			if(empty($this->data)) throw new Exception("Database Error: data is empty", 1);
			if(empty($this->where)) throw new Exception("Database Error: Where is empty", 1);

			if(!$this->db->update($this->table_name, $this->data, $this->where)) throw new Exception("Database Error: Update Error", 1);
			$response["status"] = true;
			$response["data"] = $this->db->insert_id();
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}

	/**
	 * The function attempts to delete records from a database table based on a specified condition and
	 * returns a response indicating the success or failure of the operation.
	 * 
	 * @return The `delete()` function is returning an array with two keys: "status" and "message". The
	 * "status" key indicates whether the deletion was successful (true) or not (false). The "message" key
	 * contains any error message if an exception is caught during the deletion process.
	 */
	public function delete(){
		$response = array(
			"status" => false,
			"message" => ""
		);
		try {
			if(empty($this->table_name)) throw new Exception("Database Error: table name is empty", 1);
			if(empty($this->where)) throw new Exception("Database Error: Where is empty", 1);

			if(!$this->db->delete($this->table_name, $this->where)) throw new Exception("Database Error: Delete Error", 1);
			$response["status"] = true;
		} catch (\Throwable $th) {
			$response["message"] = $th->getMessage();
		}
		return $response;
	}

}


?>

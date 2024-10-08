<?php
/* The code block you provided is a PHP function named `get_user_content`. This function is defined
within a conditional block that checks if the function does not already exist. If the function does
not exist, it is defined with the following structure: */
if (!function_exists('get_user_content')) {
	function get_user_content($us_id = "")
	{
		$response = array();
		try {
			if (empty($us_id)) throw new Exception("User data is empty", 1);
			$CI = &get_instance();
			$CI->load->model('Users/Users_model', 'Users_model');
			$CI->Users_model->us_id = $us_id["us_id"];
			$response = $CI->Users_model->get_user_info();
		} catch (\Throwable $th) {
			echo "<pre>";
			print_r($th);
			echo "</pre>";
		}
		return $response;
	}
}


/* The code block you provided is checking if a PHP function named `get_user_menus` does not already
exist using the `function_exists` function. If the function does not exist, it defines the
`get_user_menus` function. */
if (!function_exists('get_user_menus')) {
	function get_user_menus($us_id = "")
	{
		$response = array();
		try {
			if (empty($us_id)) throw new Exception("User data is empty", 1);
			$CI = &get_instance();
			$CI->db->cache_on();
			$CI->load->model('Menus_profile/Menus_profile_model', 'Menus_profile_model');
			$CI->Menus_profile_model->data = array("us_id" => $us_id["us_id"]);
			$response = $CI->Menus_profile_model->get_menu_user();
		} catch (\Throwable $th) {
			echo "<pre>";
			print_r($th);
			echo "</pre>";
		}
		return $response;
	}
}

/* The code block you provided is checking if a PHP function named `validate_session` does not already
exist using the `function_exists` function. If the function does not exist, it defines the
`validate_session` function. */
if (!function_exists('validate_session')) {
	function validate_session()
	{
		$response = false;
		try {
			$CI = &get_instance();

			if (!$CI->session->userdata('login')) {
				$CI->session->sess_destroy();
				redirect(base_url());
			} else {
				$response = true;
			}
		} catch (Exception $e) {
			echo "<pre>";
			print_r($e);
			echo "</pre>";
		}
		return $response;
	}
}

if (!function_exists('validate_store_user')) {
	function validate_store_user($us_id = "", $sto_id = "")
	{
		$response = array("status"=>false,"data"=>false,"message"=>"");
		try {
			if (empty($us_id)) throw new Exception("User data is empty", 1);
			if (empty($sto_id)) throw new Exception("Store data is empty", 1);
			$CI = &get_instance();
			$CI->db->cache_on();
			$CI->load->model('Store/Store_model', 'Store_model');
			$CI->Store_model->data = array("u.us_id" => $us_id, "s.sto_id" => $sto_id);
			$data = $CI->Store_model->get_stores();
			if($data["status"] == false) throw new Exception($data["message"], 1);
			if(count($data["data"]) > 0){
				$response["status"] = true;
				$response["data"] = true;
			} 
		} catch (\Throwable $th) {
			$response["message"] = $th;
		}
		return $response;
	}
}

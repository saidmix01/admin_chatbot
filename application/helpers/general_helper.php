<?php
if (!function_exists('get_user_content')) {
    function get_user_content($us_id = "") {
		$response = array();
       try {
		if(empty($us_id)) throw new Exception("User data is empty", 1);
		$CI =& get_instance();
		$CI->load->model('Users/Users_model','Users_model');
		$CI->Users_model->us_id = $us_id["us_id"];
		$response = $CI->Users_model->get_user_info();
	   } catch (\Throwable $th) {
		echo "<pre>"; print_r($th); echo "</pre>";
	   }
	   return $response;
    }
}

if (!function_exists('validate_session')) {
    function validate_session() {
        $response = false;
        try {
            $CI =& get_instance();
            
            if (!$CI->session->userdata('login')) {
                $CI->session->sess_destroy();
                redirect(base_url());
            } else {
                $response = true;
            }
        } catch (Exception $e) {
            echo "<pre>"; print_r($e); echo "</pre>";
        }
        return $response;
    }
}
?>

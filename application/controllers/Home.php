<?php 

class Home extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		$this->load->helper('general_helper');
	}

	/**
	 * The index function in PHP checks for a valid session, retrieves user data and menus, and loads
	 * corresponding views, handling exceptions if any occur.
	 */
	public function index(){
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if($user_data["status"] == false) throw new Exception($user_data["message"], 1);
			//Data header
			$data_header["user_data"] = $user_data["data"];
			$data_header["menus"] = get_user_menus(array("us_id" => $this->session->userdata('us_id')))["data"];
			// Data footer
			$data_footer["scripts"] = [
				"js/dashboard/dashboard.js"
			];
			$this->load->view('includes/header',$data_header);
			$this->load->view('home/home_view');
			$this->load->view('includes/footer',$data_footer);
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
		}
	}
}


?>

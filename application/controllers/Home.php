<?php 

class Home extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		$this->load->helper('general_helper');
	}

	public function index(){
		try {
			if(!validate_session()) throw new Exception("The unauthenticated user", 1);
			$user_content["us_id"] = $this->session->userdata('us_id');
			$user_data = get_user_content($user_content);
			if($user_data["status"] == false) throw new Exception($user_data["message"], 1);
			//Dats header
			$data_header["user_data"] = $user_data["data"];
			$this->load->view('includes/header',$data_header);
			$this->load->view('home/home_view');
			$this->load->view('includes/footer');
		} catch (\Throwable $th) {
			echo "<pre>"; print_r($th); echo "</pre>";
		}
	}
}


?>

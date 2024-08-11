<?php 

class Home extends CI_Controller
{
	public function __construct() {
		parent::__construct();
	}

	public function index(){
		$this->load->view('includes/header');
		$this->load->view('home/home_view');
		$this->load->view('includes/footer');
	}
}


?>

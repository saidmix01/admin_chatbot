<?php

class Api extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		//Helpers
		$this->load->helper('general_helper');
	}

	public function index(){
		$this->load->view('error_pages/404');
	}
}


?>

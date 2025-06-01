<?php

class Page extends CI_Controller 
{
    public function __construct() {
		parent::__construct();
        $this->load->helper('website_helper');
        //Load Models
        $this->load->model('Page/Services_model','Services_model');
        //Libraries
        $this->load->library('Email_helper');
	}

    public function index(){
        $data_header['site_name'] = SITE_NAME;
        $data_footer["scripts"] = [
            "js/page/general.js",
            "js/page/send_mail.js"
		];
        //Load html content
        $this->load->view('landing_page/includes/head',$data_header);
        $this->load->view('landing_page/includes/banner',$data_header);
        $this->load->view('landing_page/index');
        $this->load->view('landing_page/includes/modal_login');
        $this->load->view('landing_page/includes/footer',$data_footer);
    }

    public function servicios(){
        $data_header['site_name'] = SITE_NAME;
        $data_footer["scripts"] = [
			"js/page/services.js",
            "js/page/general.js"
		];
        $this->load->view('landing_page/includes/head',$data_header);
        $this->load->view('landing_page/services');
        $this->load->view('landing_page/includes/modal_login');
        $this->load->view('landing_page/includes/footer',$data_footer);
    }

    public function nosotros(){
        $data_header['site_name'] = SITE_NAME;
        $this->load->view('landing_page/includes/head',$data_header);
        $this->load->view('landing_page/us');
        $this->load->view('landing_page/includes/modal_login');
        $this->load->view('landing_page/includes/footer');
    }

    public function faq(){
        $data_header['site_name'] = SITE_NAME;
        $this->load->view('landing_page/includes/head',$data_header);
        $this->load->view('landing_page/faq');
        $this->load->view('landing_page/includes/modal_login');
        $this->load->view('landing_page/includes/footer');
    }

    public function legal(){
        $data_header['site_name'] = SITE_NAME;
        $this->load->view('landing_page/includes/head',$data_header);
        $this->load->view('landing_page/legal');
        $this->load->view('landing_page/includes/modal_login');
        $this->load->view('landing_page/includes/footer');
    }

    public function contacto(){
        $data_header['site_name'] = SITE_NAME;
        $this->load->view('landing_page/includes/head',$data_header);
        $this->load->view('landing_page/contact');
        $this->load->view('landing_page/includes/modal_login');
        $this->load->view('landing_page/includes/footer');
    }

    public function get_services(){
        $response = array(
            "status" => false,
            "data" => array(),
            "message" => ""
        );
        try {
            header("Content-Type: application/json; charset=UTF-8");
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$input = file_get_contents("php://input");
				$data = json_decode($input, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					$this->Services_model->data = $data;
					$data_response = $this->Services_model->get_services();
					if (!$data_response["status"]) throw new Exception($data_response["message"], 1);
					$response["status"] = true;
					$response["data"] = $data_response["data"];
					$response["message"] = "Query executed correctly";
				}
			}
        } catch (\Throwable $th) {
            http_response_code(500);
            $response['message'] = $th->getMessage();
        }
        echo json_encode($response);
    }

    public function send_message_contact(){
        $response = array(
            "status" => false,
            "message" => ""
        );
        try {
            if (empty($this->input->POST())) throw new Exception("There is empty data", 1);
            $data = $this->input->POST();
            $data_email = 
            [
                "name"    => $data["name"],
                "email"   => $data["email"],
                "message" => $data["message"],
            ];

            $data_view_mail = $this->load->view('landing_page/email/template_contact', $data_email, true);
            $email_send = $this->email_helper->send_mail(MAIL_CONTACT, 'Contacto desde la web', $data_view_mail);
            if(!$email_send) throw new Exception("Error sending mail", 1);
            $response["status"] = true;
        } catch (\Throwable $th) {
            http_response_code(500);
            $response["message"] = $th->getMessage();
        }
        echo json_encode($response);
    }
}


?>
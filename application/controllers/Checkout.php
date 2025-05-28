<?php 

class Checkout extends CI_Controller
{
    private $config_wompi;
    public function __construct()
	{
		parent::__construct();
		//Models
		$this->load->model('General_Model/General_Model', 'General_Model');
		$this->load->model('Page/Services_model','Services_model');
        $this->load->model('Users/Users_model','Users_model');
        $this->load->model('Checkout/Checkout_model','Checkout_model');
        //Libraries
        $this->load->library('Email_helper');
		//Helpers   
		$this->load->helper('general_helper');
        $this->load->helper('website_helper');
        //Config wompi
        $this->config_wompi = get_wompi_config();
	}

    public function index(){
        try {
			if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                //data header
                $data_header['site_name'] = SITE_NAME;
				if(isset($_GET['service'])){
                    $service = $_GET['service'];
                    $this->Services_model->data = array("ser_id"=>$service);
                    $data_sevice = $this->Services_model->get_services();
                    if($data_sevice["status"]){
                        //data view
                        $data_view['data_service'] = $data_sevice["data"];
                        //data foter
                        $data_footer["scripts"] = [
                            "js/page/services.js",
                            "js/page/general.js",
                            "js/page/checkout.js"
                        ];        
                        $this->load->view('landing_page/includes/head',$data_header);
                        $this->load->view('landing_page/checkout',$data_view);
                        $this->load->view('landing_page/includes/modal_login');
                        $this->load->view('landing_page/includes/footer',$data_footer);
                    }
                }else{
                    $this->load->view('landing_page/includes/head',$data_header);
                    $this->load->view('landing_page/includes/modal_login');
                    $this->load->view('landing_page/empty_cart');
                    $this->load->view('landing_page/includes/footer');
                }
			}
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    public function shop_service(){
        try {
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$data = $this->input->POST();
                $ser_id = $data["ser_id"];
                //Validate if user exist
                $this->Users_model->data = array("us_email"=>$data["us_email"]);
                $data_user = $this->Users_model->get_users();
                if(!$data_user["status"]) throw new Exception($data_user["message"], 1);
                $us_id = "";
                foreach ($data_user["data"] as $us) {
                    $us_id = $us->us_id;
                    break;
                }
                if(count($data_user["data"]) == 0){
                    //create user
                    $passwd = generate_password();
                    unset($data["ser_id"]);
                    $data["us_password"] = hash_pass($passwd);
                    $data["us_status"] = 2;
                    $data["pro_id"] = 2;
                    $this->General_Model->table_name = "users";
                    $this->General_Model->data = $data;
                    $data_insert = $this->General_Model->insert();
                    if(!$data_insert["status"]) throw new Exception($data_insert["message"], 1);
                    $us_id = $data_insert["data"];
                    $data["ser_id"] = $ser_id;
                    $response["user_created"] = true;
                    $data_email = [
                        "password"=>$passwd,
                        "user"=>$data["us_email"]
                    ];

                    $data_view_mail = $this->load->view('landing_page/email/credentials_template', $data_email, true);
                    $email_send = $this->email_helper->send_mail($data["us_email"], 'Credenciales de acceso', $data_view_mail);
                }
                //Validate service
                $this->Services_model->data = array("ser_id"=>$ser_id);
                $data_service = $this->Services_model->get_services();
                if(!$data_service["status"]) throw new Exception($data_service["message"], 1);
                foreach ($data_service["data"] as $service) {
                    $data_service_send = array(
                        "ser_us_status" => 2,
                        "us_id" => $us_id,
                        "ser_id" => $service->ser_id
                    );
                    $this->General_Model->table_name = "service_user";
                    $this->General_Model->data = $data_service_send;
                    $data_insert = $this->General_Model->insert();
                    if(!$data_insert["status"]) throw new Exception($data_insert["message"], 1);

                    //Payment Wompy
                    $reference = "ORD-" . time();
                    $signature = hash('sha256', $reference . $service->ser_price * 100 . 'COP' . $this->config_wompi["integrity_key"]);
                    $data = [
                        'public_key' => $this->config_wompi["public_key"],
                        'amount_in_cents' => $service->ser_price * 100,
                        'reference' => $reference,
                        'currency' => 'COP',
                        'signature' => $signature,
                        'redirect_url' => 'https://ferry-sure-encouraged-intense.trycloudflare.com/admin_chatbot/checkout/confirmacion?reference=' . $reference,
                        'email' => $data['us_email'],
                        'nombre' => $data['us_name'],
                        'telefono' => $data['us_country_code'].$data['us_tel'],
                        'documento' => $data['us_doc_number'],
                    ];
                    //Insert Payment log
                    $data_payment = array(
                        "us_id" => $us_id,
                        "ser_id" => $ser_id,
                        "pay_information" => json_encode(array(
                            "price"=>$service->ser_price,
                            "date"=>date('Y/m/d H:i:s'),
                        )),
                        "pay_client_information" => json_encode(array(
                            "name"=>$data['nombre'],
                            'email' => $data['email'],
                            'document' => $data['documento'],
                        )),
                        "pay_service_information" => json_encode(array(
                            "name"=>$service->ser_name,
                        )),
                        "pay_reference"=> $reference
                    );
                    $this->General_Model->table_name = "payment_log";
                    $this->General_Model->data = $data_payment;
                    $data_insert = $this->General_Model->insert();
                    $this->load->view('landing_page/wompi/go_to_wompi', $data);
                    
                }
            
			}
        } catch (\Throwable $th) {
            
        }
    }

    public function webhook_confirmation()
    {
        try {
            $input = file_get_contents("php://input");
            $data  = json_decode($input, true);

            // Busca en data.transaction
            if (!isset($data['data']['transaction']['id'])) {
                throw new Exception("Payload mal formado: falta data.transaction.id");
            }

            $transaction = $data['data']['transaction'];
            $transaction_id          = $transaction['id'];
            $reference   = $transaction['reference'];
            $status      = $transaction['status'];

            $validate_payment = $this->validate_pay($transaction_id);
            if (!$validate_payment["status"]) {
                throw new Exception($validate_payment["error_message"] ?? "Error validando el pago");
            }

            $payment_status = $validate_payment["transaction_status"];

            // Guardar log del pago
            $this->General_Model->table_name = "payment_log";
            $this->General_Model->data = ["pay_log" => json_encode($validate_payment["transaction_data"])];
            $this->General_Model->where = ["pay_reference" => $reference];
            $data_update = $this->General_Model->update();

            if (!$data_update["status"]) {
                throw new Exception($data_update["message"]);
            }

            // Obtener datos del log para seguir el flujo
            $this->Checkout_model->data = ["pay_reference" => $reference];
            $response_payment_log = $this->Checkout_model->get_payment_log();

            if (!$response_payment_log["status"]) {
                throw new Exception($response_payment_log["message"]);
            }

            // Procesar pagos aprobados
            if ($payment_status === "APPROVED") {
                foreach ($response_payment_log["data"] as $key) {
                    $client_information = json_decode($key->pay_client_information);
                    $service_information = json_decode($key->pay_service_information);
                    $information = json_decode($key->pay_information);

                    $data_email = [
                        "client"       => $client_information->name ?? "Cliente",
                        "duration"     => "1 Mes",
                        "service_name" => $service_information->name ?? "Servicio",
                        "activacion"   => "pending",
                        "price"        => $information->price ?? 0,
                    ];

                    $data_view_mail = $this->load->view('landing_page/email/template_mail', $data_email, true);
                    $email_send = $this->email_helper->send_mail($client_information->email, 'Confirmación de tu compra', $data_view_mail);
                }
            }

            http_response_code(200);
        } catch (\Throwable $th) {
            http_response_code(500);
        }
    }



    public function confirmacion()
    {
        try {
            if(!isset($_GET["reference"])) throw new Exception("Reference is empty", 1);
            if(!isset($_GET["id"])) throw new Exception("Id is empty", 1);
            $reference = $_GET["reference"];
            $id = $_GET["id"];
            if(empty($reference)) throw new Exception("Reference is empty", 1);
            $data_header['site_name'] = SITE_NAME;
            //Validate Payment Status
            $validate_payment = $this->validate_pay($id);
            if(!$validate_payment["status"]) throw new Exception($validate_payment["error_message"], 1);
            $payment_status = $validate_payment["transaction_status"];
            

            $this->Checkout_model->data = array("pay_reference"=>$reference);
            $response_payment_log = $this->Checkout_model->get_payment_log();
            if(!$response_payment_log["status"]){
                throw new Exception($response_payment_log["message"], 1);
            }
            foreach ($response_payment_log["data"] as $key) {
                if($payment_status === "APPROVED"){
                    $client_information = json_decode($key->pay_client_information);
                    $service_information = json_decode($key->pay_service_information);
    
                    $information = json_decode($key->pay_information);
                    
                    $data_view = array(
                        "reference"=>$reference,
                        "amount"=>$information->price,
                        "status"=>"pending",
                        "payment_status" => $payment_status
                    );
                }else{
                    $data_view = array(
                        "payment_status" => $payment_status
                    );
                }
                $this->load->view('landing_page/includes/head',$data_header);
                $this->load->view('landing_page/includes/modal_login');
                $this->load->view('landing_page/confirmation',$data_view);
                $this->load->view('landing_page/includes/footer');
            }
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    private function validate_pay($transaction_id = "")
    {
        $res = [
            "status" => false,
            "transaction_status" => null,
            "transaction_data" => null,
            "error_message" => null
        ];

        if (empty($transaction_id)) {
            $res['error_message'] = 'ID de transacción no proporcionado.';
            return $res;
        }

        try {
            $url = $this->config_wompi["transaction_url"] . $transaction_id;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($response === false || $http_code !== 200) {
                $res['error_message'] = "Error consultando la API de Wompi: " . ($curl_error ?: "Código HTTP $http_code");
                return $res;
            }

            $data = json_decode($response, true);

            if (!isset($data['data']['status'])) {
                $res['error_message'] = "La respuesta de Wompi no contiene el estado de la transacción.";
                return $res;
            }

            $estado = $data['data']['status'];
            $res['status'] = true;
            $res['transaction_status'] = $estado;
            $res['transaction_data'] = $data['data'];
        } catch (Exception $e) {
            $res['error_message'] = "Excepción capturada: " . $e->getMessage();
        }
        return $res;
    }



}


?>
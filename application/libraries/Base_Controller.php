<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS, PATCH');
    header('Access-Control-Allow-Headers: X-API-KEY,  X-API-TOKEN,Content-Type');
    header('Content-Type: application/json');    
    exit;       
}

class Base_Controller extends REST_Controller{

    public  $data = [];
    public  $auth = false;
    public $unauthorize = array('status' => REST_Controller::HTTP_UNAUTHORIZED,'message' => 'Unauthorized.');
    public $client_error = array('status' => REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
    public $bad_request = array('status' => REST_Controller::HTTP_BAD_REQUEST,'message' => 'Bad request.');
    public $internal = array('status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'message' => 'Internal server error.');
    public $not_found = array('status' => REST_Controller::HTTP_NOT_FOUND,'message' => 'Not found.');
    public $invalid_request = array('status' => REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    public $success = array('status' => REST_Controller::HTTP_OK);

    public $default_client = "mobileapp-client";
    public $default_auth_key = "simplerestapi";
    
    public $videoStorage = DEFAULT_URI."/uploads/";
    public $profileStorage = DEFAULT_URI."/uploads/";


    function __construct()
    {
        parent::__construct($config = 'rest');

        // $this->auth_access();
        $this->models();
        $this->data();
    }

    public function error_html()
    {
        return $this->response_code(401,"");
    }
    public function data()
    {
        return $this->data;
    }

    public function models()
    {
        $this->load->model('Main_mdl');
    }

    public function auth_access()
    {
        $header_client = $this->input->get_request_header('Client-Service', TRUE);
        $header_key  = $this->input->get_request_header('Auth-Key', TRUE);
        
        if($header_client != $this->default_client && $header_key != $this->default_auth_key) return $this->response_return($this->response_code(401,""));
        
        return true;
    }

    public function auth_request(){
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorization', TRUE);

        if(empty($users_id) || empty($token)) return false;
        return true;
    }

    public function response_return($response_code){
        header('Content-Type: application/json');
        echo json_encode($response_code);
    }

    public function response_code($status_code, $message){
        switch($status_code){
            case 200:
                $response =  $this->success["message"] = $message;
                $status = REST_Controller::HTTP_OK;
                break;
            
            case 400:
                $response =  $this->bad_request;
                $status = REST_Controller::HTTP_BAD_REQUEST;
            break;
            
            case 401:
                $response =  $this->unauthorize;
                $status = REST_Controller::HTTP_UNAUTHORIZED;
            break;

            case 403:
                $response =  $this->not_found;
                $status = REST_Controller::HTTP_NOT_FOUND;
                break;

            case 422:
                $response =  $this->client_error["message"] = $message;
                $status = REST_Controller::HTTP_BAD_REQUEST;
                break;

            case 500: 
                $response =  $this->internal;
                $status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                break;

            default:
                $response = $this->internal;
                $status = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;
                break;

        }
        $request_header = $status ;

        if($request_header === 200 || $request_header === 405 || $request_header === 422){
            return array("status" => $request_header, "data" => $response );
        }
        return $response;
    }
    
        /* Upload files */

    public function upload($file,$order_id){
        $valid_ext = array('jpeg', 'jpg', 'png', 'pdf', 'doc', 'docx');
        
        $path = 'uploads/';
        $request = 'record';
        if($file){
            $img = $file['name'];
            $tmp = $file['tmp_name'];
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

            $final_image = $order_id."-".strtolower($order_id.time().'1.'.$ext);

            $config = array(
                'upload_path' => $path,
                'overwrite' => FALSE,
                'max_size' => "30000",
                'file_name' => $final_image
            );

            if(!file_exists($path)) 
            {
                mkdir($path, 0777, true);
            }

            $this->load->library('upload', $config);
            if (in_array($ext, $valid_ext)) {
                if (!$this->upload->do_upload($request)) {
                    $error = array('error' => $this->upload->display_errors());
                    return $error;
                }
        
                return array(
                    'link' => $this->videoStorage.$final_image,
                    'name' => $final_image
                );
            }
        }

        return false;
    }
    
    public function upload_profile($file,$ref_id){
      
        
        $path = 'uploads/';
        $request = 'profile';
        $valid_ext = array('jpeg', 'jpg', 'png', 'gif', 'bmp');

        if($file){
            $img = $file['name'];
            $tmp = $file['tmp_name'];
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));


            $final_image = strtolower($ref_id.time().'1.'.$ext);

            // $config = array(
            //     'upload_path' => $path,
            //     'allowed_types' => "JPEG|JPG|PNG|GIF|gif|png|jpg|jpeg",
            //     'overwrite' => FALSE,
            //     'max_size' => "30000", // 12mb
            //     'file_name' => $final_image
            // );

            if (in_array($ext, $valid_ext)) {
				$path = $path . strtolower($final_image);
				if (move_uploaded_file($tmp, $path)) {
                    return array(
                        'link' => $this->profileStorage.$final_image,
                        'name' => $final_image
                    );
				}
            }
            
            // if(!file_exists($path)) 
            // {
            //     mkdir($path, 0777, true);
            // }

            // $this->load->library('upload', $config);
            // if (!$this->upload->do_upload($request)) {
            //     $error = array('error' => $this->upload->display_errors());
    
            //     return $error;
            // }else{
                
            //     return array(
            //         'link' => $this->profileStorage.$final_image,
            //         'name' => $final_image
            //     );
            
            // }

        }

        return false;
    }
   
    /* Email notification */

    public function send_email($email, $type, $company, $subject, $receiver_email)
	{
        
		$data['info'] = $receiver_email;
        $template = $this->load->view($type, $data, true);
		$config = array(
            'protocol' => "smtp",
			'smtp_host' => EMAIL_HOST,
			'smtp_port' =>  EMAIL_PORT,
			'smtp_user' => EMAIL_USERNAME,
			'smtp_pass' => EMAIL_PASSWORD,
			'mailtype' => "html",
			'charset' => "utf-8",
			'wordwrap' => TRUE,
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->set_mailtype("html");
		$this->email->from(ucfirst($company), "http://".$company.".com.ph");
		$this->email->to($email);
		$this->email->subject($subject);
		$this->email->message($template);
        $mail = $this->email->send();
        if($mail){
            return true;
        }else{
            show_error($this->email->print_debugger());
        }
    }
    
    public function generate_password(){

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        . '0123456789)'); // and any other characters
        shuffle($seed);
        $rand = '';
        foreach (array_rand($seed, 6) as $k) {
            $rand .= $seed[$k];
        }

        $encrypt_password = password_hash($rand, PASSWORD_DEFAULT);
        return array(
            "hashed_password" => $encrypt_password,
            "temp_password" => $rand,
        );
    }

    /* Validate inputs */

    public function validate_inpt($req, $method){
        $data = array();

        if($method == "patch"){
            foreach($req as $r) {
                if($this->patch($r) === NULL) {
                    return false;
                }else{
                    $data[$r] = $this->patch($r);
                }
            }
        }else{
            foreach($req as $r) {
                if($this->post($r) === NULL) {
                    return false;
                }else{
                    $data[$r] = $this->post($r);
                }
            }
        }

        return $data;
    }

    /* auto generates */

    public function generateReferenceCode($b)
    {
        $ref_code =  substr(filter_var($b, FILTER_SANITIZE_FULL_SPECIAL_CHARS), 0, 4);
        if ($ref_code) {
            $ref_code = strtoupper($ref_code . substr(md5(microtime()), rand(0, 26), 5));
        }

        return $ref_code;
    }
    
}
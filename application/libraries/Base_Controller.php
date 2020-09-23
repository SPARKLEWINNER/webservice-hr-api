<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: X-API-KEY,Content-Type');
    header('Content-Type: application/json');    
    exit;       
}

class Base_Controller extends REST_Controller{

    public  $data = [];
    public  $auth = false;
    public $unauthorize = array('status' => REST_Controller::HTTP_UNAUTHORIZED,'message' => 'Unauthorized.');
    public $client_error = array('status' => REST_Controller::HTTP_UNPROCESSABLE_ENTITY,'message' => 'Client error.');
    public $bad_request = array('status' => REST_Controller::HTTP_BAD_REQUEST,'message' => 'Bad request.');
    public $internal = array('status' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,'message' => 'Internal server error.');
    public $not_found = array('status' => REST_Controller::HTTP_NOT_FOUND,'message' => 'Not found.');
    public $invalid_request = array('status' => REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    public $success = array('status' => REST_Controller::HTTP_OK);

    public $default_client = "mobileapp-client";
    public $default_auth_key = "simplerestapi";
    
    public $videoStorage = "http://zupstars.com/wp-content/uploads/recorded_mobile/";
    public $profileStorage = "http://zupstars.com/wp-content/uploads/profiles/";


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
                $response =  $this->client_error;
                $status = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
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

        if($request_header === 200 || $request_header === 405){
            return array("status" => $request_header, "data" => $response );
        }
        return $response;
    }
    
        /* Upload files */

    public function upload($file,$name, $order_id){
        $valid_ext = array('mp4', 'flv');
        
        $path = '../wp-content/uploads/recorded_mobile/';
        $request = 'record';
        if($file){
            $img = $file['name'];
            $tmp = $file['tmp_name'];
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

            $final_image = "ORDER-".$order_id."-".strtolower($name.time().'1.'.$ext);

            $config = array(
                'upload_path' => $path,
                'allowed_types' => "MP4|FLV|WMV|AVI|WebM|MKV|MOV|flv|mp4|wmv|avi|webm|mkv|mov",
                'overwrite' => FALSE,
                'max_size' => "30000",
                'file_name' => $final_image
            );

            if(!file_exists($path)) 
            {
                mkdir($path, 0777, true);
            }

            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload($request)) {
                $error = array('error' => $this->upload->display_errors());
    
                return $error;
            }
            
            return array(
                'link' => $this->videoStorage.$final_image,
                'name' => $final_image
            );
        }

        return false;
    }
    
    public function upload_profile($file,$name){
      
        
        $path = '../wp-content/uploads/profiles/';
        $request = 'profile';
        if($file){
            $img = $file['name'];
            $tmp = $file['tmp_name'];
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

            $final_image = strtolower($name.time().'1.'.$ext);

            $config = array(
                'upload_path' => $path,
                'allowed_types' => "JPEG|JPG|PNG|GIF|gif|png|jpg|jpeg",
                'overwrite' => FALSE,
                'max_size' => "12000", // 12mb
                'file_name' => $final_image
            );

            if(!file_exists($path)) 
            {
                mkdir($path, 0777, true);
            }

            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload($request)) {
                $error = array('error' => $this->upload->display_errors());
    
                return $error;
            }
            
            return array(
                'link' => $this->profileStorage.$final_image,
                'name' => $final_image
            );
        }

        return false;
    }
    
    public function expo_notification($token,$message){
  
        $payload = array(
            'to' => $token,
            'title' => "Zupstars",
            'sound' => 'default',
            'body' => $message,
        );
    
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://exp.host/--/api/v2/push/send",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($payload),
          CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Accept-Encoding: gzip, deflate",
            "Content-Type: application/json",
            "cache-control: no-cache",
            "host: exp.host"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          return true;
        }
    }
    
    public function send_email($email, $type, $subject, $receiver_email)
	{
		$data['info'] = $receiver_email;
		$template = $this->load->view($type, $data, true);
		$curr_server = $_SERVER['HTTP_HOST'];
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
		$this->email->from(EMAIL_FROM, SITE_NAME);
		$this->email->to($email);
		$this->email->subject($subject);
		$this->email->message($template);
		$mail = $this->email->send();

		if ($curr_server != "localhost") {
			if ($mail) {
				return true;
			} else {
				show_error($this->email->print_debugger());
			}
		} else {
			return true;
		}
	}
    
}
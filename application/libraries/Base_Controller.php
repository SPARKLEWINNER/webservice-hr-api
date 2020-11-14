<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');
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
    
    public $documentStorage = DEFAULT_URI."uploads/docs/";
    public $profileStorage = DEFAULT_URI."uploads/";


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

    public function upload_doc($file,$doc_id,$company){
        $img = $file['name'];
        $tmp = $file['tmp_name'];
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        $path = 'uploads/docs/';
        $request = 'doc';
        $valid_ext = array('jpeg', 'jpg', 'png', 'gif', 'bmp','txt','doc','docx','pdf');
        $name = filter_var($doc_id, FILTER_SANITIZE_STRING)."-".strtolower($doc_id.time().'1.'.$ext);
        
        if(!file_exists($path)) 
        {
            mkdir($path, 0777, true);
        }

        if($file){
            // $config = array(
            //     'upload_path' => "uploads/",
            //     'allowed_types' => "gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF|txt|doc|docx|pdf|PDF|DOCX|DOC",
            //     'overwrite' => FALSE,
            //     'max_size' => "20480",
            //     'file_name' => $name
            // );
            // $this->load->library('upload', $config);
            // $this->upload->initialize($config); 
            $record_upload = array(
                "applicant_id" => $doc_id,
                "company" => $company,
                "date_uploaded" => date('Y-m-d H:i:s'),
            );
            if (in_array($ext, $valid_ext)) {
				$path = $path . strtolower($name);
				if (move_uploaded_file($tmp, $path)) {
                    $record_upload['status'] = 0;
                    $record_upload['message'] = json_encode($file);
                    $record_upload["type"] = "SUCCESSDOCUMENTUPLOAD";
                    
                     $data = array(
                        "applicant_id" => $doc_id,
                        "doc_name" => $file['name'],
                        "doc_type" => $file['type'],
                        "doc_file" => addslashes(file_get_contents($this->documentStorage.$name)),
                        "doc_size" => $file['size'],
                        "doc_link" => $this->documentStorage.$name,
                        "date_created" => date('Y-m-d H:i:s'),
                        "status" => 0,
                    );

                    $this->Main_mdl->record_upload_doc($data);
                    $this->Main_mdl->record_upload_activity($record_upload);
                    return array(
                        'link' => $this->documentStorage.$name,
                        'name' => $name
                    );
				}else{

                    $record_upload['message'] = json_encode(array('error' => $file['error']) );
                    $record_upload['status'] = 1;
                    $record_upload["type"] = "FAILEDDOCUMENTUPLOAD";
                    $this->Main_mdl->record_upload_activity($record_upload);
                }
            }
            /*
            var_dump($this->upload->display_errors());

            if (!$this->upload->do_upload('file')) {
                $record_upload['message'] = json_encode(array('error' => $this->upload->display_errors()) );
                $record_upload['status'] = 1;
                $record_upload["type"] = "FAILEDDOCUMENTUPLOAD";
                $this->Main_mdl->record_upload_activity($record_upload);
            }else{
                    $record_upload['status'] = 0;
                    $record_upload['message'] = json_encode($this->upload->data());
                    $record_upload["type"] = "SUCCESSDOCUMENTUPLOAD";
                     $data = array(
                        "applicant_id" => $doc_id,
                        "doc_name" => $file['name'],
                        "doc_type" => $file['type'],
                        "doc_size" => $file['size'],
                        "doc_link" => $this->documentStorage.$name,
                        "date_created" => date('Y-m-d H:i:s'),
                        "status" => 0,
                    );

                    $this->Main_mdl->record_upload_doc($data);
                    $this->Main_mdl->record_upload_activity($record_upload);
                    return array(
                        'link' => $this->documentStorage.$name,
                        'name' => $name
                    );
            }
            */
        }else{
            return false;
        }

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
            
            if(!file_exists($path)) 
            {
                mkdir($path, 0777, true);
            }

            if (in_array($ext, $valid_ext)) {
				$path = $path . strtolower($final_image);
				if (move_uploaded_file($tmp, $path)) {
                    return array(
                        'link' => $this->profileStorage.$final_image,
                        'name' => $final_image
                    );
				}else{
                   $error = array('error' => $this->upload->display_errors());
                    return $error;
                }
            }

        }
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

    /* Auto generate code */

    public function generateReferenceCode($b)
    {
        $ref_code =  substr(filter_var($b, FILTER_SANITIZE_FULL_SPECIAL_CHARS), 0, 4);
        if ($ref_code) {
            $ref_code = strtoupper($ref_code . substr(md5(microtime()), rand(0, 26), 5));
        }

        return $ref_code;
    }
    
    public function generate_password(){

        $seed = str_split('abcdefghijklmnopqrstuvwxyz'. 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'. '0123456789)'); // and any other characters
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

    /* Email notification -- Send Grid */
    public function send_email_sg($company, $subject, $receiver_email)
    {
        $headr = array();
        $headr[] = 'Authorization: Bearer '.EMAIL_SG_TOKEN;
        $headr[] = 'Content-Type: application/json';
        $_param = json_encode($receiver_email);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,EMAIL_SG_ENDPOINT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output,TRUE);
    }

   /* Email notification -- cPanel */

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


    /* Log Activities */

    public function activity_logs($id,$type,$message,$information, $status){

        $log_data = array(
            'user' =>  $id,
            'type' =>  $type,
            'message' => $message,
            'information' => $information,
            'status' => $status,
            'date' =>  date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->record_log($log_data);
        if($response){
            return true;
        } else{
            return false;
        }
    }

    public function appl_logs($id,$type,$message,$information,$company, $status){

        $log_data = array(
            'user' =>  $id,
            'type' =>  $type,
            'message' => $message,
            'information' => $information,
            'company' => $company,
            'status' => $status,
            'date' =>  date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->record_log($log_data);
        if($response){
            return true;
        } else{
            return false;
        }
    }


    public function email_logs($type,$user,$email,$status,$message, $data,$company){

        $log_data = array(
            'user' =>  $user,
            'email' =>  $email,
            'type' =>  $type,
            'message' => $message,
            'data' => $data,
            'status' => $status,
            'company' => $company,
            'date' =>  date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->record_system($log_data);
        if($response){
            return true;
        } else{
            return false;
        }
    }

    public function createPassword($len = 6)
    {
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

    

      
    
}
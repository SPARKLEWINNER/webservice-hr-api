<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Logs extends Base_Controller{
    public  $data = [];
    public  $auth = false;
    public $method = "";
    public $params = [];
    public $new_acc_path = 'emails/new-account';

    function __construct()
    {
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }


    /* post */


    /* get */
    public function list_email_records_get($company = NULL){
        if(empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_emails_pull($company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function list_logs_record_get($company = NULL){
        if(empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_logs_pull($company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function list_applicants_record_get($company = NULL){
        if(empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_logs_pull($company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function exam_logs_record_get($company = NULL){
          if(empty($company)){
              $this->response_return($this->response_code (400,""));
              return false;
          }

          $response = $this->Main_mdl->record_exam_logs_pull($company);
          if($response){
              return $this->set_response(array("status" => 200, "data" => $response),  200);
          }else{
              $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
              return $this->set_response($response, 422);
          }
      }


}

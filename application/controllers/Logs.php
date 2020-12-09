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

    public function exams_email_post(){
        $data = $this->validate_inpt(array('email','exam_id','job_id', 'id', 'company'), 'post');
        $response = $this->Main_mdl->record_exam_pull($data);

        if($response){
            $decode = json_decode($response['exams'][0]['meta_value']);
            $email_details = array(
                "from" => array(
                    "email" => "system@".$data['company'].".com.ph"
                ),
                "personalizations" => [array(
                    "to" => [array(
                        "email" => $data['email']
                    )],
                    "subject" => EMAIL_NEW_APPLICANT,
                    "dynamic_template_data" => array(
                        "email"=> $data['email'],
                        "help" => EMAIL_ADMIN,
                        "portal" =>"www.portal.".$data['company'].".com.ph", // to be change,
                        "title" => $decode->title,
                        "exam" => $decode->link
                    )
                )],
                "template_id" => EMAIL_SGTEMPLATE_EXAMRETAKE
            );


            $is_mailed = $this->send_email_sg($data['company'], EMAIL_SGTEMPLATE_EXAMRETAKE, $email_details);
            if($is_mailed == NULL){
                $this->email_logs(
                    'EXAMRETAKE',
                    $data['id'], 
                    $data['email'], 
                    0, 
                    "SUCCESS", 
                    json_encode($email_details), 
                    $data['company']
                );
                $this->set_response(array("status" => 200, "data" => $response),  200);
            }else{
                $this->email_logs(
                    'NEWAPPLICANT',
                    $data['id'], 
                    $data['email'], 
                    0, 
                    "FAILED", 
                    json_encode($email_details),
                    $data['company']
                );
            
            }
        } else{
            return false;
        }
    }


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

        $response = $this->Main_mdl->record_logs_pull($company, NULL);
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

        $response = $this->Main_mdl->record_logs_pull($company, "APPLICANT");
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
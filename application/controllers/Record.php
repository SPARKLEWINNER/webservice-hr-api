<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Record extends Base_Controller
{
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


    public function applicant_create_post(){
        // $data = $this->validate_inpt(array('data','email'), 'post');
        $mg_email = $this->post('person_email');
        $generated = $this->generateReferenceCode($mg_email);

        if($this->Main_mdl->record_validate_data($mg_email)){
            $response = $this->response_code(422, "Email already exists", "");
            return $this->set_response($response, 422);
        }

        $upload_proc = $this->upload_profile($_FILES['pref_image'], $generated);
        $app_data = array(
            'username' =>  $this->post('person_email'),
            'data' => json_encode($this->post()),
            'company' => $this->post('company'),
            'reference_id' => $generated,
            'profile' =>  $upload_proc['link'],
            'date_created' => date('Y-m-d H:i:s')
        );


        if($upload_proc){
            $response = $this->Main_mdl->record_data($app_data);
            if(!isset($response['status'])){
                $this->appl_logs($app_data['username'],"APPLICANT","FAILED", json_encode($app_data), 0, $this->post('company'));
                return $this->set_response($response, 422);
            }else{
                $this->appl_logs($app_data['username'],"APPLICANT","SUCCESS", json_encode($app_data), 1, $this->post('company'));
                $email_details = array(
                    "from" => array(
                        "email" => "system@".$this->post('company').".com.ph"
                    ),
                    "personalizations" => [array(
                        "to" => [array(
                            "email" => $response['username']
                        )],
                        "subject" => EMAIL_NEW_APPLICANT,
                        "dynamic_template_data" => array(
                            "email"=> $response['username'],
                            "password" => $response['reference_id'],
                            "help" => EMAIL_ADMIN,
                            "portal" =>"www.portal.".$this->post('company').".com.ph" // to be change
                        )
                    )],
                    "template_id" => EMAIL_SGTEMPLATE_NEW_ACC
                );


                $is_mailed = $this->send_email_sg($this->post('company'), EMAIL_NEW_APPLICANT, $email_details);
                if($is_mailed == NULL){
                    $this->email_logs('NEWAPPLICANT',$response['reference_id'], $response['username'], 0, "SUCCESS", json_encode($email_details), $this->post('company'));
                    $this->set_response(array("status" => 200, "data" => $response),  200);
                }else{
                    $this->email_logs('NEWAPPLICANT',$response['reference_id'], $response['username'], 0, "FAILED", json_encode($email_details), $this->post('company'));
                }
            }
        }else{
            $this->appl_logs($app_data['username'],"APPLICANT","FAILED", json_encode($app_data), 0, $app_data['company']);
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
    }

    public function applicant_document_create_post(){
        $data = $this->validate_inpt(array('company','id'), 'post');
        $upload_proc = $this->upload_doc($_FILES['file'], $data['id'], $data['company']);
        $this->activity_logs($data['id'], 'DOCUMENTLOG', json_encode($_FILES['file']), json_encode($upload_proc), "DOCUMENTLOG", filter_var($upload_proc, FILTER_VALIDATE_BOOLEAN));
        if($upload_proc){
            $response = $this->Main_mdl->records_doc_pull($data['id'], $data['company']);
            if(!$response){
                $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
                return $this->set_response($response, 422);
            }else{
                $this->set_response(array("status" => 200, "data" => $response),  200);
            }
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
            return $this->set_response($response, 422);
        }
    }

    public function applicant_exam_create_post(){
        $data = $this->validate_inpt(array('id','job', 'exam'), 'post');
        $app_data = array(
            "applicant_id" => $data["id"],
            "date_created" => date('Y-m-d H:i:s'),
            "job_id" => $data["job"],
            "exam_id" => $data["exam"],
        );

        $response = $this->Main_mdl->record_exam_data($app_data);
        $this->Main_mdl->record_applying_for($data['job'], $data['id']);
        if(!isset($response['status'])){
            $this->activity_logs($data["id"],"EXAMTAKE","FAILED", json_encode($app_data), 1);
            return $this->set_response($response, 422);
        }else{
            $this->activity_logs($data["id"],"EXAMTAKE","SUCCESS", json_encode($app_data), 0);
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }

    }





    public function review_bypass_record_patch(){
        $data = $this->validate_inpt(array('id','status'), 'patch');
        $response = $this->Main_mdl->record_patch_data($data, $data['status']);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }




    public function applicants_specific_get($company = NULL, $id = NULL){

        if(empty($company) && empty($id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_specific_pull($company,$id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }


    }

    public function applicants_specific_reviews_get($company = NULL, $id = NULL){

        if(empty($company) && empty($id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_reviews_pull($company,$id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }


    }

    // Supervisor Requests

    public function applicants_ts_specific_get($company = NULL, $id = NULL){

        if(empty($company) && empty($id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_ts_specific_pull($company,$id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_ts_specific_reviews_get($company = NULL, $ref_id = NULL){

        if(empty($company) && empty($ref_id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_ts_reviews_pull($company,$ref_id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }


    }




}

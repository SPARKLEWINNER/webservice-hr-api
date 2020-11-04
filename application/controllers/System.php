<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class System extends Base_Controller{


    /* post */ 

    public function resend_email_post(){
        $ref_id = $this->post('id');
        $response = $this->Main_mdl->record_get_system($ref_id);
        $email_details = array(
            "from" => array(
                "email" => "system@".$response['company'].".com.ph"
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
                    // "portal" =>"www.".$this->post('company').".com.ph" // to be change 
                    "portal" =>"http://portal.sparkles.com.ph/" // to be change 
                )
            )],
            "template_id" => EMAIL_SGTEMPLATE_NEW_ACC
        );

        $is_mailed = $this->send_email_sg($response['company'], EMAIL_NEW_APPLICANT, $email_details);
        if($is_mailed == NULL){
            $this->set_response(array("status" => 200, "data" => $is_mailed),  200); 
        }else{
            $this->email_logs('NEWAPPLICANT',$response['reference_id'], $response['username'], 0, "FAILED", $response['email_details'], $response['company']);
        }
    }

    public function create_job_post(){
        $data = $this->validate_inpt(array('company', 'title', 'id', 'description'), 'post');

        $app_data = array(
            "company" => $data['company'],
            "posted_by" => $data['id'],
            "meta_key" => "jobs",
            "meta_value" => json_encode($data),
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_jobs($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function create_exams_post(){
        $data = $this->validate_inpt(array('company', 'title', 'id', 'notice', 'job_id', 'link'), 'post');

        $app_data = array(
            "company" => $data['company'],
            "posted_by" => $data['id'],
            "meta_key" => "exams",
            "meta_value" => json_encode($data),
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_exams($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function create_store_post(){
        $data = $this->validate_inpt(array('name','company','created_by'), 'post');

        $app_data = array(
            "name" => $data['name'],
            "details" =>  json_encode($this->post()),
            "company" => $data['company'],
            "created_by" => $data['created_by'],
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_store($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    /* get */ 

    public function jobs_records_get($company = NULL , $id = NULL){
        if(empty($company) && empty($id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_jobs_pull($company,$id,"jobs");
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function job_specific_records_get($company = NULL , $job_id = NULL){
        if(empty($company) && empty($job_id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_jobs_specific_pull($company,$job_id,"jobs");
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    /* patch */ 

    public function update_exams_patch(){
        $data = $this->validate_inpt(array('company', 'title', 'exam_id', 'notice', 'job_id', 'link'), 'patch');
        $exam_id = $data['exam_id'];
        $app_data = array(
            "company" => $data['company'],
            "meta_key" => "exams",
            "meta_value" => json_encode($data)
        );

        $response = $this->Main_mdl->system_record_update_exams($app_data, $exam_id);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function update_email_patch(){
        $data = $this->validate_inpt(array('id', 'email'), 'patch');
        $email_id = $data['id'];
        $app_data = array(
            "email" => $data['email']
        );

        $response = $this->Main_mdl->system_record_update_email($app_data, $email_id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    /* delete */ 

    public function remove_exams_delete($exam_id = NULL){
        if(empty($exam_id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        
        $response = $this->Main_mdl->system_record_remove_exams($exam_id);
        if($response){
            return $this->set_response(array("status" => 200, "message" => "Success removed Examination"),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

}
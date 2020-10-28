<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class System extends Base_Controller{

    public function resend_email_post(){
        $ref_id = $this->post('id');
        $response = $this->Main_mdl->record_get_system($ref_id);

        $is_mailed = $this->send_email_sg($response['company'], EMAIL_NEW_APPLICANT, json_decode($response['email_details']));
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
}
<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Store extends Base_Controller{
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

    /* Patch */

    
    
    /* Post */
    public function review_create_post(){
        $data = $this->validate_inpt(array('company','id', 'reviewer', 'store_assess','review_status'), 'post');
        $response = $this->Main_mdl->record_review_store_data($data);
        if(!$response){
            return $this->set_response($response, 422);
        }else{
            // $this->send_email($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }

    }

    public function store_new_password_post(){

        $id = $this->post('id');
        if(empty($id)){
            $this->response_return($this->response_code (201,"Invalid data sent"));
            return false;
        }

        $generate_password = $this->createPassword();
        $app_data = array(
            "password" => $generate_password['hashed_password'],
            "temp_password" => $generate_password['temp_password']
        );

        $response = $this->Main_mdl->system_record_new_password($id, $app_data);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function store_new_post(){
        $data = $this->validate_inpt(array('name','company','created_by'), 'post');
        $generate_password = $this->createPassword();
        $app_data = array(
            "name" => $data['name'],
            "details" =>  json_encode($this->post()),
            "company" => $data['company'],
            "created_by" => $data['created_by'],
            "date_created" => date('Y-m-d H:i:s')
        );
        
        $acc_data = array(
            "email" =>  $this->post('email'),
            "first_name" =>  $this->post('store_type'),
            "last_name" => $data['name'],
            "user_level" => $this->post('type'),
            "company" => $data['company'],
            "password" => $generate_password['hashed_password'],
            "temp_password" => $generate_password['temp_password'],
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_store($app_data);
        if($response){

            // create user 
            $response_acc = $this->Main_mdl->system_record_store_acc_assg($acc_data, $response['id']);
            if($response_acc){
                return $this->set_response(array("status" => 200, "data" => $response_acc),  200);
            }else{
                $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
                return $this->set_response($response, 422);
            }

        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    /* Get */
    public function list_applicants_get($store = NULL, $company = NULL){

        if(empty($company) && empty($store)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->records_store_people_pull($company, $store);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function list_stores_get($company = NULL){

        if(empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_stores_pull($company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function list_stores_accounts_get($company = NULL){

        if(empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_stores_account_pull($company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function store_details_get($id = NULL, $company = NULL){

        if(empty($id) && empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_stores_details_pull($id, $company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function update_applicant_store_post($id = NULL, $company = NULL){

        if(empty($id) && empty($company)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_stores_details_pull($id, $company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }


}

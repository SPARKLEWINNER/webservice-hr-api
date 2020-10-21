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
    
           
    public function create_post(){
        

        // $data = $this->validate_inpt(array('data','email'), 'post');
        $mg_email = $this->post('person_email');
        $generated = $this->generateReferenceCode($mg_email);
        $upload_proc = $this->upload_profile($_FILES['pref_image'], $generated);
        $app_data = array(
            'username' =>  $this->post('person_email'),
            'data' => json_encode($this->post()),
            'company' => $this->post('company'),
            'reference_id' => $generated,
            'profile' =>  $upload_proc['link']
        );

        if($upload_proc){
            $response = $this->Main_mdl->record_data($app_data);
            if(!isset($response['status'])){
                return $this->set_response($response, 422);
            }else{
                $is_mailed = $this->send_email_sg($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
                if($is_mailed == NULL){
                    $this->set_response(array("status" => 200, "data" => $response),  200); 
                }else{
                    
                }
            }
        }else{
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
    }
           
    public function review_app_post(){
        $data = $this->validate_inpt(array('data','company','id'), 'post');
        $mg_id = $this->post('id');
        $generated = $this->generateReferenceCode($mg_id);

        $app_data = array(
            'applicant_id' => $this->post('id'),
            'recruitment' => json_encode($this->post()),
            'company' => $this->post('company'),
            'reference_id' => $generated,
        );

        $response = $this->Main_mdl->record_review_data($mg_id,$app_data);
        if(!isset($response['status'])){
            return $this->set_response($response, 422);
        }else{
            $this->send_email($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
            $this->set_response(array("status" => 200, "data" => $response),  200); 
        }
        
    }

    public function in_review_patch(){
        $data = $this->validate_inpt(array('id'), 'patch');
        $response = $this->Main_mdl->record_patch_data($data, 1);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_get($company = NULL){
               
        if(empty($company) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_pull($company);
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
        
    public function remove_post(){
  
        if(empty($this->post('uid')) && empty($this->post('cid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        if(empty($this->post('oid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        $uid = $this->post('uid');
        $cid = $this->post('cid');  
        $record_id = $this->post('record_id'); 
        $oid = $this->post('oid'); 
        $response = $this->Main_mdl->record_remove($uid, $cid, $record_id, $oid);
        if($response){
            $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
    public function records_get($uid = NULL, $cid = NULL, $oid = NULL){
        
        if(empty($uid) && empty($cid) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        
        if(empty($oid) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        

        
        $uid = $uid;
        $cid = $cid;
        $oid = $oid;
        $response = $this->Main_mdl->record_pull($uid,$cid,$oid);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
    public function review_record_patch(){

       if(empty($this->patch('status')) && empty($this->patch('id'))){
         $this->response_return($this->response_code(400,""));
         return false;
       }     
        
       $id = $this->patch('id'); // record_id
       $status = $this->patch('status'); // [0 = not submitted / 1 = submitted for review / 2 = reviewed / 3 = completed ] -- status 

       $data = array(
            "status" => $status
        );
        
        $response = $this->Main_mdl->submit_record($id,$data);

        if($response){
            return $this->set_response($response,  200);

        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);        
        }
        
    }
    
}
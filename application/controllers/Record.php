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
            'profile' =>  $upload_proc['link'],
            'date_created' => date('Y-m-d H:i:s')
        );

        if($upload_proc){
            $response = $this->Main_mdl->record_data($app_data);
            if(!isset($response['status'])){
                return $this->set_response($response, 422);
            }else{

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
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
    }

    public function upload_documents_post(){
        $data = $this->validate_inpt(array('company','id'), 'post');
        $upload_proc = $this->upload_doc($_FILES['file'], $data['id'], $data['company']);
        $this->activity_logs($data['id'], 'DOCUMENTLOG', json_encode($_FILES['file']), json_encode($upload_proc), "DOCUMENTLOG", filter_var($upload_proc, FILTER_VALIDATE_BOOLEAN));
        if($upload_proc){
            $response = $this->Main_mdl->records_doc_pull($data['id'], $data['company']);
            if(!$response){
                return $this->set_response($response, 422);
            }else{
                $this->set_response(array("status" => 200, "data" => $response),  200); 
            }
        }else{
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
    }

    public function review_app_post(){
        $data = $this->validate_inpt(array('data','company','id','reviewer','assess_evaluation', 'store','refernce_id'), 'post');
        $mg_id = $this->post('id');


        $app_data = array(
            'applicant_id' => $this->post('id'),
            'recruitment' => json_encode(json_decode($this->post('data'))),
            'company' => $this->post('company'),
            'recruitment_reviewer' => $this->post('reviewer'),
            'assess_evaluation' => $this->post('assess_evaluation'),
            'store' => $this->post('store'),
            'reference_id' => $this->post('refernce_id'),
            'date_created' => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->record_review_data($mg_id,$app_data);
        if(!isset($response['status'])){
            return $this->set_response($response, 422);
        }else{
            // $this->send_email($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
            $this->set_response(array("status" => 200, "data" => $response),  200); 
        }
        
    }

    public function review_store_app_post(){
        $data = $this->validate_inpt(array('company','id', 'reviewer', 'store_assess','review_status'), 'post');
        $response = $this->Main_mdl->record_review_store_data($data);
        if(!isset($response['status'])){
            return $this->set_response($response, 422);
        }else{
            // $this->send_email($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
            $this->set_response(array("status" => 200, "data" => $response),  200); 
        }
        
    }

    public function exam_take_post(){
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

    public function applicants_status_get($company = NULL, $status = 0){
               
        if(empty($company) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->record_status_pull($company, $status);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
       
        
    }

    public function applicants_weekly_get($type = NULL, $company = NULL , $number = 0){

        if(empty($company) && empty($number)){
            $this->response_return($this->response_code (400,""));
            return false;
        }
               
        if(empty($type)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        if($type == "day" || $type == "days"){
            $response = $this->Main_mdl->record_day_pull($company, $number);

        }else{
            $response = $this->Main_mdl->record_weeks_pull($company, $number);
        }

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
       
        
    }

    public function applicants_pool_get($type = NULL, $company = NULL , $number = 0){

        if(empty($company) && empty($number)){
            $this->response_return($this->response_code (400,""));
            return false;
        }
               
        if(empty($type)){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        if($type == "day" || $type == "days"){
            $response = $this->Main_mdl->record_pool_day_pull($company, $number);

        }else{
            $response = $this->Main_mdl->record_pool_weeks_pull($company, $number);
        }

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

    public function stores_record_get($company = NULL){
               
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
    
    public function store_people_record_get($store = NULL, $company = NULL){

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

    public function emails_record_get($company = NULL){
               
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
        
    public function logs_record_get($company = NULL){
               
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
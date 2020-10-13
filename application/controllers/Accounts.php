<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Accounts extends Base_Controller
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

        
    public function user_det_get($id = NULL){
        
        if(empty($id) && empty($product_id)){
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        $id = $id;
        $response = $this->Main_mdl->user_pull($id);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);
        }
    }

    
    public function create_post(){
  
        if(empty($this->post('data')) && empty($this->post('date_created'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }

        $data = array(
            'data' => $this->post('data'),
            'date_created' => $this->post('date_created'),
        );

        $response = $this->Main_mdl->new_user($data);

        if(!isset($response['status'])){
            return $this->set_response($response, 422);
        }else{
            $this->send_email($mg_email,$this->new_acc_path, EMAIL_NEW_APPLICANT,array($data,$password));
            $this->set_response($response,  200); 
        }

       
        
    }
 
    
    public function user_get($id = NULL, $product_id = NULL){
        
        if(empty($id) && empty($product_id)){
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        $id = $id;
        $product_id = $product_id;
        $response = $this->Main_mdl->user_pull($id,$product_id);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);
        }
    }
    
    public function user_rate_post(){
        if(empty($this->post('post_id')) && empty($this->post('uid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        $verify_user = $this->Main_mdl->check_user($this->post('uid'),$this->post('post_id'));
        if(!$verify_user){
            $this->response_return($this->response_code(422,""));
            return false;
        }
        $uid = $this->post('uid');
        $data = array(
            "meta_value" => $this->post('rate')
        );
        $post_id = $this->post('post_id');
        
        $response = $this->Main_mdl->update_user($uid,$post_id, $data);
        
        if(!$response){
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);            
            
        }else{
            return $this->set_response($response,  200);
        }
    }
    
    
    public function user_notify_post(){
        if(empty($this->post('uid')) && empty($this->post('oid')) ){
            $this->response_return($this->response_code(400,""));
            return false;
        }
        

        $uid = $this->post('uid'); // product_id
        $oid = $this->post('oid'); // order id
        $message = $this->post('message');
        $status = $this->post('status');
        $data = array(
            "uid" => $uid,
            "oid" => $oid,
            "message" => $message,
            "date_created" => date('Y-m-d H:i:s'),
            "status" => $status
        );
        
        
        $response = $this->Main_mdl->notify_user($data);
        
        if($response){
            if($status == 1){
                $this->expo_notification($response['token'],$message);  
            }
            
            return $this->set_response($response,  200);

        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);        
        }
        
    }
    
   public function update_user_patch(){
    
       if(empty($this->patch('id'))){
         $this->response_return($this->response_code(400,""));
         return false;
       }     
       
       if(empty($this->patch('old')) && empty($this->patch('new'))){
         $this->response_return($this->response_code(400,""));
         return false;
       }     
        
       $id = $this->patch('id'); 
       $old = $this->patch('old'); 
       $new = $this->patch('new');
       $data = array(
            "old" => $old,
            "new" => $new,
            
        );
        
        $response = $this->Main_mdl->update_details_user($id,$data);

        if($response){
            if(!empty($response['status']) && $response['status'] === 204){
                return $this->set_response($response, 422);
            }else{
                $data = $response;
                $response['token'] = AUTHORIZATION::generateToken($data);
                $this->set_response($response,  200);  
            }

        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);        
        }
        
    }
    
     public function update_user_password_patch(){

        if(empty($this->patch('id'))) {
            $this->response_return($this->response_code(400,""));
            return false; 
        }
        
        if(empty($this->patch('old')) && empty($this->patch('new'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        

        $id = $this->patch('id');
        $oldPassword = $this->patch('old');
        $newPassword = $this->patch('new');

        $response = $this->Main_mdl->update_user_password($id,array("old" => $oldPassword, "new" => $newPassword));
        if($response === FALSE):
            $response = $this->response_code(422, "User Invalid", "");
            return $this->set_response($response, 422);
        else:
        
            if(!empty($response['status']) && $response['status'] === 204){
                return $this->set_response($response, 422);
            }else{
                $data = $response;
                $response['token'] = AUTHORIZATION::generateToken($data);
                $this->set_response($response,  200);  
            }

        endif;
    }
    
    
    public function register_token_patch(){
        if(empty($this->patch('uid')) && empty($this->patch('token')) ){
            $this->response_return($this->response_code(400,""));
            return false;
        }
        

        $uid = $this->patch('uid'); // user_id
        $token = $this->patch('token'); // order id
        $data = array(
            "token" => $token,
        );
        
       
        $response = $this->Main_mdl->set_token($uid,$data);

        if($response){
            return $this->set_response($response,  200);

        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);        
        }
        
    }
    
}
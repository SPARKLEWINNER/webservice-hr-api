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

        
    public function user_get($id = NULL){
        
        if(empty($id)){
            $this->response_return($this->response_code(400,""));
            return false;
        }
        $response = $this->Main_mdl->user_pull($id);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"), "");
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
            $this->send_email_sg($mg_email,$this->new_acc_path, EMAIL_NEW_APPLICANT,array($data,$password));
            $this->set_response($response,  200); 
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
    
     public function update_user_password_patch($id = null){

        if(empty($this->patch('old')) && empty($this->patch('new'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
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

    public function user_update_patch($id = null){

        if(empty($id)){
            $this->response_return($this->response_code(400,""));
            return false;
        }

        $data = $this->validate_inpt(array('email', 'firstname', 'lastname'), 'patch');
        
        $result = $this->Main_mdl->user_update_details($data, $id);
        if ($result) {
            $this->set_response(array("status" => 200, "data" => $result),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" =>  "Bad Request"));
            return $this->set_response($response, 422);
        }
    }

     public function mobile_post($email = null, $mobile = null){
        $email = $this->post('email');
        $mobile = $this->post('mobile');
        if(empty($this->post('email')) && empty($this->post('mobile'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        

        $response = $this->Main_mdl->update_user_mobile($email,$mobile);
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

     public function otp_post($mobile = null){
        $mobile = $this->post('mobile');
        if(is_null($this->post('mobile'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        $response = $this->Main_mdl->check_mobile($mobile);
        if ($response['status'] === 204 ) {
            $this->set_response($response,  200);
        }
        else {
            $otp = random_int(100000, 999999);
            $updateOTP = $this->Main_mdl->update_otp($mobile, $otp);
            $ptn = "/^0/";  // Regex
            $str = $mobile; //Your input, perhaps $_POST['textbox'] or whatever
            $rpltxt = "63";  // Replacement string
            $formattedMobile = preg_replace($ptn, $rpltxt, $str);
            $curl = curl_init();
            var_dump($formattedMobile);
            $data = array(
              'api_key' => SMS_KEY,
              'api_secret' => SMS_SECRET,
              'text' => "Hello! ".$response['first_name']." Here is your OTP: ".$otp." Have a great day ahead.",
              'to' => $formattedMobile,
              'from' => "APEX"
            );

            #Send SMS
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.movider.co/v1/sms",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_HTTPHEADER => array(
                  "Content-Type: application/x-www-form-urlencoded",
                  "cache-control: no-cache"
                ),
            ));
            
            $smsSendResponse = curl_exec($curl);
            $sendingErr = curl_error($curl);

            curl_close($curl);
            
            if ($sendingErr) {
              $this->set_response("cURL Error #:" . $sendingErr, 204);
            } else {
              $updateOTP = $this->Main_mdl->update_otp($mobile, $otp);
              if ($updateOTP['status'] == 200) :
                  return $this->set_response($updateOTP['message'], 200);
              else :
                  return $this->set_response($updateOTP['message'], 204);
              endif;
            }
        }
        
        //var_dump($otp);
    }
}
<?php
require APPPATH . '/libraries/Base_Controller.php';
defined('BASEPATH') or exit('No direct script access allowed');


class Auth extends Base_Controller
{
    public  $data = [];
    public  $auth = false;
    public $method = "";
    public $params = [];
    public $forgot_acc_path = 'emails/forgot-password';

    function __construct()
    {
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function login_post(){


        $data = $this->validate_inpt(array('email','password'), 'post');

        $response = $this->Main_mdl->login($data['email'],$data['password']);
        if($response === FALSE):
            $response = $this->response_code(422, "User Invalid", "");
            return $this->set_response($response, 422);
        else:
            if(!array_key_exists("status",$response)){
                $data = $response;
                $response['timestamp'] = date("Y-m-d H:i:s");
                $response['token'] = AUTHORIZATION::generateToken($data);

                if($data['user_level'] == 2){
                    $response['route'] = "employee/";
                }

                if($data['user_level'] == 3){
                    $response['route'] = "admin/";
                }

                if($data['user_level'] == 4){
                    $response['route'] = "hr/";
                }

                if($data['user_level'] == 5){
                    $response['route'] = "supervisor/";
                }

                if($data['user_level'] == 6){
                    $response['route'] = "hr/";
                }

                if($data['user_level'] == 7){
                    $response['route'] = "finance/";
                }


                if($data['user_level'] == 10){
                    $response['route'] = "applicant/";
                }
                
                // $this->Main_mdl->recordToken($data['id'],$response['token']);
                $this->set_response(array("status" => 200, "data" => $response), 200);
                    
            }else{
                $response = $this->response_code(422, array("status" => 422, "message" => "Invalid Credentials"), "");
                return $this->set_response($response, 422);

            }
        endif;
    }

	public function logout_post()
	{
        if($this->auth_request() === false) return $this->response_return($this->response_code(401,""));
          
        $response = $this->Main_mdl->logout();
        
        if($response === FALSE):
            return $this->response_return($response);
        endif;
        
        return $this->response_return($response);

    }
    
    public function forgot_post()
    {
        $data = $this->validate_inpt(array('email'), 'post');
        $data['temp'] = $this->generate_password()['temp_password'];
        $data['hash'] = $data['temp'];
        $result = $this->Main_mdl->retrieveUser($data['email'], $data['temp']);
        if(!array_key_exists("status",$result)){
            $data['id'] = $result['id'];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['token'] = AUTHORIZATION::generateToken($data);
            
            $process = $this->send_email($data['email'],$this->forgot_acc_path, EMAIL_FORGOT_PASSWORD,array($data));
            if(!$process){
                $response = $this->response_code(422, "Mailing", "");
                return $this->set_response($response, 422);
            }
            $this->set_response($data,  200);
        }else{
            $response = $this->response_code(422, "", "");
            return $this->set_response($response, 422);
            
        }

    }
    
    public function reset_patch()
    {
        $data = $this->validate_inpt(array('hash', 'email', 'password'), 'patch');
        $result = $this->Main_mdl->resetUser($data);
        if(!array_key_exists("status",$result)){
            $data['id'] = $result['id'];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['token'] = AUTHORIZATION::generateToken($data);
            
            if(!$result){
                $response = $this->response_code(422, "Invalid token", "");
                return $this->set_response($response, 422);
            }
            
            $this->set_response($data,  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "User Invalid"), "");
            return $this->set_response($response, 422);
            
        }
    }

    public function token_get()
    {
        $tokenData = array();
        $tokenData['id'] = 1; //TODO: Replace with data for token
        $output['token'] = AUTHORIZATION::generateToken($tokenData);
        $this->set_response($output, REST_Controller::HTTP_OK);
    }

}

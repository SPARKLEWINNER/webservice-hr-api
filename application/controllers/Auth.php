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
            if($response['status'] == 204){
                $reponse = $this->response_code(422, "User Invalid", "");
                return $this->set_response($response, 422);                
            }else{
                $data = $response;
                $response['timestamp'] = date("Y-m-d H:i:s");
                $response['token'] = AUTHORIZATION::generateToken($data);
    
                $this->Main_mdl->recordToken($data['id'],$response['token']);
                $this->set_response($response,  200);
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
        if($data != FALSE):
            
            $data['temp'] = $this->generate_password()['temp_password'];
            $data['hash'] = $this->generate_password()['hashed_password'];
            
            $data['id'] = $this->Main_mdl->retrieveUser($data['email'], $data['hash'])['id'];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['token'] = AUTHORIZATION::generateToken($data);
            
            $this->send_email($data['email'],$this->forgot_acc_path, EMAIL_FORGOT_PASSWORD,array($data));
            $this->set_response($response,  200);

        else:
            $response = $this->response_code(400, "", "");
            return $this->set_response($response, 400);
            
        endif;
    }

    public function reset_patch()
    {
        $data = $this->validate_inpt(array('email'), 'post');
        if($data != FALSE):
            
            $data['temp'] = $this->generate_password()['temp_password'];
            $data['hash'] = $this->generate_password()['hashed_password'];
            
            $data['id'] = $this->Main_mdl->retrieveUser($data['email'], $data['hash'])['id'];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['token'] = AUTHORIZATION::generateToken($data);
            
            $this->send_email($data['email'],$this->forgot_acc_path, EMAIL_FORGOT_PASSWORD,array($data));
            $this->set_response($response,  200);

        else:
            $response = $this->response_code(400, "", "");
            return $this->set_response($response, 400);
            
        endif;
    }

    public function token_get()
    {
        $tokenData = array();
        $tokenData['id'] = 1; //TODO: Replace with data for token
        $output['token'] = AUTHORIZATION::generateToken($tokenData);
        $this->set_response($output, REST_Controller::HTTP_OK);
    }

}

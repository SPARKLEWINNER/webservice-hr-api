<?php
require APPPATH . '/libraries/Base_Controller.php';
defined('BASEPATH') or exit('No direct script access allowed');


class Auth extends Base_Controller
{
    public  $data = [];
    public  $auth = false;
    public $method = "";
    public $params = [];

    function __construct()
    {
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function login_post(){


        if(empty($this->post('email')) && empty($this->post('password'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }

        $email = $this->post('email');
        $password = $this->post('password');

        $response = $this->Main_mdl->login($email,$password);
        if($response === FALSE):
            $response = $this->response_code(422, "User Invalid", "");
            return $this->set_response($response, 422);
        else:
            $data = $response;
            $data['timestamp'] = date("Y-m-d H:i:s");
            $response['token'] = AUTHORIZATION::generateToken($data);

            $this->Main_mdl->recordToken($data['id'],$response['token']);
            $this->set_response($response,  200);
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

    public function token_get()
    {
        $tokenData = array();
        $tokenData['id'] = 1; //TODO: Replace with data for token
        $output['token'] = AUTHORIZATION::generateToken($tokenData);
        $this->set_response($output, REST_Controller::HTTP_OK);
    }

}

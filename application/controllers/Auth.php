<?php
require APPPATH . '/libraries/Base_Controller.php';
defined('BASEPATH') or exit('No direct script access allowed');


class Auth extends Base_Controller
{
    public $data = [];
    public $auth = false;
    public $method = "";
    public $params = [];
    public $forgot_acc_path = 'emails/forgot-password';

    function __construct()
    {
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function login_post()
    {


        $data = $this->validate_inpt(array('email', 'password'), 'post');

        $response = $this->Main_mdl->login($data['email'], $data['password']);
        if ($response === FALSE) :
            $response = $this->response_code(422, "User Invalid", "");
            return $this->set_response($response, 422);
        else :
            if (!array_key_exists("status", $response)) {
                $data = $response;
                $response['timestamp'] = date("Y-m-d H:i:s");
                $response['token'] = AUTHORIZATION::generateToken($data);

                if ($data['user_level'] == 2) {
                    $response['route'] = "employee/";
                }

                if ($data['user_level'] == 3) {
                    $response['route'] = "admin/";
                }

                if ($data['user_level'] == 4) {
                    $response['route'] = "hr/";
                }

                if ($data['user_level'] == 5) {
                    $response['route'] = "supervisor/";
                }

                if ($data['user_level'] == 6) {
                    $response['route'] = "hr/";
                }

                if ($data['user_level'] == 7) {
                    $response['route'] = "finance/";
                }

                if ($data['user_level'] == 8) {
                    $response['route'] = "training/";
                }

                if ($data['user_level'] == 10) {
                    $response['route'] = "applicant/";
                }

                // $this->Main_mdl->recordToken($data['id'],$response['token']);
                $this->set_response(array("status" => 200, "data" => $response), 200);
            } else {
                $response = $this->response_code(422, array("status" => 422, "message" => "Invalid Credentials"), "");
                return $this->set_response($response, 422);
            }
        endif;
    }

    public function workplace_login_post()
    {
        $data = $this->validate_inpt(array('email', 'password'), 'post');
        $response = $this->Main_mdl->workplace_login($data['email'], $data['password']);
        if (!$response) :
            $response = $this->response_code(201, array("status"=> 201, "message" => "User Invalid"), "");
            return $this->set_response($response, 201);
        else :
            $data = $response;
            $response['timestamp'] = date("Y-m-d H:i:s");
            $response['token'] = AUTHORIZATION::generateToken($data);

            if ($data['user_level'] == 5) {
                $response['route'] = "supervisor/";
            }

            $this->set_response(array("status" => 200, "data" => $response), 200);
        endif;
    }

    public function member_login_post()
    {
        echo $_SERVER['HTTP_HOST'] == "localhost"
        $data = $this->validate_inpt(array('email', 'password'), 'post');
        $response = $this->Main_mdl->member_login($data['email'], $data['password']);
        if (!$response) :
            $response = $this->response_code(422, array("status"=> 422, "message" => "User Invalid"), "");
            return $this->set_response($response, 422);
        else :
            $data = $response;
            $response['timestamp'] = date("Y-m-d H:i:s");
            $response['token'] = AUTHORIZATION::generateToken($data);

            if (intval($data['user_level']) === 10) {
                $response['route'] = "applicant/";
            }

            $this->set_response(array("status" => 200, "data" => $response), 200);
        endif;
    }

    public function logout_post()
    {
        if ($this->auth_request() === false) return $this->response_return($this->response_code(401, ""));

        $response = $this->Main_mdl->logout();

        if ($response === FALSE) :
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
        if (!array_key_exists("status", $result)) {
            $data['id'] = $result['id'];
            $data['company'] = $result['company'];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['token'] = AUTHORIZATION::generateToken($data);

            if ($result['switchable'] == 1) {
                $data['company'] = json_decode($result['company'])->company[0];
            }

            $email_details = array(
                "from" => array(
                    //"email" => "Reset Password <no-reply@" . $result['company'] . ".com.ph>"
                    "email" => "Reset Password <no-reply@sparkles.com.ph>"
                ),
                "personalizations" => [array(
                    "to" => [array(
                        "email" => $data['email']
                    )],
                    "subject" => EMAIL_NEW_APPLICANT,
                    "dynamic_template_data" => array(
                        "email" => $data['email'],
                        "help" => EMAIL_ADMIN,
                        "portal" => $result['return_url'], // to be change,
                        "title" => "Forgot Password",
                        "temp" => $data['temp']
                    )
                )],
                "template_id" => EMAIL_SGTEMPLATE_FORGOTPASSWORD
            );
            $process = $this->send_email_sg($data['email'], EMAIL_SGTEMPLATE_FORGOTPASSWORD, $email_details);

            if ($process != NULL) {
                $response = $this->response_code(422, "Mailing", "");
                return $this->set_response($response, 422);
            }
            $this->set_response($data,  200);
        } else {
            $response = $this->response_code(422, "", "");
            return $this->set_response($response, 422);
        }
    }

    public function reset_post()
    {
        $data = $this->validate_inpt(array('hash', 'email', 'password'), 'post');
        $result = $this->Main_mdl->resetUser($data);
        if (!array_key_exists("status", $result)) {
            $data['id'] = $result['id'];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['token'] = AUTHORIZATION::generateToken($data);

            if (!$result) {
                $response = $this->response_code(422, "Invalid token", "");
                return $this->set_response($response, 422);
            }

            $this->set_response($data,  200);
        } else {
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

    public function me_post($token = NULL)
    {
        $data = $this->validate_inpt(array('id'), 'post');
        $response = $this->Main_mdl->verifyUser($data);
        $this->set_response(array("status" => 200, "data" => $response), 200);
    }
}

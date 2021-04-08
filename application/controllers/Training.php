<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');

class Training extends Base_Controller
{
    public $data = [];
    public $auth = false;
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

    /* Get */

    public function list_employee_get($company = NULL)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_pull($company);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function list_employee_status_get($company = NULL, $status = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_employee_status_pull($company, $status);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function employee_specific_get($id = "")
    {
        if (empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_specific_employee_pull($id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }
}

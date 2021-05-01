<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Recruitment extends Base_Controller
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

    /* Patch */

    public function review_update_patch()
    {
        $data = $this->validate_inpt(array('id'), 'patch');
        $response = $this->Main_mdl->record_patch_data($data, 1);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    /* Post */

    public function review_create_post()
    {
        $data = $this->validate_inpt(array('data', 'company', 'id', 'reviewer', 'assess_evaluation', 'store', 'refernce_id'), 'post');
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

        $response = $this->Main_mdl->record_review_data($mg_id, $app_data);
        if (!$response) {
            return $this->set_response($response, 422);
        } else {
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }
    }


    public function review_create_document_post()
    {
        $data = $this->validate_inpt(array('company', 'id', 'appl_company', 'appl_id', 'notice', 'status'), 'post');
        $app_data = array(
            'appl_id' => $data['appl_id'],
            'appl_company' => $data['appl_company'],
            'author_id' => $data['id'],
            'author_company' => $data['company'],
            'notice' => $data['notice'],
            'status' => $data['status'],
            'data' => json_encode($this->post()),
            'date_created' => date('Y-m-d H:i:s'),
        );

        $response = $this->Main_mdl->record_review_doc_data($app_data);
        if (!$response) {
            return $this->set_response($response, 422);
        } else {
            // $this->send_email($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }
    }

    public function recruitment_final_post()
    {
        $data = $this->validate_inpt(array('id', 'company', 'appl_company', 'appl_id', 'notice', 'status'), 'post');
        $app_data = array(
            'appl_id' => $data['appl_id'],
            'appl_company' => $data['appl_company'],
            'author_id' => $data['id'],
            'author_company' => $data['company'],
            'notice' => $data['notice'],
            'status' => $data['status'],
            'data' => json_encode($this->post()),
            'date_created' => date('Y-m-d H:i:s'),
        );

        $response = $this->Main_mdl->record_for_training($app_data);
        if (!$response) {
            return $this->set_response($response, 422);
        } else {
            // $this->send_email($mg_email,$this->new_acc_path, $this->post('company'), EMAIL_NEW_APPLICANT,array($response,$generated));
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }
    }

    /* Get */

    public function list_applicants_get($company = NULL)
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


    public function list_applicants_status_get($company = NULL, $status = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_status_pull($company, $status);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function list_applicants_datecreated_get($type = NULL, $company = NULL, $number = 0)
    {

        if (empty($company) && empty($number)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        if (empty($type)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        if ($type == "day" || $type == "days") {
            $response = $this->Main_mdl->record_day_pull($company, $number);
        } else {
            $response = $this->Main_mdl->record_weeks_pull($company, $number);
        }

        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }
}

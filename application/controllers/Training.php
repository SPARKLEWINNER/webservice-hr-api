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

    public function human_relations_post($aplid = NULL, $storeid = NULL, $status = NULL, $date = NULL, $company = NULL, $apl_name = NULL)
    { 
        $data = array(
            'applicant_id' =>  $this->post('aplid'),
            'store_id' => $this->post('storeid'),
            'status' => $this->post('status'),
            'date_hired' => $this->post('date'),
            'company' => $this->post('company'),
            'applicant_name' => $this->post('apl_name')
        );
        $response = $this->Main_mdl->humanRelationsPost($data);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function list_human_relations_get($storeId = null, $company = null)
    { 
        $response = $this->Main_mdl->humanRelationsGet($storeId, $company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function update_personnel_status_post($hrName = NULL, $hrEmail = NULL, $id = NULL, $status = NULL, $date = NULL, $startDate = NULL, $endDate = NULL){

        if(empty($this->post('id')) && empty($this->post('status'))){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        $id = $this->post('id');
        $status = $this->post('status');
        $startDate = $this->post('startDate');
        $date = $this->post('date');
        $endDate = $this->post('endDate');
        $hr = $this->post('hr');
        $hrName = $this->post('hrName');
        $hrEmail = $this->post('hrEmail');
        if ($this->post('date') === NULL) {
            $response = $this->Main_mdl->update_status_deployment($hrName, $hrEmail, $id, $status, $startDate, $endDate);
            if($response){
                return $this->set_response(array("status" => 200, "data" => $response),  200);
            }else{
                $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
                return $this->set_response($response, 422);
            }
        }
        if ($this->post('startDate') === NULL || $this->post('endDate') === NULL) {
            $response = $this->Main_mdl->update_status_deploymentV2($hrName, $hrEmail, $id, $status, $date, $hr);
            if($response){
                return $this->set_response(array("status" => 200, "data" => $response),  200);
            }else{
                $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
                return $this->set_response($response, 422);
            }
        }
    }

    public function list_specific_employee_get($name = NULL)
    {

        if (empty($name)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->personnel_specific_get($name);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found"));
            return $this->set_response($response, 422);
        }
    }

    public function list_specific_applicant_get($name = NULL, $company = NULL)
    {

        if (empty($name)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->applicant_specific_get($name, $company);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found"));
            return $this->set_response($response, 422);
        }
    }
    /*public function sanctions_post()
    { 
        $data = array(
            'applicant_id' =>  $this->post('aplid'),
            'sanction' => $this->post('sanction'),
            'section' => $this->post('section'),
            'remarks' => $this->post('remarks'),
            'url' => $this->post('url'),
        );
        $response = $this->Main_mdl->humanRelationsPost($data);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }*/
}

<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Finance extends Base_Controller{
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

   public function wage_create_record_post(){
            $data = $this->validate_inpt(array('id','company','name'), 'post');
            $app_data = array(
                "id" => $data["id"],
                "name" => $data["name"],
                "company" => $data["company"],
                "date_created" => date('Y-m-d H:i:s'),
                "data" =>  $this->post('data'),
                "status" => 0
            );

            $response = $this->Main_mdl->record_wage_data($app_data);
            if(!isset($response['status'])){
                $this->activity_logs($data["id"],"WAGEFAILED","FAILED", json_encode($app_data), 1);
                return $this->set_response($response, 422);
            }else{
                $this->activity_logs($data["id"],"ADDWAGE","SUCCESS", json_encode($app_data), 0);
                $this->set_response(array("status" => 200, "data" => $response),  200);
            }

    }


    public function wage_assign_record_post(){
        $data = $this->validate_inpt(array('id','company','store_id', 'wage_id'), 'post');
        $app_data = array(
            "emp_id" => $data["id"],
            "store_id" => $data["store_id"],
            "wage_id" => $data["wage_id"],
            "company" => $data["company"],
            "date_assigned" => date('Y-m-d H:i:s'),
        );

        $response = $this->Main_mdl->record_wage_assign_data($app_data);
        if(!isset($response['status'])){
            $this->activity_logs($data["id"],"WAGEFAILEDASSIGN","FAILED", json_encode($app_data), 1);
            return $this->set_response($response, 422);
        }else{
            $this->activity_logs($data["id"],"ASSIGNWAGESUCCESS","SUCCESS", json_encode($app_data), 0);
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }

    }


}

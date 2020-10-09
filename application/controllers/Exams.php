<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Record extends Base_Controller
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
    
           
    public function iq_create_post(){
        
        $score = 0;

        for($i = 1; $i <= 30; $i++){

        }

        $data = array(
            'applicant_id' => $this->post('applicant_id'),
            'data' => $this->post('data'),
            'score ' => $score,
            'status' => 0
        );

        $response = $this->Main_mdl->record_data($data);
        if(!isset($response['status'])){
            return $this->set_response($response, 422);
        }else{
            $this->set_response($response,  200); 
        }

       
        
    }
        

}
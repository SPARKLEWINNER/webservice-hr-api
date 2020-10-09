<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Exams extends Base_Controller
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
        // $data = $this->validate_inpt(array('email','password'), 'post');
        $answers = array("d","d","c","a","a","c","c","c","b","c","d","b","a","d","a","c","c","d","b","a","b","b","c","b","d");
        var_dump($answers);
        for($i = 1; $i <= 30; $i++){
            
        }

        $data = array(
            'applicant_id' => $this->post('applicant_id'),
            'data' => $this->post('data'),
            'score ' => $score,
            'status' => 0
        );

        $response = $this->Main_mdl->exam_data($data);
        if(!isset($response['status'])){
            return $this->set_response($response, 422);
        }else{
            $this->set_response($response,  200); 
        }

       
        
    }
        

}
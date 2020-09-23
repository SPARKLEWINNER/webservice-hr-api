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

    function __construct()
    {
        parent::__construct();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }
    
    public function create_post(){
  
        if(empty($this->post('uid')) && empty($this->post('cid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        if(empty($this->post('pid')) && empty($this->post('oid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }


        $uid = $this->post('uid');
        $cid = $this->post('cid');  
        $oid = $this->post('oid');  
        $pid = $this->post('pid');   
        $upload_proc = $this->upload($_FILES['record'], $uid, $oid);
   
        $data = array(
          "uid" => $uid,
          "cid" => $cid,
          "oid" => $oid,
          "pid" => $pid,
          "video" => $upload_proc['link'],
          "name" => $upload_proc['name'],
          "date_created" => date('Y-m-d H:i:s'),
          "status" => 0
        );
  
        if($upload_proc){
            $response = $this->Main_mdl->record_data($data);
            $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
       
        
    }
        
    public function remove_post(){
  
        if(empty($this->post('uid')) && empty($this->post('cid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        if(empty($this->post('oid'))) {
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        $uid = $this->post('uid');
        $cid = $this->post('cid');  
        $record_id = $this->post('record_id'); 
        $oid = $this->post('oid'); 
        $response = $this->Main_mdl->record_remove($uid, $cid, $record_id, $oid);
        if($response){
            $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
    public function records_get($uid = NULL, $cid = NULL, $oid = NULL){
        
        if(empty($uid) && empty($cid) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        
        if(empty($oid) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        

        
        $uid = $uid;
        $cid = $cid;
        $oid = $oid;
        $response = $this->Main_mdl->record_pull($uid,$cid,$oid);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
    public function review_record_patch(){

       if(empty($this->patch('status')) && empty($this->patch('id'))){
         $this->response_return($this->response_code(400,""));
         return false;
       }     
        
       $id = $this->patch('id'); // record_id
       $status = $this->patch('status'); // [0 = not submitted / 1 = submitted for review / 2 = reviewed / 3 = completed ] -- status 

       $data = array(
            "status" => $status
        );
        
        $response = $this->Main_mdl->submit_record($id,$data);

        if($response){
            return $this->set_response($response,  200);

        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);        
        }
        
    }
    
}
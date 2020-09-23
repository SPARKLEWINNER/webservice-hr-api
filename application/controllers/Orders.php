<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class Orders extends Base_Controller
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


        $uid = $this->post('uid');
        $cid = $this->post('cid');  
        $upload_proc = $this->upload($_FILES['record'], $uid);
   
        $data = array(
          "uid" => $uid,
          "cid" => $cid,
          "video" => $upload_proc['link'],
          "name" => $upload_proc['name'],
          "date_created" => date('Y-m-d H:i:s')
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
        $uid = $this->post('uid');
        $cid = $this->post('cid');  
        $record_id = $this->post('record_id');  
        $response = $this->Main_mdl->record_remove($uid, $cid, $record_id);
        if($response){
            $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Server upload error", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
    public function orders_get($uid = NULL, $product_id = NULL){
        
        if(empty($uid) && empty($product_id)){
            $this->response_return($this->response_code(400,""));
            return false;
        }
        
        $uid = $uid;
        $product_id = $product_id;
        $response = $this->Main_mdl->orders_pull($uid,$product_id);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
     public function orders_list_get($uid = NULL, $product_id = NULL, $status = NULL){
        
        if(empty($uid) && empty($product_id)){
            $this->response_return($this->response_code(400,""));
            return false;
        }
            
        if(empty($status)){
            $this->response_return($this->response_code(400,""));
            return false;
        }
            
        $uid = $uid;
        $product_id = $product_id;
        $status = $status;
        $response = $this->Main_mdl->order_pull_list($uid,$product_id,$status);
        
        if($response){
            return $this->set_response($response,  200);
        }else{
            $response = $this->response_code(422, "Unable to process your request", "");
            return $this->set_response($response, 422);
        }
       
        
    }
    
    
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Base_Model.php';

class Main_mdl extends Base_Model {
    public $videoStorage = "../wp-content/uploads/recorded_mobile/";

    public function __construct () {
        parent::__construct();
    
    }

    public function login($email, $password){
        $acc = $this->db->select('password,id,email,first_name,last_name,profile,user_level')->from('users')->where('email', $email)->get()->row();
        if(!$acc) return $this->response_code(204,"User invalid", "");

        if(password_verify($password,  $acc->password)):
            return array(
                "id" => $acc->id,
                "email" =>$acc->email,
                "firstname" => $acc->first_name,
                "lastname" => $acc->last_name,
                "" => $acc->profile,
                "profile" => $acc->profile,
                "user_level" => $acc->user_level
            );
            
        else:
            $check_temporary_account = $this->temporary_login($email, $password);
            if(!$check_temporary_account) return $this->response_code(204,"User invalid", "");
            return $check_temporary_account;

        endif;
    }

    public function temporary_login($email, $password){
        $statement = array('username' => $email, 'reference_id' => $password);
        $acc = $this->db->select('*')->from('applications')->where($statement)->get()->row();

        if(!$acc) return false;
        return array(
            "id" => $acc->id,
            "applicant_id" => $acc->applicant_id,
            "reference_id" =>$acc->reference_id,
            "date_created" => $acc->date_created,
            "data" => $acc->data,
            "account_status" => $acc->status,
            "reviewer" => $acc->reviewer,
            "notification" => $acc->notification,
            "username" => $acc->username,
            "password" => $acc->password,
            "user_level" => "1",
            "profile" => $acc->profile,
            "company" => $acc->company
        );
      
        
    }

    public function recordToken($id, $token){
        $this->db->where('id', $id);
        $this->db->update('keys', array("key" => $token));
    }

    public function retrieveUser($email, $password){
        $acc = $this->db->select('id,email,first_name,last_name,profile')->from('users')->where('email', $email)->get()->row();
        if(!$acc) return $this->response_code(204,"User invalid", "");
        $update = array("password"=> $password, "token" => $password);
        $this->db->where('id', $acc->id);
        $this->db->update('users', $update);

        return array(
            "id" => $acc->id,
        );
    }

    public function resetUser($data){
        $acc = $this->db->select('id,email,first_name,last_name,profile,token')->from('users')->where('email', $data['email'])->get()->row();
        if(!$acc) return $this->response_code(204,"", "");
        
        if($acc->token === "" || empty($acc->token)) return $this->response_code(204,"Invalid token","");

        if($data['hash'] ==  $acc->token):

            $update = array(
                "password" => password_hash($data['password'], PASSWORD_DEFAULT),
                "token" => ""
            );
            $this->db->where('id', $acc->id);
            $this->db->update('users', $update);

            return array(
                "id" => $acc->id,
                "email" =>$acc->email,
                "firstname" => $acc->first_name,
                "lastname" => $acc->last_name,
                "profile" => $acc->profile
            );

        else:
          return false;
        endif;
    }

    /** Exams **/
    
        public function exam_data($data){

            $this->db->insert('exams', $data);
            $inserted_id = $this->db->insert_id();
            
            $exam = $this->db->select('*')->from('exams')->where('id', $inserted_id)->get()->row();
    
            if($this->db->affected_rows() > 0):    
                return array(
                    "id" => $this->db->insert_id(),
                    "applicant_id" => $exam->applicant_id,
                    "date_created" => $exam->date_created,
                    "type" => $exam->type,
                    "data" => $exam->data,
                    "score" => $exam->score,
                    "exam_status" => $exam->status,
                    "reviewer" => $exam->reviewer,
                );  
            else: return false;
            endif;
    
        }
    
    /** Records **/
    
    public function record_data($data){

        $this->db->insert('applications', $data);
        $inserted_id = $this->db->insert_id();
        
        $record = $this->db->select('*')->from('applications')->where('id', $inserted_id)->get()->row();

        if($this->db->affected_rows() > 0):    
            return array(
                "id" => $this->db->insert_id(),
                "applicant_id" => $record->applicant_id,
                "reference_id" => $record->reference_id,
                "data" => $record->data,
                "date_created" => $record->date_created,
                "status" => $record->status,
                "reviewer" => $record->reviewer,
                "notification" => $record->notification,
                "username" => $record->username,
                "company" => $record->company,
                "profile" => $record->profile
            );  
        else: return false;
        endif;

    }
    
    public function record_pull($uid,$cid,$oid){

        $query = "SELECT * FROM records WHERE uid = {$uid} AND  cid = {$cid} AND oid = {$oid} ORDER BY id DESC"; 
        $result = $this->db->query($query);
        $data = array();
        foreach($result->result_array() as $arr => $key){
            $file_pointer = $this->videoStorage.$key['name'];
            if (!file_exists($file_pointer)) {
                continue;
            }else{
                $data[] = $key;
            }          
        }
        return ($result->num_rows() > 0) ? $data : false;

    }
    
    public function record_remove($uid, $cid, $record_id, $oid){
        $query = "SELECT * FROM records WHERE id = {$record_id}  AND oid = {$oid} ORDER BY id DESC";
        $result = $this->db->query($query);
        if (unlink($this->videoStorage . "" . $result->result()[0]->name)) {
            $this->db->where('id', $record_id);
            $this->db->where('oid', $oid);
            $this->db->delete("records");
            return ($this->db->affected_rows() > 0) ? array("Removed successfully") : false;
        }
        
        echo $record_id;
        $this->db->where('id', $record_id);
        $this->db->delete('records');
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    
    
    public function submit_record($id,$data){
    
        $this->db->where('id', $id);
        $this->db->update('records', $data);
    
        if($this->db->affected_rows() > 0):
            $record = $this->db->select('id,status')->from('records')->where('id', $id)->get()->row();
            return array(
                "id" => $id,
                "status" => $record->status
            );
        else:
            return false;
        endif;
    }
    
    
    /** Accounts **/
    
    public function new_user($data){
        
        $validate_email = "SELECT * FROM users WHERE email = '{$data['email']}'";
        $result_vd = $this->db->query($validate_email);
        
        if($result_vd->num_rows() > 0){
            return $this->response_code(204,"Email already exist", "");
        }else{
            $this->db->insert('users', $data);
            $inserted_id = $this->db->insert_id();
            
            $record = $this->db->select('id,first_name,last_name,email,profile,date_created,filename')->from('users')->where('id', $inserted_id)->get()->row();
            
            if($this->db->affected_rows() > 0):    
                return array(
                  "id" => $this->db->insert_id(),
                  "first_name" => $record->first_name,
                  "last_name" => $record->last_name,
                  "email" => $record->email,
                  "profile" => $record->profile,
                  "filename" => $record->filename,
                  "date_created" => $record->date_created,
                );  
            else: return false;
            endif;
        }
    

  
    }
    
    public function check_user($uid, $product_id){
        $query = "SELECT * FROM users WHERE id = {$uid} AND  product_id = {$product_id} ORDER BY id DESC";
        $result = $this->db->query($query);

        return ($result->num_rows() > 0) ? true : false;
    }
        
    public function user_pull($id){
        
        $query = "SELECT * FROM applications WHERE id = '{$id}' LIMIT 1";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }

    
    public function update_user($uid,$post_id,$data){
        $this->db->where('post_id', $post_id);
        $this->db->where('meta_key', '_price');
        $this->db->update('wp_postmeta', $data);
        
        $acc = $this->db->select('id,email,first_name,last_name,profile,product_id')->from('users')->where('id', $uid)->get()->row();
        $woocom_meta = $this->db->select('meta_value')->from('wp_postmeta')->where('meta_key','_price')->where('post_id',$acc->product_id)->get()->row();
        $id = $acc->id;

        if(!$acc) return $this->response_code(204,"User invalid", "");

        $result = array(
            "id" => $id,
            "email" => $acc->email,
            "firstname" => $acc->first_name,
            "lastname" => $acc->last_name,
            "profile" => $acc->profile,
            "product_id" => $acc->product_id,
            "rate" => $woocom_meta->meta_value
        );
            
        return ($this->db->affected_rows() > 0) ? $result : false;
    }
    
    public function update_details_user($id,$data){
       $acc = $this->db->select('password,id,email,first_name,last_name,profile,product_id')->from('users')->where('id', $id)->get()->row();
       $woocom_meta = $this->db->select('meta_value')->from('wp_postmeta')->where('meta_key','_price')->where('post_id',$acc->product_id)->get()->row();
       $woocom_details = "SELECT p.*, ( SELECT guid FROM wp_posts WHERE id = m.meta_value ) AS imgurl,  (SELECT meta_value FROM wp_postmeta pm WHERE meta_key='_wp_attachment_metadata' AND pm.post_id=m.meta_value ) AS imgdetails FROM wp_posts p
       LEFT JOIN  wp_postmeta m ON(p.id = m.post_id AND m.meta_key =  '_thumbnail_id' ) WHERE p.post_type =  'product' AND p.id= {$acc->product_id}";
       $woo_details = $this->db->query($woocom_details);
             
       if($woo_details->num_rows() > 0){
          $woo_details = $woo_details->result()[0];
       }
                  
        if(!$acc || $acc->email != $data['old']) return $this->response_code(204,"User invalid details", "");
       
        $new_email = array("email"=> $data['new']);  
        $this->db->where('id', $id);
        $this->db->update('users', $new_email);
    
        if($this->db->affected_rows() > 0):
            return array(
               "id" => $id,
               "email" => $data['new'],
               "firstname" => $woo_details->post_title,
               "lastname" => " ",
               "profile" => $woo_details->imgurl,
               "product_id" => $acc->product_id,
               "rate" => $woocom_meta->meta_value,
            );
        else:
            return false;
        endif;
    }
    
    
    public function update_user_password($id, $passwords){
       
        $acc = $this->db->select('password,id,email,first_name,last_name,profile,product_id')->from('users')->where('id', $id)->get()->row();
        $woocom_meta = $this->db->select('meta_value')->from('wp_postmeta')->where('meta_key','_price')->where('post_id',$acc->product_id)->get()->row();
        $woocom_details = "SELECT p.*, ( SELECT guid FROM wp_posts WHERE id = m.meta_value ) AS imgurl,  (SELECT meta_value FROM wp_postmeta pm WHERE meta_key='_wp_attachment_metadata' AND pm.post_id=m.meta_value ) AS imgdetails FROM wp_posts p
        LEFT JOIN  wp_postmeta m ON(p.id = m.post_id AND m.meta_key =  '_thumbnail_id' ) WHERE p.post_type =  'product' AND p.id= {$acc->product_id}";
        $woo_details = $this->db->query($woocom_details);
         
        if($woo_details->num_rows() > 0){
            $woo_details = $woo_details->result()[0];
        }
       
        $grab_password =  $acc->password;
        $grab_email =  $acc->email;
        $id = $acc->id;
        

        if(!$acc) return $this->response_code(204,"User invalid", "");
        
        if(password_verify($passwords['old'],  $grab_password)):
            $data = array(
                "password" => password_hash($passwords['new'], PASSWORD_DEFAULT)
            );
         
        
            $this->db->where('id', $id);
            $this->db->update('users', $data);
       
            if($this->db->affected_rows() > 0):
                return array(
                    "id" => $id,
                    "email" => $grab_email,
                    "firstname" => $woo_details->post_title,
                    "lastname" => " ",
                    "profile" => $woo_details->imgurl,
                    "product_id" => $acc->product_id,
                    "rate" => $woocom_meta->meta_value,
                );
            else:
              return $this->response_code(204,"User unable to change current password", "");
            endif;   
        else:
          return $this->response_code(204,"User invalid current password", "");

        endif;
    }
    
    
    public function set_token($uid, $data){
        $this->db->where('id', $uid);
        $this->db->update('users', $data);
        
        $acc = $this->db->select('id,token')->from('users')->where('id', $uid)->get()->row();
        
        if(!$acc) return $this->response_code(204,"User invalid", "");
        $result = array(
            "id" => $acc->id,
            "token" => $acc->token
        );
        return ($this->db->affected_rows() > 0) ? $result : false;
        
    }
    
    /** Orders **/
    
    public function orders_pull($uid,$product_id){
        
        $pre_requisites = "SELECT DISTINCT ORDERS.order_id, POSTS.post_status status, ORDERS.order_item_id as ordermetaId, WPUSERS.user_nicename AS client, ORDERSMETA.meta_value AS prod_id, CIUSERS.id AS uid, 
        WPUSERS.ID as cid, POSTS.post_date as date_posted FROM wp_woocommerce_order_items ORDERS 
        LEFT JOIN wp_woocommerce_order_itemmeta ORDERSMETA ON ORDERS.order_item_id = ORDERSMETA.order_item_id 
        LEFT JOIN wp_posts POSTS ON ORDERS.order_id = POSTS.ID 
        LEFT JOIN wp_postmeta POSTMETA ON POSTS.ID = POSTMETA.post_id 
        LEFT JOIN users CIUSERS ON ORDERSMETA.meta_value = CIUSERS.product_id 
        LEFT JOIN wp_users WPUSERS ON WPUSERS.ID = POSTS.post_author 
        WHERE ORDERSMETA.meta_key = '_product_id' AND ORDERSMETA.meta_value = {$product_id} AND CIUSERS.id = {$uid} AND POSTS.post_status != 'trash'";
        $result = $this->db->query($pre_requisites);
        $result_array = $this->db->query($pre_requisites)->result_array();
        
        
        foreach($result_array as $k => $pre_req){  
            $meta_id = $pre_req['ordermetaId'];
            $message = $this->db->select('meta_value')->from('wp_woocommerce_order_itemmeta')->where('meta_key', "Video message")->where('order_item_id', $meta_id)->get()->row();
            $total = $this->db->select('meta_value')->from('wp_woocommerce_order_itemmeta')->where('meta_key', "_line_total")->where('order_item_id', $meta_id)->get()->row();
            $result_array[$k]['message'] = $message->meta_value;
            $result_array[$k]['total'] = $total->meta_value;
        }

        return ($result->num_rows() > 0) ? $result_array : false;

    }
    
    public function order_pull_list($uid, $product_id, $status){
        $pre_requisites = "SELECT DISTINCT ORDERS.order_id, POSTS.post_status status, ORDERS.order_item_id as ordermetaId, WPUSERS.user_nicename AS client, ORDERSMETA.meta_value AS prod_id, CIUSERS.id AS uid, 
        WPUSERS.ID as cid, POSTS.post_date as date_posted FROM wp_woocommerce_order_items ORDERS 
        LEFT JOIN wp_woocommerce_order_itemmeta ORDERSMETA ON ORDERS.order_item_id = ORDERSMETA.order_item_id 
        LEFT JOIN wp_posts POSTS ON ORDERS.order_id = POSTS.ID 
        LEFT JOIN wp_postmeta POSTMETA ON POSTS.ID = POSTMETA.post_id 
        LEFT JOIN users CIUSERS ON ORDERSMETA.meta_value = CIUSERS.product_id 
        LEFT JOIN wp_users WPUSERS ON WPUSERS.ID = POSTS.post_author 
        WHERE ORDERSMETA.meta_key = '_product_id' AND ORDERSMETA.meta_value = {$product_id} AND CIUSERS.id = {$uid} AND POSTS.post_status = '{$status}'";
        $result = $this->db->query($pre_requisites);
        $result_array = $this->db->query($pre_requisites)->result_array();
        
        
        foreach($result_array as $k => $pre_req){       
            $meta_id = $pre_req['ordermetaId'];
            $oid = $pre_req['order_id'];
            $uid = $pre_req['uid'];
            
            $message = $this->db->select('meta_value')->from('wp_woocommerce_order_itemmeta')->where('meta_key', "Video message")->where('order_item_id', $meta_id)->get()->row();
            $total = $this->db->select('meta_value')->from('wp_woocommerce_order_itemmeta')->where('meta_key', "_line_total")->where('order_item_id', $meta_id)->get()->row();
            $video = $this->db->select('video')->from('records')->where('oid', $oid)->where('uid', $uid)->get()->row();
            
            $result_array[$k]['message'] = $message->meta_value;
            $result_array[$k]['total'] = $total->meta_value;
            $result_array[$k]['video'] = !empty($video->video) ? $video->video : ""; 
        }

        return ($result->num_rows() > 0) ? $result_array : false;
    }
    
    /** Notification **/
    
    public function notify_user($data){

        $this->db->insert('notifications', $data);
        $inserted_id = $this->db->insert_id();
        
        $notify = $this->db->select('*')->from('notifications')->where('id', $inserted_id)->get()->row();
        $token = $this->db->select('token')->from('users')->where('product_id',$notify->uid)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
              "id" => $this->db->insert_id(),
              "uid" => $notify->uid,
              "oid" => $notify->oid,
              "message" => $notify->message,
              "status" => $notify->status,
              "date_created" => $notify->date_created,
              "token" => $token->token
            );  
        else: return false;
        endif;
  
    }
    

/*
    public function generate_token($password, $grab_password, $id, $grab_email){
        if(password_verify($password,  $grab_password)):
            $last_login = date('Y-m-d H:i:s');
            $token = password_hash($id."".$grab_email, PASSWORD_BCRYPT);
            $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));

            $this->db->where('id',$id)->update('users',array('last_login' => $last_login));
            $result = $this->db->insert('user_token',
                array(
                    'user_id' => $id,
                    'token' => $token,
                    'expired_at' => $expired_at
                )
            );
            
            if ($result === FALSE):
                return $this->response_code(500,"", "");
            else:
                
                return $this->response_code(200, 'Successfully login.',
                    array('id' => $id, 
                    'token' => $token
                ));
                
            endif;

        endif;

    }
 */   
    public function logout()
    {
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token  = $this->input->get_request_header('Authorization', TRUE);

        $token_validity = $this->db->select('user_id, token')->from('user_token')->where('user_id', $users_id)->where('token', $token)->get()->row();

        if(!$token_validity) return $this->response_code(401,"", ""); 

        $this->db->where('user_id',$users_id)->where('token',$token)->delete('user_token');
        return $this->response_code(200, '', '');
    }
}

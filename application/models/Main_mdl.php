<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');
require APPPATH . '/libraries/Base_Model.php';

class Main_mdl extends Base_Model {
    public $videoStorage = "../wp-content/uploads/recorded_mobile/";

    public function __construct () {
        parent::__construct();
    
    }

    public function login($email, $password){
        
        $acc = $this->db->select('*')->from('users')->where('email', $email)->get()->row();
        if(isset($acc)){
            if(password_verify($password,  $acc->password)){
                $this->db->where('id', $acc->id);
                $this->db->update('users', array("last_login" => date('Y-m-d H:i:s') ));

                if($acc->user_level == 3){
                    return array(
                        "id" => $acc->id,
                        "email" =>$acc->email,
                        "firstname" => $acc->first_name,
                        "lastname" => $acc->last_name,
                        "company" => $acc->company,
                        "profile" => $acc->profile,
                        "user_level" => $acc->user_level
                    );
                }else{

                    if($acc->user_level == 5){
                        $asg = $this->db->select('*')->from('assigning')->where('emp_id', $acc->id)->get()->row();
                        $store = $this->db->select('*')->from('store')->where('id', $asg->store_id)->get()->row();
                        return array(
                            "id" => $acc->id,
                            "email" =>$acc->email,
                            "firstname" => $acc->first_name,
                            "lastname" => $acc->last_name,
                            "company" => $acc->company,
                            "profile" => $acc->profile,
                            "user_level" => $acc->user_level,
                            "store_id" => $store->id,
                            "store_name" => $store->name
                        );
                    }else{
                        return array(
                            "id" => $acc->id,
                            "email" =>$acc->email,
                            "firstname" => $acc->first_name,
                            "lastname" => $acc->last_name,
                            "company" => $acc->company,
                            "profile" => $acc->profile,
                            "user_level" => $acc->user_level,
                        );
                    }
                }
            }
            else{
                return $this->response_code(204,"User invalid", "");
            }
        }

        else{
            $check_temporary_account = $this->temporary_login($email, $password);
            if(!$check_temporary_account) return $this->response_code(204,"User invalid", "");
            return $check_temporary_account;

        }
    }

    public function temporary_login($email, $password){
        $statement = array('username' => $email, 'reference_id' => $password);
        $acc = $this->db->select('*')->from('applications')->where($statement)->get()->row();

        if($acc == null){
            return false;
        }else{
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
                "user_level" => 10,
                "profile" => $acc->profile,
                "company" => $acc->company
            );
        }
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

    public function record_upload_doc($data){
        $this->db->insert('records', $data);
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function record_upload_activity($data){
        $this->db->insert('upload', $data);
        return $this->db->affected_rows() > 0 ? true : false;
    }
    
    
    public function records_doc_pull($id,$company){
        $this->db->where('id', $id);
        $this->db->update('applications', array("status" => 5));
        if($this->db->affected_rows() > 0):    
            return $this->db->select('*')->from('records')->where('applicant_id', $id)->get()->row();
        else: return false;
        endif;
    }

    public function record_review_data($id,$data){

        $this->db->insert('reviews', $data);
        $inserted_id = $this->db->insert_id();
        
        $record = $this->db->select('*')->from('reviews')->where('id', $inserted_id)->get()->row();

        if($this->db->affected_rows() > 0):    
            $this->db->where('id', $id);
            $this->db->update('applications', array("status" => 3));
    
            return array(
                "id" => $inserted_id,
                "applicant_id" => $record->applicant_id,
                "reference_id" => $record->reference_id,
                "recruitment" => $record->recruitment,
                "reviewer" => $record->recruitment_reviewer,
                "assess_evaluations" => $record->assess_evaluation
            );  
        else: return false;
        endif;

    }    

    public function record_applying_for($job_id, $applicant_id){

        $result = $this->db->select('*')->from('applications')->where('id', $applicant_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            if($result->applying_for == 0){
                $this->db->where('id', $applicant_id);
                $this->db->update('applications', array("applying_for" => $job_id));
            }
        endif;
    }

    public function record_review_store_data($data){
        $app_id = $data['id'];
        $app_data = array(
            "store_assess" => $data['store_assess'],
            "reviewer" => $data['reviewer'],
            "review_status" => $data['review_status'],
            "store_review_date" => date('Y-m-d H:i:s')
        );

        $this->db->where('applicant_id', $app_id);
        $this->db->update('reviews', $app_data);

        if($this->db->affected_rows() > 0):    
            $record = $this->db->select('*')->from('reviews')->where('applicant_id', $app_id)->get()->row();
            $this->db->where('id', $app_id);
            $this->db->update('applications', array("status" => 4));
    
            return array(
                "id" => $app_id,
                "applicant_id" => $record->applicant_id,
                "reference_id" => $record->reference_id,
                "recruitment" => $record->recruitment,
                "reviewer" => $record->reviewer,
                "reviewer_status" => $record->review_status
            );  
        else: return false;
        endif;

    }    

    public function record_exam_data($data){
        $this->db->insert('exams', $data);
        $inserted_id = $this->db->insert_id();
        $exams = $this->db->select('*')->from('exams')->where('id', $inserted_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
                "id" => $inserted_id,
                "applicant_id" => $exams->applicant_id,
                "job_id" => $exams->job_id,
                "exam_id" => $exams->exam_id,
                "date_created" => $exams->date_created,
                "status" => $exams->status,
            );  
        else:
            return false;
        endif;
    }    

    public function record_pull($company){

        $query = "SELECT * FROM `applications` where `company` = '{$company}'"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }

    public function record_status_pull($company, $status){

        $query = "SELECT * FROM `applications` where `company` = '{$company}' AND `status` = '{$status}' ORDER BY id DESC"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }

    public function record_weeks_pull($company, $weeks){

        $query = "SELECT * FROM `applications` where `company` = '{$company}' AND date_created < now() - interval {$weeks} week"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }

    public function record_day_pull($company, $days){

        $query = "SELECT * FROM `applications` where `company` = '{$company}' AND date_created  >= DATE(NOW()) - INTERVAL {$days} DAY AND status = 0 ORDER BY id DESC"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    /* Pool */
    public function record_pool_weeks_pull($company, $weeks){

        $query = "SELECT * FROM `applications` where `company` = '{$company}' AND date_created < now() - interval {$weeks} week AND status = 0"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }

    public function record_pool_day_pull($company, $days){

        $query = "SELECT * FROM `applications` where `company` = '{$company}' AND date_created  >= DATE(NOW()) - INTERVAL {$days} DAY"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }
    
    /* Specific */
    public function record_specific_pull($company, $id){

        $query = "SELECT app.*, st.meta_value FROM `applications` app  
        LEFT JOIN `settings` st ON app.applying_for = st.id WHERE app.company = '{$company}' AND app.id = '{$id}' LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        if($result->num_rows() > 0){
            $exams = "SELECT * FROM `exams` WHERE applicant_id = '{$id}'";
            $reviews = "SELECT * FROM `records` WHERE applicant_id = '{$id}'";
            $data[0]['taken_exam'][] = $this->db->query($exams)->result_array();
            $data[0]['documents'][] = $this->db->query($reviews)->result_array();
            return $data;
        } else{

            return false;
        }


    }
    
    public function record_reviews_pull($company, $id){

        $query = "SELECT * FROM `reviews` WHERE `company` = '{$company}' AND `applicant_id` = '{$id}' LIMIT 1"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }

    // Team Supervisor

    public function record_ts_specific_pull($company, $ref_id){

        $query = "SELECT app.*, st.meta_value FROM `applications` app  
        LEFT JOIN `settings` st ON app.applying_for = st.id WHERE app.company = '{$company}' AND app.reference_id = '{$ref_id}' LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        if($result->num_rows() > 0){
            $exams = "SELECT * FROM `exams` WHERE applicant_id = '{$ref_id}'";
            $data[0]['taken_exam'][] = $this->db->query($exams)->result_array();
            return $data;
        } else{

            return false;
        }
    }
    
    public function record_ts_reviews_pull($company, $ref_id){
        $apl = $this->db->select('*')->from('applications')->where('reference_id', $ref_id)->where('company', $company)->get()->row();
        $query = "SELECT * FROM `reviews` WHERE `company` = '{$company}' AND `applicant_id` = '{$apl->id}' LIMIT 1"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }
    
    public function record_stores_pull($company){

        $query = "SELECT * FROM `store` WHERE `company` = '{$company}'"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }
    
    
    public function records_store_people_pull($company, $store_id){

        $applications_q = "SELECT *, apls.reference_id as gen_id FROM applications apls 
        LEFT JOIN reviews rvws ON apls.id = rvws.applicant_id WHERE apls.company = '{$company}' AND rvws.store = $store_id";
        $result = $this->db->query($applications_q);
        $appls = $result->result_array();

        $store_results = array();
        if($result->num_rows() > 0 ){
            foreach($appls as $k => $apls){
                if($apls['recruitment'] && json_decode($apls['recruitment'])->assess_evaluation == 1):
                    $store_results = $apls;
                    $store_deploy = $apls['store'];
                    if($store_deploy != NULL){
                        $store_details = "SELECT * FROM store WHERE company = '{$company}' AND id = {$store_id}";
                        $store_result = $this->db->query($store_details);

                        $applying_for = json_decode($apls['applying_for']);
                        $job_details = $this->db->select('*')->from('settings')->where('id', $applying_for)->where('company', $company)->get()->row();
                        if($store_result->num_rows() > 0){
                            $store_results['store_name'] = $store_result->result_array()[0]['name']; 
                            $store_results['store_id'] = $store_result->result_array()[0]['id']; 
                            $store_results['job_title'] = json_decode($job_details->meta_value)->title; 
                        }
                    }
                endif;
            }
        }

        return ($result->num_rows() > 0) ? $store_results : false;

    }
    
    public function record_emails_pull($company){

        $query = "SELECT * FROM `system` WHERE `company` = '{$company}'"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }
    
    public function record_logs_pull($company){

        $query = "SELECT * FROM `logs` WHERE `company` = '{$company}'"; 
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;

    }
    
    public function record_exam_logs_pull($company){

        $applicants = "SELECT * FROM `applications` where `company` = '{$company}'"; 
        $result = $this->db->query($applicants);
        if($result->num_rows() > 0){
            $app_res = $result->result_array();

            foreach($app_res as $key => $value){
                $app_res[$key]['exams'] = array();
                $exams = "SELECT * FROM exams"; 
                $exams_takers = $this->db->query($exams)->result_array();

                foreach($exams_takers as $k => $v){
                    if($value['id'] == $exams_takers[$k]['applicant_id']){

                        $stgs_exms = "SELECT * FROM settings WHERE id = '{$exams_takers[$k]["exam_id"]}' AND meta_key = 'exams'";
                        $stgs_details = $this->db->query($stgs_exms)->result_array();
                        if($stgs_details){

                            $exam_title = json_decode($stgs_details[0]['meta_value'])->title;
                            if($exams_takers[$k]){
                                $exams_takers[$k]['title'] = $exam_title;
                                $app_res[$key]['exams'][] =  $exams_takers[$k];
                            }
                        }
                    }
                }
            }
            
        }
        return ($result->num_rows() > 0) ? $app_res : false;

    }
    
    public function record_patch_data($data,$status){
        $this->db->where('id', $data['id']);
        $this->db->update('applications', array("status" => $status));
    
        if($this->db->affected_rows() > 0):
            $result = $this->db->select('id,status')->from('applications')->where('id', $data['id'])->get()->row();
            return array(
                "id" => $result->id,
                "status" => $result->status
            );
        else:
            return false;
        endif;
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

    /* Stores */

    public function system_record_store($data){
        $this->db->insert('store', $data);
        $inserted_id = $this->db->insert_id();
        $store = $this->db->select('*')->from('store')->where('id', $inserted_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
              "id" => $inserted_id,
              "name" => $store->name,
              "details" => $store->details,
              "company" => $store->company,
              "status" => $store->status,
              "date_created" => $store->date_created
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

    /* People */
    public function system_record_people($data, $temp_password){
        $validate_acc = $this->db->select('*')->from('users')->where('email', $data['email'])->get();
        if($validate_acc->num_rows() == 0){
            $this->db->insert('users', $data);
            $inserted_id = $this->db->insert_id();
            $people = $this->db->select('*')->from('users')->where('id', $inserted_id)->get()->row();
            if($this->db->affected_rows() > 0):    
                return array(
                  "id" => $inserted_id,
                  "company" => $people->company,
                  "email" => $people->email,
                  "first_name" => $people->first_name,
                  "last_name" => $people->last_name,
                  "user_level" => $people->user_level,
                  "date_created" => $people->date_created,
                  "temp_password" => $temp_password,
                ); 
            else:
                return false;
            endif;
        }else{
            return false;
        }
    }

    public function system_record_people_password($data, $old_password){
        $validate_acc = $this->db->select('*')->from('users')->where('email', $data['email'])->get()->row();
        if($validate_acc->num_rows() == 0){
            if(password_verify($old_password,  $acc->password)){
                $this->db->where('id', $validate_acc->id);
                $this->db->update('users', $data);
                if($this->db->affected_rows() > 0):
                    return array(
                        "id" => $acc->id,
                        "email" =>$acc->email,
                        "firstname" => $acc->first_name,
                        "lastname" => $acc->last_name,
                        "company" => $acc->company,
                        "profile" => $acc->profile,
                        "user_level" => $acc->user_level
                    );
                else:
                    return $this->response_code(204,"User invalid", "");
                endif;
            }
            else{
                return $this->response_code(204,"User invalid", "");
            }
        }else{
            return $this->response_code(204,"User invalid", "");
        }
    }

    public function system_record_reset_people($data, $temp_password){
        $validate_acc = $this->db->select('*')->from('users')->where('email', $data['email'])->get()->row();
        if($validate_acc){
            $this->db->where('id', $validate_acc->id);
            $this->db->update('users', $data);
            $people = $this->db->select('*')->from('users')->where('id', $validate_acc->id)->get()->row();
            if($this->db->affected_rows() > 0):    
                return array(
                  "id" => $validate_acc->id,
                  "company" => $people->company,
                  "email" => $people->email,
                  "first_name" => $people->first_name,
                  "last_name" => $people->last_name,
                  "user_level" => $people->user_level,
                  "date_created" => $people->date_created,
                  "temp_password" => $temp_password,
                ); 
            else:
                return false;
            endif;
        }else{
            return false;
        }
    }

    public function system_people_assign($data){
            $this->db->insert('assigning', $data);
            $inserted_id = $this->db->insert_id();
            $assign = $this->db->select('*')->from('assigning')->where('id', $inserted_id)->get()->row();
            if($this->db->affected_rows() > 0):    
                return array(
                  "id" => $inserted_id,
                  "emp_id" => $assign->emp_id,
                  "store_id" => $assign->store_id,
                  "company" => $assign->company,
                  "date_assigned" => $assign->date_assigned,
                ); 
            else:
                return false;
            endif;
    }

    
    public function system_people_pull($company){
        $query = "SELECT * FROM users WHERE user_level != 3 AND  company = '{$company}' ORDER BY id DESC";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }
    
    public function system_people_specific_pull($company, $id){
        $query = "SELECT * FROM users WHERE user_level != 3 AND  company = '{$company}' AND id = {$id} ORDER BY id DESC";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    /* Log activity */

        
    public function record_log($data){
        $this->db->insert('activity', $data);
        return $this->db->affected_rows() != 1  ? false : true;
    }
        
    public function record_system($data){
        $this->db->insert('system', $data);
        return $this->db->affected_rows() != 1  ? false : true;
    }


    public function system_record_jobs($data){
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $jobs = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
              "id" => $inserted_id,
              "company" => $jobs->company,
              "posted_by" => $jobs->posted_by,
              "meta_key" => $jobs->meta_key,
              "meta_value" => $jobs->meta_value,
              "date_created" => $jobs->date_created,
            ); 
        else:
            return false;
        endif;
       
    }

    public function system_record_exams($data){
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $jobs = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
              "id" => $inserted_id,
              "company" => $jobs->company,
              "posted_by" => $jobs->posted_by,
              "meta_key" => $jobs->meta_key,
              "meta_value" => $jobs->meta_value,
              "date_created" => $jobs->date_created,
            ); 
        else:
            return false;
        endif;
    }

    public function system_record_update_exams($data,$exam_id){

        $this->db->where('id', $exam_id);
        $this->db->update('settings', $data);
    
        if($this->db->affected_rows() > 0):
            $jobs = $this->db->select('*')->from('settings')->where('id', $exam_id)->get()->row();
            return array(
                "id" => $jobs->id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
              ); 
        else:
            return false;
        endif;
    }


    public function system_record_remove_exams($exam_id){

        $this->db->where('id', $exam_id);
        $this->db->delete("settings");

        if($this->db->affected_rows() > 0):
            return true;
        else:
            return false;
        endif;
    }

    /* Requirements */

    public function system_record_requirements($data){
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $jobs = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
            "id" => $inserted_id,
            "company" => $jobs->company,
            "posted_by" => $jobs->posted_by,
            "meta_key" => $jobs->meta_key,
            "meta_value" => $jobs->meta_value,
            "date_created" => $jobs->date_created,
            ); 
        else:
            return false;
        endif;
    }


    public function system_update_requirements($data, $req_id){
        $this->db->where('id', $req_id);
        $this->db->update('settings', $data);
        $jobs = $this->db->select('*')->from('settings')->where('id', $req_id)->get()->row();
        if($this->db->affected_rows() > 0):    
            return array(
                "id" => $req_id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            ); 
        else:
            return false;
        endif;
    }


    /* Email */

    public function system_record_update_email($data,$email_id){
        $applicants = $this->db->select('*')->from('applications')->where('reference_id', $email_id)->get()->row();
        if($this->db->affected_rows() > 0):    

            $profile = json_decode($applicants->data);
            $profile->person_email = $applicants->username = $data['email'];
            $update_application = array(
              "data" => json_encode($profile),
              "username" => $applicants->username
            ); 

            $this->db->where('reference_id', $email_id);
            $this->db->update('applications', $update_application);
        
            if($this->db->affected_rows() > 0):

                // get the system table's data
                $system = $this->db->select('*')->from('system')->where('user', $email_id)->get()->row();
                $mail_data = json_decode($system->data);
                $mail_data->personalizations[0]->to = $mail_data->personalizations[0]->dynamic_template_data->email = $data['email'];
                $mail_new_data = array(
                  "data" => json_encode($mail_data),
                  "email" => $data['email']
                ); 
    
                $this->db->where('user', $email_id);
                $this->db->update('system', $mail_new_data);

                if($this->db->affected_rows() > 0):
                    $system = $this->db->select('*')->from('system')->where('user', $email_id)->get()->row();
                    return array(
                        "id" => $system->id,
                        "user" => $system->user,
                        "type" => $system->type,
                        "message" => $system->message,
                        "data" => $system->data,
                        "email" => $system->email,
                    ); 

                else:
                    return false;
                endif; // update for system table

            else:
                return false;
            endif; // update for application table

        else:
            return false;
        endif; // get of applicant's profile


    }



    public function record_get_system($ref_id){
        $applicant = $this->db->select('*')->from('applications')->where('reference_id', $ref_id)->get()->row();
        $system = $this->db->select('*')->from('system')->where('user', $ref_id)->get()->row();
        if($this->db->affected_rows() > 0){
            return array(
                "reference_id" => $applicant->reference_id,
                "username" => $applicant->username,
                "company" => $applicant->company,
                "email_details" => $system->data,
            );
        }else{
            return false;
        }
        

    }

    public function system_jobs_pull($company,$id,$jobs){
  
        $jobs = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = '{$jobs}'"; 
        $result = $this->db->query($jobs);
        if($result->num_rows() > 0){
            $jobs_result = $result->result_array();


            foreach($jobs_result as $key => $value){
                $jobs_result[$key]['exams'] = array();
                $exams = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'exams'"; 
                $exams_result = $this->db->query($exams)->result_array();

                $jobs_result[$key]['requirements'] = array();
                $requirements = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'requirements'"; 
                $requirements_result = $this->db->query($requirements)->result_array();
                foreach($requirements_result as $kr => $vr){
                    if($value['id'] == json_decode($requirements_result[$kr]['meta_value'])->job_id){
                        $jobs_result[$key]['requirements'][] =  $requirements_result[$kr];
                    }
                }

                foreach($exams_result as $k => $v){
                    
                    if($value['id'] == json_decode($exams_result[$k]['meta_value'])->job_id){
                        $jobs_result[$key]['exams'][] =  $exams_result[$k];
                    }

                }
            }
            
        }
        return ($result->num_rows() > 0) ? $jobs_result : false;
        
    }

    public function system_jobs_specific_pull($company,$job_id,$jobs){
  
        $jobs = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = '{$jobs}' AND id = {$job_id} LIMIT 1"; 
        
        $result = $this->db->query($jobs);
        if($result->num_rows() > 0){
            $jobs_result = $result->result_array();

            foreach($jobs_result as $key => $value){
                $jobs_result[$key]['exams'] = array();
                $exams = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'exams'"; 
                $exams_result = $this->db->query($exams)->result_array();

                $jobs_result[$key]['requirements'] = array();
                $requirements = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'requirements'"; 
                $requirements_result = $this->db->query($requirements)->result_array();
                foreach($requirements_result as $kr => $vr){
                    if($value['id'] == json_decode($requirements_result[$kr]['meta_value'])->job_id){
                        $jobs_result[$key]['requirements'][] =  $requirements_result[$kr];
                    }
                }

                foreach($exams_result as $k => $v){
                    if($value['id'] == json_decode($exams_result[$k]['meta_value'])->job_id){
                        $jobs_result[$key]['exams'][] =  $exams_result[$k];
                    }

                }
            }
            
        }
        return ($result->num_rows() > 0) ? $jobs_result : false;
        
    }
}

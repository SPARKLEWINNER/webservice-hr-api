<?php
require APPPATH . '/libraries/Base_Controller.php';
date_default_timezone_set('Asia/Manila');
defined('BASEPATH') or exit('No direct script access allowed');


class System extends Base_Controller{


    /* post */ 

    public function resend_email_post(){
        $ref_id = $this->post('id');
        $response = $this->Main_mdl->record_get_system($ref_id);
        $email_details = array(
            "from" => array(
                "email" => "system@".$response['company'].".com.ph"
            ),
            "personalizations" => [array(
                "to" => [array(
                    "email" => $response['username']
                )],
                "subject" => EMAIL_NEW_APPLICANT,
                "dynamic_template_data" => array(
                    "email"=> $response['username'],
                    "password" => $response['reference_id'],
                    "help" => EMAIL_ADMIN,
                    // "portal" =>"www.".$this->post('company').".com.ph" // to be change 
                    "portal" =>"http://portal.sparkles.com.ph/" // to be change 
                )
            )],
            "template_id" => EMAIL_SGTEMPLATE_NEW_ACC
        );

        $is_mailed = $this->send_email_sg($response['company'], EMAIL_NEW_APPLICANT, $email_details);
        if($is_mailed == NULL){
            $this->set_response(array("status" => 200, "data" => $is_mailed),  200); 
        }else{
            $this->email_logs('NEWAPPLICANT',$response['reference_id'], $response['username'], 0, "FAILED", $response['email_details'], $response['company']);
        }
    }

    public function create_job_post(){
        $data = $this->validate_inpt(array('company', 'title', 'id', 'description'), 'post');

        $app_data = array(
            "company" => $data['company'],
            "posted_by" => $data['id'],
            "meta_key" => "jobs",
            "meta_value" => json_encode($data),
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_jobs($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function create_exams_post(){
        $data = $this->validate_inpt(array('company', 'title', 'id', 'notice', 'job_id', 'link'), 'post');

        $app_data = array(
            "company" => $data['company'],
            "posted_by" => $data['id'],
            "meta_key" => "exams",
            "meta_value" => json_encode($data),
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_exams($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function create_requirements_post(){
        $data = $this->validate_inpt(array('company','job_id','id','requirements'), 'post');

        $app_data = array(
            "company" => $data['company'],
            "posted_by" => $data['id'],
            "meta_key" => "requirements",
            "meta_value" => json_encode($data),
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_requirements($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function update_requirements_post(){
        $data = $this->validate_inpt(array('company','job_id','id','req_id','requirements'), 'post');
        $req_id = $data['req_id'];
        $app_data = array(
            "company" => $data['company'],
            "posted_by" => $data['id'],
            "meta_key" => "requirements",
            "meta_value" => json_encode($data)
        );

        $response = $this->Main_mdl->system_update_requirements($app_data, $req_id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }
    
    public function create_report_post(){
        $data = $this->validate_inpt(array('emp_id','name','company','details'), 'post');

        $app_data = array(
            "emp_id" => $data['emp_id'],
            "company" => $data['company'],
            "name" => $data['name'],
            "details" => $data['details'],
            "status" => 0,
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_report($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }
    
    public function create_store_post(){
        $data = $this->validate_inpt(array('name','company','created_by'), 'post');

        $app_data = array(
            "name" => $data['name'],
            "details" =>  json_encode($this->post()),
            "company" => $data['company'],
            "created_by" => $data['created_by'],
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_store($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function assign_people_post(){
        $data = $this->validate_inpt(array('id','user_id','store_id','company'), 'post');

        $app_data = array(
            "emp_id" =>  $data['user_id'],
            "store_id" => $data['store_id'],
            "company" => $data['company'],
            "date_assigned" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_people_assign($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function create_people_post(){
        $data = $this->validate_inpt(array('company', 'fname', 'id', 'lname','email','access'), 'post');
        $generate_password = $this->createPassword();
        $app_data = array(
            "company" => $data['company'],
            "first_name" => $data['fname'],
            "last_name" => $data['lname'],
            "email" => $data['email'],
            "password" => $generate_password['hashed_password'],
            "temp_password" => $generate_password['temp_password'],
            "user_level" => $data['access'],
            "date_created" => date('Y-m-d H:i:s')
        );

        $response = $this->Main_mdl->system_record_people($app_data, $generate_password['temp_password']);
        $email_details = array(
            "from" => array(
                "email" => "system@".$response['company'].".com.ph"
            ),
            "personalizations" => [array(
                "to" => [array(
                    "email" => $response['email']
                )],
                "subject" => EMAIL_NEW_APPLICANT,
                "dynamic_template_data" => array(
                    "email"=> $response['email'],
                    "password" => $response['temp_password'],
                    "help" => EMAIL_ADMIN,
                    // "portal" =>"www.".$this->post('company').".com.ph" // to be change 
                    "portal" =>"http://portal.".$response['company'].".com.ph/" // to be change 
                )
            )],
            "template_id" => EMAIL_SGTEMPLATE_NEW_EMPLOYEE
        );
        $is_mailed = $this->send_email_sg($response['company'], EMAIL_NEW_APPLICANT, $email_details);
        if($is_mailed == NULL){
            $this->email_logs('NEWEMPLOYEE',$data['id'], $data['email'], 0, "SUCCESS", json_encode($email_details), $response['company']);
            $this->set_response(array("status" => 200, "data" => $response),  200); 
        }else{
            $this->email_logs('NEWEMPLOYEE',$data['id'], $data['email'], 0, "FAILED", json_encode($email_details), $response['company']);
        }
    }

    public function update_people_password_post(){
        $data = $this->validate_inpt(array('company','id','email','password', 'new_password'), 'post');
        $generate_password = $this->createPassword();
        $app_data = array(
            "company" => $data['company'],
            "id" => $data['id'],
            "email" => $data['email'],
            "password" => $data['new_password'],
        );

        $response = $this->Main_mdl->system_record_people_password($app_data, $data['password']);
        if($response == NULL){
            $this->set_response(array("status" => 200, "data" => $response),  200); 
            return $this->set_response($response, 422);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
            $this->email_logs('NEWEMPLOYEE',$data['id'], $data['email'], 0, "FAILED", json_encode($email_details), $response['company']);
        }
    }

    public function reset_password_post(){
        $email = $this->post('email');

        if(empty($email) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $generate_password = $this->createPassword();
        $app_data = array(
            "email" => $email,
            "password" => $generate_password['hashed_password'],
            "temp_password" => $generate_password['temp_password'],
        );

        $response = $this->Main_mdl->system_record_reset_people($app_data, $generate_password['temp_password']);
        $email_details = array(
            "from" => array(
                "email" => "system@".$response['company'].".com.ph"
            ),
            "personalizations" => [array(
                "to" => [array(
                    "email" => $response['email']
                )],
                "subject" => EMAIL_NEW_APPLICANT,
                "dynamic_template_data" => array(
                    "email"=> $response['email'],
                    "password" => $response['temp_password'],
                    "help" => EMAIL_ADMIN,
                    // "portal" =>"www.".$this->post('company').".com.ph" // to be change 
                    "portal" =>"http://portal.".$response['company'].".com.ph/" // to be change 
                )
            )],
            "template_id" => EMAIL_SGTEMPLATE_NEW_EMPLOYEE
        );
        $is_mailed = $this->send_email_sg($response['company'], EMAIL_NEW_APPLICANT, $email_details);
        if($is_mailed == NULL){
            $this->email_logs('RESETPASSWORD',$response['id'], $email, 0, "SUCCESS", json_encode($email_details), $response['company']);
            $this->set_response(array("status" => 200, "data" => $response),  200); 
        }else{
            $this->email_logs('RESETPASSWORD',$response['id'], $email, 0, "FAILED", json_encode($email_details), $response['company']);
        }
    }

    public function create_dtr_post(){
        $data = $this->validate_inpt(array('id','company','store_id', 'emp_id', 'dtr'), 'post');

        $app_data = array(
            "company" => $data['company'],
            "store_id" => $data['store_id'],
            "emp_id" => $data['emp_id'],
            "author" => $data['id'],
            "dtr" =>  json_encode($this->post()),
            "date_created" => date('Y-m-d H:i:s'),
            "status" => 0
        );

        $response = $this->Main_mdl->system_record_dtr($app_data);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    /* get */ 

    public function peoples_get($company = NULL){
        if(empty($company) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_people_pull($company);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function people_specific_get($company = NULL, $id = NULL){
        if(empty($company) && empty($id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_people_specific_pull($company,$id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function jobs_records_get($company = NULL , $id = NULL){
        if(empty($company) && empty($id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_jobs_pull($company,$id,"jobs");
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function job_specific_records_get($company = NULL , $job_id = NULL){
        if(empty($company) && empty($job_id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_jobs_specific_pull($company,$job_id,"jobs");
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function payroll_record_get($company = NULL , $store_id = NULL){
        if(empty($company) && empty($store_id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }

        $response = $this->Main_mdl->system_record_payroll($company,$store_id);
        if($response){

            return $this->set_response(array("status" => 200, "data" => $this->computePayroll($response)),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    /* patch */ 

    public function update_exams_patch(){
        $data = $this->validate_inpt(array('company', 'title', 'exam_id', 'notice', 'job_id', 'link'), 'patch');
        $exam_id = $data['exam_id'];
        $app_data = array(
            "company" => $data['company'],
            "meta_key" => "exams",
            "meta_value" => json_encode($data)
        );

        $response = $this->Main_mdl->system_record_update_exams($app_data, $exam_id);

        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function update_email_patch(){
        $data = $this->validate_inpt(array('id', 'email'), 'patch');
        $email_id = $data['id'];
        $app_data = array(
            "email" => $data['email']
        );

        $response = $this->Main_mdl->system_record_update_email($app_data, $email_id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    /* delete */ 

    public function remove_exams_delete($exam_id = NULL){
        if(empty($exam_id) ){
            $this->response_return($this->response_code (400,""));
            return false;
        }
        
        $response = $this->Main_mdl->system_record_remove_exams($exam_id);
        if($response){
            return $this->set_response(array("status" => 200, "message" => "Success removed Examination"),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    /* computations */

    public function computePayroll($response){

            
        $rate_per_hr = $ot = $nsd = $ts = $ml = 0;
        $basic_pay = $otamt = $lateamt = $nsdamt = $tsamt = $mlamt = $gross_pay = $gross_pay_less = 0;
        $total_hrs = 0;
        $days = 0;
        $sss = $phic = $hdmf = 0;
        $rpd = $total_salary = $total_contribution=  0;
        $dtrArr = array();
        foreach($response as $ke => $employee){
            $wages = $this->Main_mdl->system_record_wages_combine($employee['store_id']);
            $dtrArr['employee'] = $employee;
            if($wages){

                // get the store's wage computation
                foreach($wages as $kw => $wage){
                    $declared_calc = json_decode($wage['data'], true);
                    $rpd = $declared_calc['rate-per-day'];  //rate per hr %
                    $rate_per_hr = $declared_calc['rate-per-hr'];  //rate per hr %
                    $ot = $declared_calc['overtime'];   // overtime %
                    $late = $declared_calc['late-per-hr']; // late %
                    $nsd = $declared_calc['nightshift-diff'];   // nsd %
                    $ts = $declared_calc['tsallowance_amt'];   // ts allowance %
                    $ml = $declared_calc['meal_amt'];   // meal allowance %
                    $sss = $declared_calc['sss_amt']; // sss deduction
                    $phic = $declared_calc['phl_amt']; // phl deduction
                    $hdmf = $declared_calc['hmdf_amt']; // hdmf deduction
                }   
            }

            if($employee['dtr']){
                foreach($employee['dtr'] as $dtr_ext){
                    $dtr = json_decode(json_decode($dtr_ext['dtr'])->dtr);
                    if($dtr){
                        if(!empty($dtr->ot_t)){
                            $otamt = $dtr->ot_t;
                        }

                        if(!empty($dtr->nsd_t)){
                            $nsdamt = $dtr->nsd_t;
                        }

                        if(!empty($dtr->late_t)){
                            $lateamt = $dtr->late_t;
                        }

                        $total = array_sum($dtr->no_hours) + $otamt;
                        $days = $this->floatNumber($total / 8);
                    }

                    // iterate response 
                    $dtrArr['employee']['basic_pay'] = $this->floatNumber($basic_pay = $rate_per_hr * $total); // basic pay 
                    $dtrArr['employee']['omtamt'] = $omtamt = $ot * $otamt; // overtime 
                    $dtrArr['employee']['lateamt'] = $lateamt = $lateamt * $late; // late
                    $dtrArr['employee']['nsdamt'] = $nsdamt = $nsdamt * $nsd;  // nsd
                    $dtrArr['employee']['tsamt'] = $tsamt = $days * $ts;  // ts allowance
                    $dtrArr['employee']['mlamt'] = $this->floatNumber($mlamt = $days * $ml); // meal allowance
                    $dtrArr['employee']['gross_pay'] = $gross_pay = $basic_pay + $otamt + $nsdamt + $tsamt + $mlamt - $lateamt; // gross pay
                    $dtrArr['employee']['gross_pay_less'] = $gross_pay_less = $gross_pay - $tsamt - $mlamt;    // gross pay without ts & meal
                    $dtrArr['employee']['sil']  = $sil =  $this->floatNumber($rpd * 5 / 12 / 2);
                    $dtrArr['employee']['13month']  = $month13 = $this->floatNumber(($basic_pay - $lateamt) / 12);
                    $dtrArr['employee']['total_salary']  = $total_salary = $this->floatNumber(($basic_pay + $omtamt + $nsdamt + $sil + $month13) - $lateamt);
                    $dtrArr['employee']['sss']  = $sss;
                    $dtrArr['employee']['phic']  = $phic;
                    $dtrArr['employee']['hdmf']  = $hdmf;
                    $dtrArr['employee']['total_contribution']  = $total_contribution = $this->floatNumber($sss + $phic + $hdmf);
                    $dtrArr['employee']['gross_billing']  = $this->floatNumber($total_salary + $total_contribution);
                }

                return $dtrArr;
            }


        }



        
        
        
        
       
       
        
        
     
    }

    public function floatNumber($number){
        return number_format((float) $number, 2, '.', '');
    }

    public function floatVal($percentage){
        return floatval($percentage);
    }

}
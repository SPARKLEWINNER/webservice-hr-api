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


    public function applicant_create_post()
    {
        $data = $this->validate_inpt(array('data','email'), 'post');
        $mg_email = $this->post('person_email');
        $generated = $this->generateReferenceCode($mg_email);

        if ($this->Main_mdl->record_validate_data($mg_email)) {
            $response = $this->response_code(422, array("status" => 422, "message" => "Email already exists."), "");
            return $this->set_response($response, 422);
        }

        $upload_proc = $this->upload_profile($_FILES['pref_image'], $generated);
        //$upload_proc = "sample";
        $app_data = array(
            'username' =>  $this->post('person_email'),
            'data' => json_encode($this->post()),
            'company' => $this->post('company'),
            'reference_id' => $generated,
            'profile' =>  $upload_proc['link'],
            'date_created' => date('Y-m-d H:i:s')
        );
        


        if ($upload_proc) {
            $response = $this->Main_mdl->record_data($app_data);
            if (!isset($response['status'])) {
                $this->appl_logs($app_data['username'], "APPLICANT", "FAILED", json_encode($app_data), 0, $this->post('company'));
                return $this->response_code(422, array("status" => 422, "message" => "Failed to Submit Form application"), "");
            } else {
                $this->appl_logs($app_data['username'], "APPLICANT", "SUCCESS", json_encode($app_data), 1, $this->post('company'));
                $email_details = array(
                    "from" => array(
                        "email" => ucfirst($this->post('company')) . " - Recruitment Account <no-reply@" . $this->post('company') . ".com.ph>",
                    ),
                    "personalizations" => [array(
                        "to" => [array(
                            "email" => $response['username']
                        )],
                        "subject" => EMAIL_NEW_APPLICANT,
                        "dynamic_template_data" => array(
                            "email" => $response['username'],
                            "password" => $response['reference_id'],
                            "help" => EMAIL_ADMIN,
                            "portal" => $response['return_url'] 
                        )
                    )],
                    "template_id" => EMAIL_SGTEMPLATE_NEW_ACC
                );


                $is_mailed = $this->send_email_sg($this->post('company'), EMAIL_NEW_APPLICANT, $email_details);
                if ($is_mailed == NULL) {
                    $this->email_logs('NEWAPPLICANT', $response['reference_id'], $response['username'], 0, "SUCCESS", json_encode($email_details), $this->post('company'));
                    $this->set_response(array("status" => 200, "data" => $response),  200);
                } else {
                    $this->email_logs('NEWAPPLICANT', $response['reference_id'], $response['username'], 0, "FAILED", json_encode($email_details), $this->post('company'));
                }
            }
        } else {
            $this->appl_logs($app_data['username'], "APPLICANT", "FAILED", json_encode($app_data), 0, $app_data['company']);
            return $this->response_code(422, array("status" => 422, "message" => "Server upload error."), "");
        }
    }

    public function applicant_create_v2_post()
    {
        $data = $this->validate_inpt(array('data','email'), 'post');
        $mg_email = $this->post('person_email');
        $generated = $this->generateReferenceCode($mg_email);

        if ($this->Main_mdl->record_validate_data($mg_email)) {
            $response = $this->response_code(422, array("status" => 422, "message" => "Email already exists."), "");
            return $this->set_response($response, 422);
        }

        $app_data = array(
            'username' =>  $this->post('person_email'),
            'data' => json_encode($this->post()),
            'company' => $this->post('company'),
            'reference_id' => $generated,
            'profile' =>  AWS_PROFILE_URI."".$this->post('pref_image'),
            'date_created' => date('Y-m-d H:i:s')
        );
        

        $response = $this->Main_mdl->record_data($app_data);
        if (!isset($response['status'])) {
            $this->appl_logs($app_data['username'], "APPLICANT", "FAILED", json_encode($app_data), 0, $this->post('company'));
            return $this->response_code(422, array("status" => 422, "message" => "Failed to Submit Form application"), "");
        } else {
            $this->appl_logs($app_data['username'], "APPLICANT", "SUCCESS", json_encode($app_data), 1, $this->post('company'));
            $email_details = array(
                "from" => array(
                    "email" => ucfirst($this->post('company')) . " - Recruitment Account <no-reply@" . $this->post('company') . ".com.ph>",
                ),
                "personalizations" => [array(
                    "to" => [array(
                        "email" => $response['username']
                    )],
                    "subject" => EMAIL_NEW_APPLICANT,
                    "dynamic_template_data" => array(
                        "email" => $response['username'],
                        "password" => $response['reference_id'],
                        "help" => EMAIL_ADMIN,
                        "portal" => $response['return_url'] 
                    )
                )],
                "template_id" => EMAIL_SGTEMPLATE_NEW_ACC
            );


            $is_mailed = $this->send_email_sg($this->post('company'), EMAIL_NEW_APPLICANT, $email_details);
            if ($is_mailed == NULL) {
                $this->email_logs('NEWAPPLICANT', $response['reference_id'], $response['username'], 0, "SUCCESS", json_encode($email_details), $this->post('company'));
                $this->set_response(array("status" => 200, "data" => $response),  200);
            } else {
                $this->email_logs('NEWAPPLICANT', $response['reference_id'], $response['username'], 0, "FAILED", json_encode($email_details), $this->post('company'));
            }
        }

    }


    public function applicant_document_create_post()
    {

        $data = $this->validate_inpt(array('company', 'id', 'name', 'type'), 'post');
        $status = $archive = 0;
        if(!empty($this->post('docid'))){
            $status = 1;
        }

        $this->activity_logs($data['id'], 'DOCUMENTLOG', $data['type'], $data['name'], "DOCUMENTLOG", filter_var($data['name'], FILTER_VALIDATE_BOOLEAN));
        $app_data = array(
            "applicant_id" => $data['id'],
            "name" => $data['name'],
            "company" => $data['company'],
            "doctype" => $data['type'],
            "created" => date('Y-m-d H:i:s'),
            "status" => 0,
            "archive" => 0,
            "url" => AWS_S3_URL . $data['name']
        );

        if ($data['name']) {

            $isExisting = $this->Main_mdl->records_doc_pull($data['id'], $data['type']);
            if ($isExisting) {
                if(!empty($this->post('docid'))){
                    $result = $this->Main_mdl->record_document_data_patch($app_data, $this->post('docid'), $status);
                    if ($result) {
                        return $this->set_response(array("status" => 200, "data" => $result),  200);
                    } else {
                        $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
                        return $this->set_response($response, 422);
                    }
                }else{
                    $result = $this->Main_mdl->record_document_data($app_data);
                    if ($result) {
                        return $this->set_response(array("status" => 200, "data" => $result),  200);
                    } else {
                        $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
                        return $this->set_response($response, 422);
                    }
                    /*return $this->set_response(array("status" => 200, "data" => $isExisting),  200);*/
                }
            } else {
                $result = $this->Main_mdl->record_document_data($app_data);
                if ($result) {
                    return $this->set_response(array("status" => 200, "data" => $result),  200);
                } else {
                    $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
                    return $this->set_response($response, 422);
                }
            }
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
            return $this->set_response($response, 422);
        }
    }

    public function applicant_document_archive_post()
    {

        $data = $this->validate_inpt(array('id', 'applicant_id', 'type'), 'post');
        if(empty($data['applicant_id']) || empty($data['id'])){
            $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
            return $this->set_response($response, 422);
        }

        $app_data = array(
            "status" => $data['type'],
            "archive" => $data['type']
        );

        $result = $this->Main_mdl->record_document_archive_patch($app_data, $data['id'], $data['applicant_id']);
        if ($result) {
            $this->set_response(array("status" => 200, "data" => $result),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" =>  "Server upload error"));
            return $this->set_response($response, 422);
        }

    }

    public function applicant_exam_create_post()
    {
        $data = $this->validate_inpt(array('id', 'job', 'exam'), 'post');
        $app_data = array(
            "applicant_id" => $data["id"],
            "date_created" => date('Y-m-d H:i:s'),
            "job_id" => $data["job"],
            "exam_id" => $data["exam"],
        );

        $response = $this->Main_mdl->record_exam_data($app_data);
        $this->Main_mdl->record_applying_for($data['job'], $data['id']);
        if (!isset($response['status'])) {
            $this->activity_logs($data["id"],  "EXAMTAKE", "FAILED", json_encode($data), json_encode($app_data), 1);
            return $this->set_response($response, 422);
        } else {
            $this->activity_logs($data["id"],  "EXAMTAKE", "SUCCESS", json_encode($data), json_encode($app_data), 0);
            $this->set_response(array("status" => 200, "data" => $response),  200);
        }
    }





    public function review_bypass_record_patch()
    {
        $data = $this->validate_inpt(array('id', 'status'), 'patch');
        $response = $this->Main_mdl->record_patch_data($data, $data['status']);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }




    public function applicants_specific_get($company = NULL, $id = NULL)
    {

        if (empty($company) && empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_specific_pull($company, $id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_specific_reviews_get($company = NULL, $id = NULL)
    {

        if (empty($company) && empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_reviews_pull($company, $id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function applicants_specific_reviews__documents_get($company = NULL, $id = NULL)
    {

        if (empty($company) && empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_specific_document_pull($company, $id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    // Documents 

    public function applicants_specific_reviews_documents_get($company = NULL, $status = 0, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_documents_pull($company, $status, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function applicant_documents_get($id = "")
    {
        if (empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->documents_pull($id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }


    public function applicant_document_lists_get($id = "", $status = "")
    {
        if (empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->documents_pull_list($id, $status);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_specific_documents_get($id = 0)
    {
        if (empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_specifics_reviews_pull($id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }




    // Supervisor Requests

    public function applicants_ts_specific_get($company = NULL, $id = NULL)
    {

        if (empty($company) && empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_ts_specific_pull($company, $id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_ts_specific_reviews_get($company = NULL, $ref_id = NULL)
    {

        if (empty($company) && empty($ref_id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_ts_reviews_pull($company, $ref_id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function update_applicant_store_get($company = NULL, $id = NULL, $store = NULL)
    {
        if (empty($company) && empty($id) && empty($store)) {
            $this->response_return($this->response_code(400, "Invalid parameters"));
            return false;
        }

        $response = $this->Main_mdl->get_store_assessment_record($company, $id, $store);
        if ($response) {
            $data = $response[0]['store_assess'];
            $currentStore = $response[0]['store'];
            $updatedStore = str_replace($currentStore, $store, $data);
            $updateResponse = $this->Main_mdl->update_store_assessment_record($company, $id, $store, $updatedStore);
            if ($updateResponse) {
                return $this->set_response(array("status" => 200, "message" => "success"),  200);
            } else {
                return $this->set_response(array("status" => 422, "message" => "Company or applicant not found"),  200);
            }
           
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Company or applicant not found"));
            return $this->set_response($response, 422);
        }
    }

    public function applicant_store_get($company = NULL, $id = NULL)
    {
        if (empty($company) && empty($id)) {
            $this->response_return($this->response_code(400, "Invalid parameters"));
            return false;
        }

        $response = $this->Main_mdl->get_store_assessment_record($company, $id);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            return $this->set_response(array("status" => 422, "message" => "Company or applicant not found"),  200);
        }
    }   

    public function sanctions_post()
    { 
        $data = array(
            'applicant_id' =>  $this->post('aplid'),
            'sanction' => $this->post('sanction'),
            'section' => $this->post('section'),
            'remarks' => $this->post('remarks'),
            'file' => AWS_S3_URL . $this->post('name'),
            'hr' => $this->post('hr'),
            'date' => date('Y-m-d')
        );
        $hrData = array(
            'hrName' => $this->post('hrName'),
            'hrEmail' => $this->post('hrEmail')
        );
        $response = $this->Main_mdl->sanctions_create($data, $hrData);
        if($response){
            return $this->set_response(array("status" => 200, "message" => "success"),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function sanctions_list_get($id)
    { 
        $response = $this->Main_mdl->sanctions_get($id);
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function personnel_logs_list_get()
    { 
        $response = $this->Main_mdl->personnel_logs_get();
        
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function update_applicant_details_post($id = NULL, $data = NULL)
    {
        if (empty($id) && empty($data)) {
            $this->response_return($this->response_code(400, "Invalid parameters"));
            return false;
        }
        $firstName = $this->post('firstName');
        $oldFirstName = $this->post('oldFirstName');
        $oldMiddleName = $this->post('oldMiddleName');
        $middleName = $this->post('middleName');
        $oldLastName = $this->post('oldLastName');
        $lastName = $this->post('lastName');
        $response = $this->Main_mdl->get_user_record($id);
        $updatedRecord = str_replace($oldFirstName, $firstName, $response[0]['data']);
        $updatedRecord2 = str_replace($oldMiddleName, $middleName, $updatedRecord); 
        $updatedRecord3 = str_replace($oldLastName, $lastName, $updatedRecord2);
          
        if ($response) {
            $updateResponse = $this->Main_mdl->update_user_record($id, $updatedRecord3);
            if ($updateResponse) {
                return $this->set_response(array("status" => 200, "message" => "success"),  200);
            } else {
                return $this->set_response(array("status" => 422, "message" => "Company or applicant not found"),  200);
            }
           
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Company or applicant not found"));
            return $this->set_response($response, 422);
        }
    }

    public function update_applicant_phone_post($id = NULL, $data = NULL)
    {
        if (empty($id) && empty($data)) {
            $this->response_return($this->response_code(400, "Invalid parameters"));
            return false;
        }
        $phone = $this->post('phone');
        $oldPhone = $this->post('oldPhone');
        $response = $this->Main_mdl->get_user_record($id);
        $updatedRecord = str_replace($oldPhone, $phone, $response[0]['data']);
          
        if ($response) {
            $updateResponse = $this->Main_mdl->update_user_record($id, $updatedRecord);
            if ($updateResponse) {
                return $this->set_response(array("status" => 200, "message" => "success"),  200);
            } else {
                return $this->set_response(array("status" => 422, "message" => "Company or applicant not found"),  200);
            }
           
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Company or applicant not found"));
            return $this->set_response($response, 422);
        }
    }

    public function update_applicant_gov_post($id = NULL, $data = NULL)
    {
        if (empty($id) && empty($data)) {
            $this->response_return($this->response_code(400, "Invalid parameters"));
            return false;
        }
        $tin = $this->post('tin');
        $oldTin = $this->post('oldTin');
        $phil = $this->post('phil');
        $oldPhil = $this->post('oldPhil');
        $sss = $this->post('sss');
        $oldSss = $this->post('oldSss');
        $pagibig = $this->post('pagibig');
        $oldPagibig = $this->post('oldPagibig');
        $response = $this->Main_mdl->get_user_record($id);
        $updatedRecord = str_replace($oldTin, $tin, $response[0]['data']);
        $updatedRecord2 = str_replace($oldPhil, $phil, $updatedRecord); 
        $updatedRecord3 = str_replace($oldSss, $sss, $updatedRecord2);
        $updatedRecord4 = str_replace($oldPagibig, $pagibig, $updatedRecord3);
          
        if ($response) {
            $updateResponse = $this->Main_mdl->update_user_record($id, $updatedRecord4);
            if ($updateResponse) {
                return $this->set_response(array("status" => 200, "message" => "success"),  200);
            } else {
                return $this->set_response(array("status" => 422, "message" => "Company or applicant not found"),  200);
            }
           
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Company or applicant not found"));
            return $this->set_response($response, 422);
        }
    }

    public function personnel_get($id = NULL)
    { 
        $response = $this->Main_mdl->personnel_get($id);
        
        if($response){
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        }else{
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }

    }

    public function applicants_documents_specific_get($company = NULL, $status = NULL, $user = NULL, $name = NULL)
    {

        if (empty($company) && empty($id)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_documents_specific_pull($company, $status, $user, $name);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "Unable to process your request"));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_specific_search_documents_post($company = NULL, $status = 0, $user = 0)
    {
        $name = $this->post('name');
        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_documents_specific_pull($company, $status, $user, $name);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function applicants_count_get($company = NULL, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_applicants_count_pull($company, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function personnel_count_get($company = NULL, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_personnel_count_pull($company, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function complete_count_get($company = NULL, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_completed_count_pull($company, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function pending_count_get($company = NULL, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_pending_count_pull($company, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function examination_count_get($company = NULL, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_examination_count_pull($company, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function store_review_count_get($company = NULL, $user = 0)
    {

        if (empty($company)) {
            $this->response_return($this->response_code(400, ""));
            return false;
        }

        $response = $this->Main_mdl->record_store_review_count_pull($company, $user);
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }

    public function record_for_extraction_get()
    {
        $response = $this->Main_mdl->record_for_extraction_pull();
        if ($response) {
            return $this->set_response(array("status" => 200, "data" => $response),  200);
        } else {
            $response = $this->response_code(422, array("status" => 422, "message" => "No data found."));
            return $this->set_response($response, 422);
        }
    }
    

}

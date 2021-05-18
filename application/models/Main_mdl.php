<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');
require APPPATH . '/libraries/Base_Model.php';

class Main_mdl extends Base_Model
{
    public $videoStorage = "../wp-content/uploads/recorded_mobile/";

    public function __construct()
    {
        parent::__construct();
    }

    public function login($email, $password)
    {

        $acc = $this->db->select('*')->from('users')->where('email', $email)->get()->row();
        if (!isset($acc)) return $this->response_code(204, "User invalid", "");
        if (!password_verify($password,  $acc->password))  return $this->response_code(204, "User invalid", "");
        $this->db->where('id', $acc->id);
        $this->db->update('users', array("last_login" => date('Y-m-d H:i:s')));

        if ($acc->user_level == 3) {
            return array(
                "id" => $acc->id,
                "email" => $acc->email,
                "firstname" => $acc->first_name,
                "lastname" => $acc->last_name,
                "company" => $acc->company,
                "profile" => $acc->profile,
                "user_level" => $acc->user_level,
                "switchable" => $acc->switchable
            );
        } else {

            if ($acc->user_level == 5) {
                $asg = $this->db->select('*')->from('assigning')->where('emp_id', $acc->id)->get()->row();
                $store = $this->db->select('*')->from('store')->where('id', $asg->store_id)->get()->row();
                return array(
                    "id" => $acc->id,
                    "email" => $acc->email,
                    "firstname" => $acc->first_name,
                    "lastname" => $acc->last_name,
                    "company" => $acc->company,
                    "profile" => $acc->profile,
                    "user_level" => $acc->user_level,
                    "store_id" => $store->id,
                    "store_name" => $store->name
                );
            } else {
                return array(
                    "id" => $acc->id,
                    "email" => $acc->email,
                    "firstname" => $acc->first_name,
                    "lastname" => $acc->last_name,
                    "company" => $acc->company,
                    "profile" => $acc->profile,
                    "user_level" => $acc->user_level,
                );
            }
        }
    }

    public function member_login($email, $password)
    {
        $statement = array('username' => $email);
        $acc = $this->db->select('*')->from('applications')->where($statement)->get()->row();
        if (empty($acc->password)) {
            $statement = array('username' => $email, 'reference_id' => $password);
            $acc = $this->db->select('*')->from('applications')->where($statement)->get()->row();
        } else {
            if (!password_verify($password, $acc->password)) return false;
            $statement = array('username' => $email);
            $acc = $this->db->select('*')->from('applications')->where($statement)->get()->row();
        }

        if ($acc) {
            return array(
                "id" => $acc->id,
                "applicant_id" => $acc->applicant_id,
                "reference_id" => $acc->reference_id,
                "date_created" => $acc->date_created,
                "data" => $acc->data,
                "account_status" => $acc->status,
                "reviewer" => $acc->reviewer,
                "notification" => $acc->notification,
                "username" => $acc->username,
                "password" => $acc->password,
                "user_level" => 10,
                "profile" => $acc->profile,
                "applying_for" => $acc->applying_for,
                "company" => $acc->company
            );
        } else {
            return false;
        }
    }

    public function workplace_login($email, $password)
    {
        $acc = $this->db->select('*')->from('users')->where('email', $email)->get()->row();

        if (!isset($acc) && !password_verify($password,  $acc->password)) return false;

        $this->db->where('id', $acc->id);
        $this->db->update('users', array("last_login" => date('Y-m-d H:i:s')));
        $asg = $this->db->select('*')->from('assigning')->where('emp_id', $acc->id)->get()->row();
        $store = $this->db->select('*')->from('store')->where('id', $asg->store_id)->get()->row();
        return array(
            "id" => $acc->id,
            "email" => $acc->email,
            "firstname" => $acc->first_name,
            "lastname" => $acc->last_name,
            "company" => $acc->company,
            "profile" => $acc->profile,
            "user_level" => $acc->user_level,
            "store_id" => $store->id,
            "store_name" => $store->name
        );
    }

    public function recordToken($id, $token)
    {
        $this->db->where('id', $id);
        $this->db->update('keys', array("key" => $token));
    }

    public function retrieveUser($email, $password)
    {
        $acc = $this->db->select('id,email,first_name,last_name,profile,company,switchable')->from('users')->where('email', $email)->get()->row();

        if (!$acc) { // not employee
            $acc = $this->db->select('id,username,company')->from('applications')->where('username', $email)->get()->row();

            if (!$acc) return $this->response_code(204, "User invalid", "");
            $update = array("password" => $password, "token" => $password);
            $this->db->where('id', $acc->id);
            $this->db->update('applications', $update);
            return array(
                "id" => $acc->id,
                "company" => $acc->company,
                "switchable" => 0,
                "return_url" => MEMBER_URL
            );
        } else {
            $update = array("password" => $password, "token" => $password);
            $this->db->where('id', $acc->id);
            $this->db->update('users', $update);
            $return_url = STAFF_URL;

            if (intval($acc->user_level) === 5) {
                $return_url = WORKPLACE_URL;
            }

            return array(
                "id" => $acc->id,
                "company" => $acc->company,
                "switchable" => $acc->switchable,
                "return_url" => $return_url
            );
        }
    }

    public function verifyUser($data)
    {
        $acc = $this->db->select('*')->from('users')->where('id', $data['id'])->get()->row();
        if ($acc->user_level == 5) {
            $asg = $this->db->select('*')->from('assigning')->where('emp_id', $acc->id)->get()->row();
            $store = $this->db->select('*')->from('store')->where('id', $asg->store_id)->get()->row();
            return array(
                "id" => $acc->id,
                "email" => $acc->email,
                "firstname" => $acc->first_name,
                "lastname" => $acc->last_name,
                "company" => $acc->company,
                "profile" => $acc->profile,
                "user_level" => $acc->user_level,
                "store_id" => $store->id,
                "store_name" => $store->name
            );
        } else {
            return array(
                "id" => $acc->id,
                "email" => $acc->email,
                "firstname" => $acc->first_name,
                "lastname" => $acc->last_name,
                "company" => $acc->company,
                "profile" => $acc->profile,
                "user_level" => $acc->user_level,
            );
        }
    }


    public function resetUser($data)
    {
        $acc = $this->db->select('id,email,first_name,last_name,profile,token')->from('users')->where('email', $data['email'])->get()->row();
        if (!$acc) {
            $applicant = $this->db->select('id,username,company,password,token')->from('applications')->where('username', $data['email'])->get()->row();

            if ($applicant) {

                if ($applicant->token === "" || empty($applicant->token)) return $this->response_code(204, "Invalid token", "");
                if ($data['hash'] ==  $applicant->token) :

                    $update = array(
                        "password" => password_hash($data['password'], PASSWORD_DEFAULT),
                        "token" => ""
                    );
                    $this->db->where('id', $applicant->id);
                    $this->db->update('applications', $update);

                    return array(
                        "id" => $applicant->id,
                        "email" => $applicant->username,
                        "company" => $applicant->company
                    );

                else :
                    return false;
                endif;
            } else {
                return $this->response_code(204, "User Invalid", "");
            }
        } else {

            if ($acc->token === "" || empty($acc->token)) return $this->response_code(204, "Invalid token", "");

            if ($data['hash'] ==  $acc->token) :

                $update = array(
                    "password" => password_hash($data['password'], PASSWORD_DEFAULT),
                    "token" => ""
                );
                $this->db->where('id', $acc->id);
                $this->db->update('users', $update);

                return array(
                    "id" => $acc->id,
                    "email" => $acc->email,
                    "firstname" => $acc->first_name,
                    "lastname" => $acc->last_name,
                    "profile" => $acc->profile
                );

            else :
                return false;
            endif;
        }
    }

    /** Exams **/

    public function exam_data($data)
    {

        $this->db->insert('exams', $data);
        $inserted_id = $this->db->insert_id();

        $exam = $this->db->select('*')->from('exams')->where('id', $inserted_id)->get()->row();

        if ($this->db->affected_rows() > 0) :
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
        else : return false;
        endif;
    }

    /** Records **/

    public function record_data($data)
    {

        $this->db->insert('applications', $data);
        $inserted_id = $this->db->insert_id();

        $record = $this->db->select('*')->from('applications')->where('id', $inserted_id)->get()->row();

        if ($this->db->affected_rows() > 0) :
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
                "profile" => $record->profile,
                "return_url" => MEMBER_URL
            );
        else : return false;
        endif;
    }

    public function record_validate_data($email)
    {
        $query = "SELECT * FROM applications WHERE username = '{$email}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? true : false;
    }

    public function record_upload_doc($data)
    {
        $this->db->insert('records', $data);
        return $this->db->affected_rows() > 0 ? true : false;
    }

    public function record_upload_activity($data)
    {
        $this->db->insert('upload', $data);
        return $this->db->affected_rows() > 0 ? true : false;
    }


    public function records_doc_pull($id, $type)
    {
        $query = "SELECT * FROM documents WHERE applicant_id = {$id} AND doctype = '{$type}'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return false;
        }
    }

    public function record_review_data($id, $data)
    {

        $this->db->insert('reviews', $data);
        $inserted_id = $this->db->insert_id();

        $record = $this->db->select('*')->from('reviews')->where('id', $inserted_id)->get()->row();

        if ($this->db->affected_rows() > 0) :
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
        else : return false;
        endif;
    }

    public function record_review_doc_data($data)
    {

        $this->db->insert('reviews_doc', $data);
        $inserted_id = $this->db->insert_id();

        $record = $this->db->select('*')->from('reviews_doc')->where('id', $inserted_id)->get()->row();

        if ($this->db->affected_rows() > 0) :

            $status = array(0 => 5, 1 => 80, 2 => 4);
            $this->db->where('id', $data['appl_id']);
            $this->db->update('applications', array("status" => $status[$data['status']])); // 1 : FAILED , 0 : Complete , 2 : Incomplete

            if ($status[$data['status']] == 5) {
                $this->db->where('id', $data['appl_id']);
                $this->db->update('reviews_doc', array("status" => 0));
            }

            return array(
                "id" => $inserted_id,
                'applicant_id' => $record->appl_id,
                'applicant_company' => $record->appl_company,
                'author_id' => $record->author_id,
                'author_company' => $record->author_company,
                'notice' => $record->notice,
                'data' => $record->data,
                'status' => $record->status,
                'date_created' => $record->date_created,
            );
        else : return false;
        endif;
    }

    public function record_for_training($data)
    {
        $record = $this->db->select('*')->from('reviews_doc')->where('appl_id', $data['appl_id'])->get()->row();
        if (intval($data['status']) === 3) {

            $training_data = array(
                "appl_id" => $data['appl_id'],
                "company" => $data['appl_company'],
                "author" => $data['author_id'],
                "notice" => $data['notice'],
                "status" => $data['status'],
                "date_created" => $data['date_created']
            );

            $this->db->insert('training', $training_data);
            $inserted_id = $this->db->insert_id();

            $record = $this->db->select('*')->from('training')->where('id', $inserted_id)->get()->row();

            if ($this->db->affected_rows() > 0) :

                $this->db->where('id', $data['appl_id']);
                $this->db->update('applications', array("status" => 6));

                $this->db->where('id', $data['appl_id']);
                $this->db->update('reviews_doc', array("status" => 4));

                return array(
                    "id" => $inserted_id,
                    'applicant_id' => $record->appl_id,
                    'author' => $record->author,
                    'company' => $record->company,
                    'notice' => $record->notice,
                    'status' => $record->status,
                    'date_created' => $record->date_created,
                );
            else : return false;
            endif;
        } else {
            $status = array(1 => 80, 2 => 4);
            $record = $this->db->select('*')->from('reviews_doc')->where('appl_id', $data['appl_id'])->get()->row();

            if ($this->db->affected_rows() > 0) :

                $this->db->where('id', $data['appl_id']);
                $this->db->update('applications', array("status" => $status[$data['status']]));

                $this->db->where('id', $data['appl_id']);
                $this->db->update('reviews_doc', array("status" => 4));

                $applicant = $this->db->select('*')->from('applications')->where('id', $data['appl_id'])->get()->row();

                return array(
                    'applicant_id' => $applicant->id,
                    'status' => $applicant->status,
                    'applicant_company' => $record->appl_company,
                    'author_id' => $record->author_id,
                    'author_company' => $record->author_company,
                    'notice' => $record->notice,
                    'data' => $record->data,
                    'doc_status' => $record->status,
                    'date_created' => $record->date_created,
                );
            else : return false;
            endif;
        }
    }


    public function record_applying_for($job_id, $applicant_id)
    {

        $result = $this->db->select('*')->from('applications')->where('id', $applicant_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            if ($result->applying_for == 0) {
                $this->db->where('id', $applicant_id);
                $this->db->update('applications', array("applying_for" => $job_id));
            }
        endif;
    }

    public function record_review_store_data($data)
    {
        $app_id = $data['id'];
        $app_data = array(
            "store_assess" => $data['store_assess'],
            "reviewer" => $data['reviewer'],
            "review_status" => $data['review_status'],
            "store_review_date" => date('Y-m-d H:i:s')
        );

        $this->db->where('applicant_id', $app_id);
        $this->db->update('reviews', $app_data);

        if ($this->db->affected_rows() > 0) :
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
        else : return false;
        endif;
    }

    public function record_wage_data($data)
    {
        $this->db->insert('wages', $data);
        $inserted_id = $this->db->insert_id();
        $wage = $this->db->select('*')->from('wages')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "name" => $wage->name,
                "company" => $wage->company,
                "data" => $wage->data,
                "author" => $wage->author,
                "date_created" => $wage->date_created,
                "status" => $wage->status
            );
        else :
            return false;
        endif;
    }

    public function record_wage_assign_data($data)
    {
        $this->db->insert('wage_assigning', $data);
        $inserted_id = $this->db->insert_id();
        $wage = $this->db->select('*')->from('wages')->where('id', $data['wage_id'])->get()->row();

        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $wage->id,
                "name" => $wage->name,
                "company" => $wage->company,
                "data" => $wage->data,
                "date_created" => $wage->date_created,
                "status" => $wage->status,
                "store_id" => $data['store_id'],
                "assign_id" => $inserted_id
            );
        else :
            return false;
        endif;
    }

    public function record_exam_data($data)
    {
        $this->db->insert('exams', $data);
        $inserted_id = $this->db->insert_id();
        $exams = $this->db->select('*')->from('exams')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "applicant_id" => $exams->applicant_id,
                "job_id" => $exams->job_id,
                "exam_id" => $exams->exam_id,
                "date_created" => $exams->date_created,
                "status" => $exams->status,
            );
        else :
            return false;
        endif;
    }

    public function record_pull($company)
    {

        $query = "SELECT * FROM `applications` where `company` = '{$company}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_document_data($data)
    {
        $this->db->insert('documents', $data);
        $inserted_id = $this->db->insert_id();
        $documents = $this->db->select('*')->from('documents')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "applicant_id" => $documents->applicant_id,
                "url" => $documents->url,
                "doctype" => $documents->doctype,
                "created" => $documents->created,
                "status" => $documents->status,
                "archive" => $documents->archive
            );
        else :
            return false;
        endif;
    }

    public function record_document_data_patch($data, $docid, $status)
    {
        $this->db->where('id', $docid);
        $this->db->update('documents', array("status" => $status));


        $this->db->insert('documents', $data);
        $inserted_id = $this->db->insert_id();
        $documents = $this->db->select('*')->from('documents')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "applicant_id" => $documents->applicant_id,
                "url" => $documents->url,
                "doctype" => $documents->doctype,
                "created" => $documents->created,
                "status" => $documents->status,
                "archive" => $documents->archive
            );
        else :
            return false;
        endif;
    }

    public function record_document_archive_patch($data, $docid, $applicant_id)
    {
        $this->db->where('id', $docid);
        $this->db->update('documents', $data);
        if ($this->db->affected_rows() > 0) :
            $record = $this->db->select('id,applicant_id,status,archive')->from('documents')->where('id', $docid)->get()->row();
            return array(
                "id" => $record->id,
                "applicant_id" => $record->applicant_id,
                "status" => $record->status,
                "archive" => $record->archive
            );
        else :
            return false;
        endif;
    }

    public function documents_pull($id)
    {

        $query = "SELECT * FROM `documents` where `applicant_id` = '{$id}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function documents_pull_list($id, $status)
    {
        if ($status === "all") {
            $query = "SELECT * FROM `documents` where `applicant_id` = '{$id}'";
        } else {
            $query = "SELECT * FROM `documents` where `applicant_id` = '{$id}' AND `status` = {$status}";
        }
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }


    public function wages_pull($company)
    {
        $query = "SELECT * FROM wages wg WHERE company = '{$company}'";
        $result = $this->db->query($query);
        $compiled_dd = $result->result_array();
        foreach ($result->result_array() as $k => $wages) {
            $asg_wages =  "SELECT wgasg.id AS assigning_id, wgasg.store_id, wgasg.date_assigned, st.*
            FROM wage_assigning wgasg
            LEFT JOIN store st ON st.id = wgasg.store_id WHERE wgasg.company = '{$company}' AND wgasg.wage_id = {$wages['id']}";
            $asg_result = $this->db->query($asg_wages);

            if ($asg_result->num_rows() > 0) {
                $compiled_dd[$k]['store'] =  $asg_result->result_array();
            }
        }
        return ($result->num_rows() > 0) ? $compiled_dd : false;
    }

    public function record_status_pull($company, $status)
    {

        $query = "SELECT * FROM `applications` where `status` = '{$status}' ORDER BY id DESC";
        $result = $this->db->query($query);
        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            if ($app['company'] == $company) {
                $arr_app[] = $app;
            }
        }
        return ($result->num_rows() > 0) ? $arr_app : false;
    }

    public function record_weeks_pull($company, $weeks)
    {
        $query = "SELECT * FROM `applications` where date_created < now() - interval {$weeks} WEEK OR status = 0";
        $result = $this->db->query($query);
        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            if ($app['company'] == $company) {
                $arr_app[] = $app;
            }
        }
        return ($result->num_rows() > 0) ? $arr_app : false;
    }

    public function record_day_pull($company, $days)
    {
        $query = "SELECT * FROM `applications` where date_created  >= DATE(NOW()) - INTERVAL {$days} DAY OR status = 0 ORDER BY id DESC";
        $result = $this->db->query($query);
        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            if ($app['company'] == $company) {
                $arr_app[] = $app;
            }
        }
        return ($result->num_rows() > 0) ? $arr_app : false;
    }

    /* Documents */
    public function record_documents_pull($company, $status)
    {

        $query = "SELECT * FROM `applications` where `status` = '{$status}' ORDER BY id DESC";
        $result = $this->db->query($query);
        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            $arr_app[$k] = $app;
            if ($app['company'] == $company) {
                $reviews_query = "SELECT * FROM `reviews_doc` WHERE `appl_id` = {$app['id']} ";
                $reviews = $this->db->query($reviews_query);
                if ($reviews->num_rows() > 0) {
                    foreach ($reviews->result_array() as $kk => $kv) {
                        if ($kv['appl_id'] == $app['id']) {
                            $arr_app[$k]['reviews'][] = $reviews->result_array();
                        }
                    }
                }
            }
        }

        return ($result->num_rows() > 0) ? $arr_app : false;
    }
    public function record_specifics_reviews_pull($id)
    {

        $query = "SELECT * FROM `reviews_doc` WHERE `appl_id` = {$id}";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    /* Pool */
    public function record_pool_weeks_pull($company, $weeks)
    {

        $query = "SELECT * FROM `applications` where date_created < now() - interval {$weeks} WEEK OR status = 0";
        $result = $this->db->query($query);
        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            if ($app['company'] == $company) {
                $arr_app[] = $app;
            }
        }
        return ($result->num_rows() > 0) ? $arr_app : false;
    }

    public function record_pool_day_pull($company, $days)
    {

        $query = "SELECT * FROM `applications` where date_created  >= DATE(NOW()) - INTERVAL {$days} DAY";
        $result = $this->db->query($query);
        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            if ($app['company'] == $company) {
                $arr_app[] = $app;
            }
        }
        return ($result->num_rows() > 0) ? $arr_app : false;
    }

    /* Specific */
    public function record_specific_pull($company, $id)
    {

        $query = "SELECT app.*, st.meta_value FROM `applications` app
        LEFT JOIN `settings` st ON app.applying_for = st.id WHERE app.company = '{$company}' AND app.id = '{$id}' LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        if ($result->num_rows() > 0) {
            $exams = "SELECT * FROM `exams` WHERE applicant_id = '{$id}'";
            $reviews = "SELECT * FROM `records` WHERE applicant_id = '{$id}'";
            if ($data[0]['status'] == 5) {
                $data[0]['documents'][] = $this->db->query($reviews)->result_array();
            }
            $data[0]['taken_exam'][] = $this->db->query($exams)->result_array();
            return $data;
        } else {

            return false;
        }
    }

    public function record_reviews_pull($company, $id)
    {

        $query = "SELECT * FROM `reviews` WHERE `company` = '{$company}' AND `applicant_id` = '{$id}' LIMIT 1";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_specific_document_pull($company, $id)
    {

        $query = "SELECT * FROM `reviews_doc` WHERE `appl_company` = '{$company}' AND `appl_id` = '{$id}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    // Team Supervisor

    public function record_ts_specific_pull($company, $ref_id)
    {

        $query = "SELECT app.*, st.meta_value FROM `applications` app
        LEFT JOIN `settings` st ON app.applying_for = st.id WHERE app.company = '{$company}' AND app.reference_id = '{$ref_id}' LIMIT 1";
        $result = $this->db->query($query);
        $data = $result->result_array();
        if ($result->num_rows() > 0) {
            $exams = "SELECT * FROM `exams` WHERE applicant_id = '{$ref_id}'";
            $data[0]['taken_exam'][] = $this->db->query($exams)->result_array();
            return $data;
        } else {

            return false;
        }
    }

    public function record_ts_reviews_pull($company, $ref_id)
    {
        $apl = $this->db->select('*')->from('applications')->where('reference_id', $ref_id)->where('company', $company)->get()->row();
        $query = "SELECT * FROM `reviews` WHERE `company` = '{$company}' AND `applicant_id` = '{$apl->id}' LIMIT 1";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_stores_pull($company)
    {

        $query = "SELECT * FROM `store` WHERE `company` = '{$company}' ORDER BY `name` ASC";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_stores_account_pull($company)
    {

        $query = "SELECT *, usr.id AS usr_id, strs.id AS str_id FROM `users` usr
        LEFT JOIN `assigning` asg ON usr.id = asg.emp_id
        LEFT JOIN `store` strs ON asg.store_id = strs.id
        WHERE strs.company = '{$company}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_stores_details_pull($id, $company)
    {

        $query = "SELECT strs.*, usr.email FROM store strs 
        LEFT JOIN assigning asg ON strs.id = asg.store_id
        LEFT JOIN users usr ON asg.emp_id = usr.id
        WHERE strs.company = '{$company}' AND strs.id = {$id}";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }


    public function records_store_people_pull($company, $store_id)
    {

        $applications_q = "SELECT *, apls.reference_id as gen_id FROM applications apls";
        $result = $this->db->query($applications_q);

        $appls = $result->result_array();
        $return_array = array();
        if ($result->num_rows() > 0) {
            foreach ($appls as $k => $apls) {
                $specific_r = array('applicant_id' => $apls['id'], 'company' => $company, 'store' => $store_id);
                $review = $this->db->select('*')->from('reviews')->where($specific_r)->get()->row_array();
                if (!empty($review) && $review['applicant_id'] == $apls['id']) :

                    if (intval($review['assess_evaluation']) == 1) :
                        $store = $this->db->select('*')->from('store')->where('id', $store_id)->where('company', $company)->get()->row();
                        $job = $this->db->select('*')->from('settings')->where('id', json_decode($apls['applying_for']))->where('company', $company)->get()->row();
                        if (!empty($store) && !empty($job)) {

                            $apls['review'] = $review;
                            $apls['review_status'] = $review['review_status'];
                            $apls['store_name'] = $store->name;
                            $apls['store_id'] = $store->id;
                            $apls['job_title'] = json_decode($job->meta_value)->title;
                            $return_array[] = $apls;
                        }

                    endif;

                endif;
            }
            return (!empty($return_array)) ? $return_array : false;
        } else {
            return false;
        }
    }

    public function record_emails_pull($company)
    {

        $query = "SELECT * FROM `system` WHERE `company` = '{$company}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_logs_pull($company, $type)
    {
        if ($type != null) {
            $query = "SELECT * FROM `activity` WHERE `company` = '{$company}' AND `type` = '{$type}'";
        } else {
            $query = "SELECT * FROM `activity` WHERE `company` = '{$company}'";
        }

        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function record_exam_logs_pull($company)
    {

        $applicants = "SELECT * FROM `applications` WHERE `date_created` >= now()-interval 40 day AND `company` = '{$company}' ORDER BY `id`";
        $result = $this->db->query($applicants);
        if ($result->num_rows() > 0) {
            $app_res = $result->result_array();
            foreach ($app_res as $key => $value) {
                $app_res[$key]['exams'] = array();
                $exams = "SELECT * FROM exams";
                $exams_takers = $this->db->query($exams)->result_array();



                foreach ($exams_takers as $k => $v) {
                    if ($value['id'] == $exams_takers[$k]['applicant_id']) {

                        $stgs_exms = "SELECT * FROM settings WHERE id = '{$exams_takers[$k]["exam_id"]}' AND meta_key = 'exams'";
                        $stgs_details = $this->db->query($stgs_exms)->result_array();
                        if ($stgs_details) {

                            $exam_title = json_decode($stgs_details[0]['meta_value'])->title;
                            if ($exams_takers[$k]) {
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

    public function record_patch_data($data, $status)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('applications', array("status" => $status));

        if ($this->db->affected_rows() > 0) :
            $result = $this->db->select('id,status')->from('applications')->where('id', $data['id'])->get()->row();
            return array(
                "id" => $result->id,
                "status" => $result->status
            );
        else :
            return false;
        endif;
    }

    public function record_remove($uid, $cid, $record_id, $oid)
    {
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

    public function submit_record($id, $data)
    {

        $this->db->where('id', $id);
        $this->db->update('records', $data);

        if ($this->db->affected_rows() > 0) :
            $record = $this->db->select('id,status')->from('records')->where('id', $id)->get()->row();
            return array(
                "id" => $id,
                "status" => $record->status
            );
        else :
            return false;
        endif;
    }

    /* Stores */

    public function system_record_store($data)
    {
        $this->db->insert('store', $data);
        $inserted_id = $this->db->insert_id();
        $store = $this->db->select('*')->from('store')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "name" => $store->name,
                "details" => $store->details,
                "company" => $store->company,
                "status" => $store->status,
                "date_created" => $store->date_created
            );
        else :
            return false;
        endif;
    }

    public function system_record_store_acc_assg($data, $store_id)
    {
        $this->db->insert('users', $data);
        $inserted_id = $this->db->insert_id();
        $user = $this->db->select('*')->from('users')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :

            $asg_data = array(
                "emp_id" => $inserted_id,
                "store_id" => $store_id,
                "company" => $data['company'],
                "date_assigned" => date('Y-m-d H:i:s')
            );


            $this->db->insert('assigning', $asg_data);
            if ($this->db->affected_rows() > 0) :
                return array(
                    "id" => $inserted_id,
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "company" => $user->company,
                    "user" => $user->user_level
                );
            else :
                return false;
            endif;


        else :
            return false;
        endif;
    }



    public function system_record_new_password($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('users', $data);
        if ($this->db->affected_rows() > 0) :
            $store = $this->db->select('*')->from('users')->where('id', $id)->get()->row();
            return array(
                "id" => $store->id
            );
        else :
            return false;
        endif;
    }




    /* DTR */

    public function system_record_dtr($data)
    {
        $this->db->insert('dtr', $data);
        $inserted_id = $this->db->insert_id();
        $dtr = $this->db->select('*')->from('dtr')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "store_id" => $dtr->store_id,
                "author" => $dtr->author,
                "company" => $dtr->company,
                "dtr" => $dtr->dtr,
                "date_created" => $dtr->date_created,
                "status" => $dtr->status
            );
        else :
            return false;
        endif;
    }


    public function system_record_dtr_list($company, $store_id)
    {
        $query = "SELECT * FROM dtr WHERE `company` = '{$company}' AND `store_id` = {$store_id}";
        $result = $this->db->query($query);
        $dtr = $result->result_array();
        if ($result->num_rows() > 0) :
            return $dtr;
        else :
            return false;
        endif;
    }

    public function system_record_wage_list($store_id, $company)
    {
        $query = "SELECT * FROM wages wg
        LEFT JOIN wage_assigning wasg ON wasg.wage_id = wg.id WHERE wasg.company = '{$company}' AND wasg.store_id = {$store_id}";
        $result = $this->db->query($query);
        $dtr = $result->result_array();
        if ($result->num_rows() > 0) :
            return $dtr;
        else :
            return false;
        endif;
    }

    public function system_record_payroll($company, $store)
    {
        $employees = "SELECT * FROM  applications appl
        LEFT JOIN employee emd ON emd.applicant_id = appl.id
        WHERE  appl.status = 6 AND appl.company = '{$company}'";
        $result_emp = $this->db->query($employees);

        $compiled_dd = $result_emp->result_array();
        if ($result_emp->num_rows() > 0) {
            foreach ($result_emp->result_array() as $k => $emp) {
                $dtr = "SELECT * FROM dtr WHERE store_id = {$store} AND emp_id = {$emp["applicant_id"]}";
                $result_dtr = $this->db->query($dtr);
                if ($result_dtr->num_rows() > 0) {
                    $compiled_dd[$k]['dtr'] =  $result_dtr->result_array();
                }
            }

            return ($result_emp->num_rows() > 0) ? $compiled_dd : false;
        } else {
            return false;
        }
    }

    public function system_record_wages_combine($store)
    {
        $query = "SELECT wg.*, wgasg.id AS assigning_id, wgasg.store_id, wgasg.date_assigned FROM wages wg
        LEFT JOIN wage_assigning wgasg ON wgasg.wage_id = wg.id  WHERE wgasg.store_id = '{$store}'";
        $result = $this->db->query($query);
        $compiled_dd = $result->result_array();
        return ($result->num_rows() > 0) ? $compiled_dd : false;
    }



    /** Accounts **/

    public function new_user($data)
    {

        $validate_email = "SELECT * FROM users WHERE email = '{$data['email']}'";
        $result_vd = $this->db->query($validate_email);

        if ($result_vd->num_rows() > 0) {
            return $this->response_code(204, "Email already exist", "");
        } else {
            $this->db->insert('users', $data);
            $inserted_id = $this->db->insert_id();

            $record = $this->db->select('id,first_name,last_name,email,profile,date_created,filename')->from('users')->where('id', $inserted_id)->get()->row();

            if ($this->db->affected_rows() > 0) :
                return array(
                    "id" => $this->db->insert_id(),
                    "first_name" => $record->first_name,
                    "last_name" => $record->last_name,
                    "email" => $record->email,
                    "profile" => $record->profile,
                    "filename" => $record->filename,
                    "date_created" => $record->date_created,
                );
            else : return false;
            endif;
        }
    }

    public function check_user($uid, $product_id)
    {
        $query = "SELECT * FROM users WHERE id = {$uid} AND  product_id = {$product_id} ORDER BY id DESC";
        $result = $this->db->query($query);

        return ($result->num_rows() > 0) ? true : false;
    }

    public function user_pull($id)
    {

        $query = "SELECT * FROM applications WHERE id = '{$id}' LIMIT 1";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }


    public function update_user($uid, $post_id, $data)
    {
        $this->db->where('post_id', $post_id);
        $this->db->where('meta_key', '_price');
        $this->db->update('wp_postmeta', $data);

        $acc = $this->db->select('id,email,first_name,last_name,profile,product_id')->from('users')->where('id', $uid)->get()->row();
        $woocom_meta = $this->db->select('meta_value')->from('wp_postmeta')->where('meta_key', '_price')->where('post_id', $acc->product_id)->get()->row();
        $id = $acc->id;

        if (!$acc) return $this->response_code(204, "User invalid", "");

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

    public function update_details_user($id, $data)
    {
        $acc = $this->db->select('password,id,email,first_name,last_name,profile,product_id')->from('users')->where('id', $id)->get()->row();
        $woocom_meta = $this->db->select('meta_value')->from('wp_postmeta')->where('meta_key', '_price')->where('post_id', $acc->product_id)->get()->row();
        $woocom_details = "SELECT p.*, ( SELECT guid FROM wp_posts WHERE id = m.meta_value ) AS imgurl,  (SELECT meta_value FROM wp_postmeta pm WHERE meta_key='_wp_attachment_metadata' AND pm.post_id=m.meta_value ) AS imgdetails FROM wp_posts p
       LEFT JOIN  wp_postmeta m ON(p.id = m.post_id AND m.meta_key =  '_thumbnail_id' ) WHERE p.post_type =  'product' AND p.id= {$acc->product_id}";
        $woo_details = $this->db->query($woocom_details);

        if ($woo_details->num_rows() > 0) {
            $woo_details = $woo_details->result()[0];
        }

        if (!$acc || $acc->email != $data['old']) return $this->response_code(204, "User invalid details", "");

        $new_email = array("email" => $data['new']);
        $this->db->where('id', $id);
        $this->db->update('users', $new_email);

        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $id,
                "email" => $data['new'],
                "firstname" => $woo_details->post_title,
                "lastname" => " ",
                "profile" => $woo_details->imgurl,
                "product_id" => $acc->product_id,
                "rate" => $woocom_meta->meta_value,
            );
        else :
            return false;
        endif;
    }

    public function user_update_details($data, $id)
    {
        $arr = array(
            "first_name" => $data['firstname'],
            "last_name" => $data['lastname'],
            "email" => $data['email'],
        );

        $this->db->where('id', $id);
        $this->db->update('users', $arr);
        if ($this->db->affected_rows() > 0) :
            return $data;
        else :
            return false;
        endif;
    }


    public function update_user_password($id, $passwords)
    {

        $acc = $this->db->select('password,id,email,first_name,last_name,user_level')->from('users')->where('id', $id)->get()->row();
        $grab_password =  $acc->password;
        $grab_email =  $acc->email;
        $id = $acc->id;


        if (!$acc) return $this->response_code(204, "User invalid", "");

        if (password_verify($passwords['old'],  $grab_password)) :
            $data = array(
                "password" => password_hash($passwords['new'], PASSWORD_DEFAULT)
            );


            $this->db->where('id', $id);
            $this->db->update('users', $data);

            if ($this->db->affected_rows() > 0) :
                return array(
                    "id" => $id,
                    "email" => $acc->email,
                    "firstname" => $acc->first_name,
                    "lastname" => $acc->last_name,
                    "user_level" => $acc->user_level
                );
            else :
                return $this->response_code(204, "User unable to change current password", "");
            endif;
        else :
            return $this->response_code(204, "User invalid current password", "");

        endif;
    }


    public function set_token($uid, $data)
    {
        $this->db->where('id', $uid);
        $this->db->update('users', $data);

        $acc = $this->db->select('id,token')->from('users')->where('id', $uid)->get()->row();

        if (!$acc) return $this->response_code(204, "User invalid", "");
        $result = array(
            "id" => $acc->id,
            "token" => $acc->token
        );
        return ($this->db->affected_rows() > 0) ? $result : false;
    }


    /** Notification **/

    public function notify_user($data)
    {

        $this->db->insert('notifications', $data);
        $inserted_id = $this->db->insert_id();

        $notify = $this->db->select('*')->from('notifications')->where('id', $inserted_id)->get()->row();
        $token = $this->db->select('token')->from('users')->where('product_id', $notify->uid)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $this->db->insert_id(),
                "uid" => $notify->uid,
                "oid" => $notify->oid,
                "message" => $notify->message,
                "status" => $notify->status,
                "date_created" => $notify->date_created,
                "token" => $token->token
            );
        else : return false;
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

        if (!$token_validity) return $this->response_code(401, "", "");

        $this->db->where('user_id', $users_id)->where('token', $token)->delete('user_token');
        return $this->response_code(200, '', '');
    }

    /* People */
    public function system_record_people($data, $temp_password)
    {
        $validate_acc = $this->db->select('*')->from('users')->where('email', $data['email'])->get();
        if ($validate_acc->num_rows() == 0) {
            $this->db->insert('users', $data);
            $inserted_id = $this->db->insert_id();
            $people = $this->db->select('*')->from('users')->where('id', $inserted_id)->get()->row();
            $return_url = STAFF_URL;
            if (intval($people->user_level) === 5) {
                $return_url = WORKPLACE_URL;
            }
            if ($this->db->affected_rows() > 0) :
                return array(
                    "id" => $inserted_id,
                    "company" => $people->company,
                    "email" => $people->email,
                    "first_name" => $people->first_name,
                    "last_name" => $people->last_name,
                    "user_level" => $people->user_level,
                    "date_created" => $people->date_created,
                    "temp_password" => $temp_password,
                    "return_url" => $return_url
                );
            else :
                return false;
            endif;
        } else {
            return false;
        }
    }

    public function system_record_people_password($data, $old_password)
    {
        $validate_acc = $this->db->select('*')->from('users')->where('email', $data['email'])->get()->row();
        if ($validate_acc->num_rows() == 0) {
            if (password_verify($old_password,  $validate_acc->password)) {
                $this->db->where('id', $validate_acc->id);
                $this->db->update('users', $data);
                if ($this->db->affected_rows() > 0) :
                    return array(
                        "id" => $validate_acc->id,
                        "email" => $validate_acc->email,
                        "firstname" => $validate_acc->first_name,
                        "lastname" => $validate_acc->last_name,
                        "company" => $validate_acc->company,
                        "profile" => $validate_acc->profile,
                        "user_level" => $validate_acc->user_level
                    );
                else :
                    return $this->response_code(204, "User invalid", "");
                endif;
            } else {
                return $this->response_code(204, "User invalid", "");
            }
        } else {
            return $this->response_code(204, "User invalid", "");
        }
    }

    public function system_record_reset_people($data, $temp_password)
    {
        $validate_acc = $this->db->select('*')->from('users')->where('email', $data['email'])->get()->row();
        if ($validate_acc) {
            $this->db->where('id', $validate_acc->id);
            $this->db->update('users', $data);
            $people = $this->db->select('*')->from('users')->where('id', $validate_acc->id)->get()->row();
            $return_url = STAFF_URL;
            if (intval($people->user_level) === 5) {
                $return_url === WORKPLACE_URL;
            }

            if ($this->db->affected_rows() > 0) :
                return array(
                    "id" => $validate_acc->id,
                    "company" => $people->company,
                    "email" => $people->email,
                    "first_name" => $people->first_name,
                    "last_name" => $people->last_name,
                    "user_level" => $people->user_level,
                    "date_created" => $people->date_created,
                    "temp_password" => $temp_password,
                    "return_url" => $return_url
                );
            else :
                return false;
            endif;
        } else {
            return false;
        }
    }

    public function system_people_assign($data)
    {
        $this->db->insert('assigning', $data);
        $inserted_id = $this->db->insert_id();
        $assign = $this->db->select('*')->from('assigning')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "emp_id" => $assign->emp_id,
                "store_id" => $assign->store_id,
                "company" => $assign->company,
                "date_assigned" => $assign->date_assigned,
            );
        else :
            return false;
        endif;
    }


    public function system_people_pull($company)
    {
        $query = "SELECT * FROM users WHERE user_level != 3 AND  company = '{$company}' ORDER BY id DESC";
        $result = $this->db->query($query);
        $user_arr = array();
        foreach ($result->result_array() as $users) {
            if ($users['user_level'] == 5) {

                $query_store = "SELECT * FROM assigning asg LEFT JOIN store st ON asg.store_id = st.id
                WHERE asg.emp_id = {$users['id']}";
                $store = $this->db->query($query_store)->result_array();
                if ($store) {
                    $users['assigned'] = $store;
                }
            }
            $user_arr[] = $users;
        }
        return ($result->num_rows() > 0) ? $user_arr : false;
    }

    public function system_people_specific_pull($company, $id)
    {
        $query = "SELECT * FROM users WHERE user_level != 3 AND  company = '{$company}' AND id = {$id} ORDER BY id DESC";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }

    public function system_people_validate($email)
    {
        $query = "SELECT * FROM users WHERE email = '{$email}'";
        $result = $this->db->query($query);
        return ($result->num_rows() > 0) ? true : false;
    }


    /* Reports */

    public function system_record_report($data)
    {
        $this->db->insert('reports', $data);
        $inserted_id = $this->db->insert_id();
        $report = $this->db->select('*')->from('reports')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "company" => $report->company,
                "name" => $report->name,
                "details" => $report->details,
                "status" => $report->status,
                "date_created" => $report->date_created
            );
        else :
            return false;
        endif;
    }

    /* Log activity */


    public function record_log($data)
    {
        $this->db->insert('activity', $data);
        return $this->db->affected_rows() != 1  ? false : true;
    }

    public function record_system($data)
    {
        $this->db->insert('system', $data);
        return $this->db->affected_rows() != 1  ? false : true;
    }


    public function system_record_jobs($data)
    {
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $jobs = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            );
        else :
            return false;
        endif;
    }

    public function system_record_exams($data)
    {
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $jobs = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            );
        else :
            return false;
        endif;
    }

    public function system_record_update_exams($data, $exam_id)
    {

        $this->db->where('id', $exam_id);
        $this->db->update('settings', $data);

        if ($this->db->affected_rows() > 0) :
            $jobs = $this->db->select('*')->from('settings')->where('id', $exam_id)->get()->row();
            return array(
                "id" => $jobs->id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            );
        else :
            return false;
        endif;
    }


    public function system_record_remove_exams($exam_id)
    {

        $this->db->where('id', $exam_id);
        $this->db->delete("settings");

        if ($this->db->affected_rows() > 0) :
            return true;
        else :
            return false;
        endif;
    }

    /* Requirements */

    public function system_record_requirements($data)
    {
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $jobs = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            );
        else :
            return false;
        endif;
    }


    public function system_update_requirements($data, $req_id)
    {
        $this->db->where('id', $req_id);
        $this->db->update('settings', $data);
        $jobs = $this->db->select('*')->from('settings')->where('id', $req_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $req_id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            );
        else :
            return false;
        endif;
    }

    public function system_record_uploading_status($data)
    {
        $this->db->insert('settings', $data);
        $inserted_id = $this->db->insert_id();
        $isUpload = $this->db->select('*')->from('settings')->where('id', $inserted_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $inserted_id,
                "company" => $isUpload->company,
                "posted_by" => $isUpload->posted_by,
                "meta_key" => $isUpload->meta_key,
                "meta_value" => $isUpload->meta_value,
                "date_created" => $isUpload->date_created,
            );
        else :
            return false;
        endif;
    }


    public function system_update_uploading_status($data, $us_id)
    {
        $this->db->where('id', $us_id);
        $this->db->update('settings', $data);
        $jobs = $this->db->select('*')->from('settings')->where('id', $us_id)->get()->row();
        if ($this->db->affected_rows() > 0) :
            return array(
                "id" => $us_id,
                "company" => $jobs->company,
                "posted_by" => $jobs->posted_by,
                "meta_key" => $jobs->meta_key,
                "meta_value" => $jobs->meta_value,
                "date_created" => $jobs->date_created,
            );
        else :
            return false;
        endif;
    }

    /* Email */

    public function system_record_update_email($data, $email_id)
    {
        $applicants = $this->db->select('*')->from('applications')->where('reference_id', $email_id)->get()->row();
        if ($this->db->affected_rows() > 0) :

            $profile = json_decode($applicants->data);
            $profile->person_email = $applicants->username = $data['email'];
            $update_application = array(
                "data" => json_encode($profile),
                "username" => $applicants->username
            );

            $this->db->where('reference_id', $email_id);
            $this->db->update('applications', $update_application);

            if ($this->db->affected_rows() > 0) :

                // get the system table's data
                $system = $this->db->select('*')->from('system')->where('user', $email_id)->get();

                if ($system->num_rows() > 0) {
                    $system = $system->row();
                    $mail_data = json_decode($system->data);
                    $mail_data->personalizations[0]->to = $mail_data->personalizations[0]->dynamic_template_data->email = $data['email'];
                    $mail_new_data = array(
                        "data" => json_encode($mail_data),
                        "email" => $data['email']
                    );

                    $this->db->where('user', $email_id);
                    $this->db->update('system', $mail_new_data);

                    return array(
                        "id" => $system->id,
                        "user" => $system->user,
                        "type" => $system->type,
                        "message" => $system->message,
                        "data" => $system->data,
                        "email" => $system->email,
                    );
                } else {
                    return array(
                        "id" => $applicants->id,
                        "user" => $applicants->reference_id,
                        "email" => $applicants->username,
                    );
                }


            else :
                return false;
            endif; // update for application table

        else :
            return false;
        endif; // get of applicant's profile


    }



    public function record_get_system($ref_id)
    {
        $applicant = $this->db->select('*')->from('applications')->where('reference_id', $ref_id)->get();
        $system = $this->db->select('*')->from('system')->where('user', $ref_id)->get();

        if ($applicant->num_rows() > 0) {
            $applicant = $applicant->row();
            if ($system->num_rows() > 0) {
                return array(
                    "reference_id" => $applicant->reference_id,
                    "username" => $applicant->username,
                    "company" => $applicant->company,
                    "data" => $system->row()->data,
                    "return_url" => MEMBER_URL
                );
            } else {
                return array(
                    "reference_id" => $applicant->reference_id,
                    "username" => $applicant->username,
                    "company" => $applicant->company,
                );
            }
        } else {
            return false;
        }
    }

    public function system_jobs_pull($company, $id, $jobs)
    {

        $jobs = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = '{$jobs}'";
        $result = $this->db->query($jobs);
        if ($result->num_rows() > 0) {
            $jobs_result = $result->result_array();


            foreach ($jobs_result as $key => $value) {
                $jobs_result[$key]['exams'] = array();
                $exams = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'exams'";
                $exams_result = $this->db->query($exams);

                $jobs_result[$key]['requirements'] = array();
                $requirements = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'requirements'";
                $requirements_result = $this->db->query($requirements);

                $jobs_result[$key]['isUpload'] = array();
                $isUpload = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'uploading_status'";
                $isUpload_result = $this->db->query($isUpload);

                if ($requirements_result->num_rows() > 0) {
                    $requirements_result = $requirements_result->result_array();
                    foreach ($requirements_result as $kr => $vr) {
                        if ($value['id'] == json_decode($requirements_result[$kr]['meta_value'])->job_id) {
                            $jobs_result[$key]['requirements'][] =  $requirements_result[$kr];
                        }
                    }
                }

                if ($exams_result->num_rows() > 0) {
                    $exams_result = $exams_result->result_array();
                    foreach ($exams_result as $k => $v) {
                        if ($value['id'] == json_decode($exams_result[$k]['meta_value'])->job_id) {
                            $jobs_result[$key]['exams'][] =  $exams_result[$k];
                        }
                    }
                }

                if ($isUpload_result->num_rows() > 0) {
                    $isUpload_result = $isUpload_result->result_array();
                    foreach ($isUpload_result as $ku => $v) {
                        if ($value['id'] == json_decode($isUpload_result[$ku]['meta_value'])->job_id) {
                            $jobs_result[$key]['isUpload'][] =  $isUpload_result[$ku];
                        }
                    }
                }
            }
        }
        return ($result->num_rows() > 0) ? $jobs_result : false;
    }

    public function system_jobs_specific_pull($company, $job_id, $jobs)
    {

        $jobs = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = '{$jobs}' AND id = {$job_id} LIMIT 1";

        $result = $this->db->query($jobs);
        if ($result->num_rows() > 0) {
            $jobs_result = $result->result_array();

            foreach ($jobs_result as $key => $value) {
                $jobs_result[$key]['exams'] = array();
                $exams = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'exams'";
                $exams_result = $this->db->query($exams);

                $jobs_result[$key]['requirements'] = array();
                $requirements = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'requirements'";
                $requirements_result = $this->db->query($requirements);

                $jobs_result[$key]['isUpload'] = array();
                $isUpload = "SELECT * FROM `settings` where `company` = '{$company}' AND `meta_key` = 'uploading_status'";
                $isUpload_result = $this->db->query($isUpload);

                if ($requirements_result->num_rows() > 0) {
                    $requirements_result = $requirements_result->result_array();
                    foreach ($requirements_result as $kr => $vr) {
                        if ($value['id'] == json_decode($requirements_result[$kr]['meta_value'])->job_id) {
                            $jobs_result[$key]['requirements'][] =  $requirements_result[$kr];
                        }
                    }
                }

                if ($exams_result->num_rows() > 0) {
                    $exams_result = $exams_result->result_array();
                    foreach ($exams_result as $k => $v) {
                        if ($value['id'] == json_decode($exams_result[$k]['meta_value'])->job_id) {
                            $jobs_result[$key]['exams'][] =  $exams_result[$k];
                        }
                    }
                }

                if ($isUpload_result->num_rows() > 0) {
                    $isUpload_result = $isUpload_result->result_array();
                    foreach ($isUpload_result as $ku => $v) {
                        if ($value['id'] == json_decode($isUpload_result[$ku]['meta_value'])->job_id) {
                            $jobs_result[$key]['isUpload'][] =  $isUpload_result[$ku];
                        }
                    }
                }
            }
        }

        return ($result->num_rows() > 0) ? $jobs_result : false;
    }

    public function record_exam_pull($data)
    {
        $jobs = "SELECT * FROM `settings` where `company` = '{$data['company']}' AND `meta_key` = 'jobs' AND id = {$data["job_id"]} LIMIT 1";
        $result = $this->db->query($jobs);

        if ($result->num_rows() > 0) {
            $jobs_result = $result->result_array();

            foreach ($jobs_result as $key => $value) {
                $jobs_result[$key]['exams'] = array();
                $exams = "SELECT * FROM `settings` WHERE `id` = {$data['exam_id']}";
                $exams_result = $this->db->query($exams);

                if ($exams_result->num_rows() > 0) {
                    $exams_result = $exams_result->result_array();
                    foreach ($exams_result as $k => $v) {
                        $jobs_result[$key]['exams'][] =  $exams_result[$k];
                    }
                }
            }
        }

        return ($result->num_rows() > 0) ? $jobs_result[0] : false;
    }

    /* Training */

    public function record_employee_status_pull($company, $status)
    {

        $query = "SELECT *, appl.id AS applicant_id, strs.id AS str_id, strs.name AS str_name, strs.details AS str_details, trs.status AS training_status  FROM `applications` appl
        LEFT JOIN `assigning` asg ON appl.id = asg.emp_id
        LEFT JOIN `store` strs ON asg.store_id = strs.id
        LEFT JOIN `training` trs ON trs.appl_id = appl.id
        WHERE strs.company = '{$company}' AND appl.status = {$status}";
        $result = $this->db->query($query);

        $arr_app = [];
        foreach ($result->result_array() as $k => $app) {
            if ($app['company'] == $company) {
                $arr_app[] = $app;
            }
            $store = json_decode($app['str_details']);
            $arr_app[$k]['str_name'] = $store->store_type . " - " . $app['str_name'];
        }
        return ($result->num_rows() > 0) ? $arr_app : false;
    }

    public function record_specific_employee_pull($id)
    {

        $query = "SELECT *, appl.id AS applicant_id, strs.id AS str_id, strs.name AS str_name, strs.details AS str_details, trs.status AS training_status  FROM `applications` appl
        LEFT JOIN `assigning` asg ON appl.id = asg.emp_id
        LEFT JOIN `store` strs ON asg.store_id = strs.id
        LEFT JOIN `training` trs ON trs.appl_id = appl.id
        WHERE appl.id = '{$id}'";

        $result = $this->db->query($query);

        $arr_app = [];

        foreach ($result->result_array() as $k => $app) {
            $store = json_decode($app['str_details']);
            $arr_app[$k]['str_name'] = $store->store_type . " - " . $app['str_name'];
        }
        return ($result->num_rows() > 0) ? $result->result_array() : false;
    }
}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Base_Model extends CI_Model {

    public $unauthorize = array('status' => 401,'message' => 'Unauthorized.');
    public $client_error = array('status' => 422,'message' => 'Client error.');
    public $bad_request = array('status' => 400,'message' => 'Bad request.');
    public $internal = array('status' => 500,'message' => 'Internal server error.');
    public $success = array('status' => 200);
    public $invalid = array('status' => 204);

    public function response_code($status_code, $message, $data){
        $response = "";
        switch($status_code){
            case 200:
                $this->success['message'] = $message;
                $this->success['result'] = $data;
                $response =  $this->success;
            break;
            case 204:
                $this->invalid['message'] = $message;
                $response =  $this->invalid;
            break;
            case 400:
                $response =  $this->bad_request;
            break;
            case 401:
                $response =  $this->unauthorize;
            break;
            case 422:
                $response =  $this->client_error;
            break;
            case 500: 
                $response =  $this->internal;
            break;
            default:
                $response = $this->internal;
        }
        return $response;
    }

}
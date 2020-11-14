<?php
require APPPATH . '/libraries/Base_Controller.php';
defined('BASEPATH') or exit('No direct script access allowed');
class Main extends Base_Controller
{
    public  $data = [];
    public  $auth = false;
    public $method = "";

    function __construct()
    {
        parent::__construct();
        $this->load->view('deploy');
        $this->load->view('default');

    }

    public function view_document($id = NULL, $company = NULL){
        $result['fetch'] =  $this->Main_mdl->records_doc_pull($data['id'], $data['company']);
        header("Content-type: " . $row["imageType"]);
        echo $row["imageData"];
    }
}

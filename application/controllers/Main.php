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
    }
}

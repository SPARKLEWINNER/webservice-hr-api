<?php
defined('BASEPATH') or exit('No direct script access allowed');

$autoload['packages'] = array();
$autoload['libraries'] = array('form_validation', 'database', 'session', 'pagination', 'email');
$autoload['drivers'] = array();
$autoload['helper'] = array('url', 'file', 'string', 'form', 'security', 'jwt','authorization');
$autoload['config'] = array('jwt');
$autoload['language'] = array();
$autoload['model'] = array('Main_mdl');

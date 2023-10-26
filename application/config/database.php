<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$connected = @fsockopen("www.google.com", 80);
$hostname = "";
$database = "";
$username = "";
$password = "";

if ($_SERVER['HTTP_HOST'] == "localhost:8080") :
	$hostname = "localhost";
	$database = "sparkles";
	$username = "root";
	$password = ""; //macos mysql
else:
	$hostname = "localhost";
	$database = "webapi_portal";
	$username = "webapi_livedev";
	$password = "qj5FHv6d4So2";
endif;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $hostname,
	'username' => $username,
	'password' => $password,
	'database' =>  $database,
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

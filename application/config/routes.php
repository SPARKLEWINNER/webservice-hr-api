<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'main';
$route['404_override'] = 'main/error_html';
$route['translate_uri_dashes'] = FALSE;


/** Authentication **/
$route['internal/auth/login']['post'] = 'auth/login';
$route['internal/auth/logout']['post'] = 'auth/logout';

/** Records **/
$route['internal/record/create']['post'] = 'record/create';
$route['internal/record/show/(:num)/(:num)/(:num)']['get'] = 'record/records/$1/$2/$3';
$route['internal/record/remove']['post'] = 'record/remove';
$route['internal/record/apply_review']['patch'] = 'record/review_record';

/** Accounts **/
$route['internal/account/create']['post'] = 'accounts/create';
$route['internal/account/show/(:num)/(:num)']['get'] = 'accounts/user/$1/$2';
$route['internal/account/update']['post'] = 'accounts/user_rate';
$route['internal/account/user/update']['patch'] = 'accounts/update_user';
$route['internal/account/user/update/password']['patch'] = 'accounts/update_user_password';
$route['internal/account/token/update']['patch'] = 'accounts/register_token';

/** Orders **/
$route['internal/orders/show/(:num)/(:num)']['get'] = 'orders/orders/$1/$2';
$route['internal/orders/list/(:num)/(:num)/(:any)']['get'] = 'orders/orders_list/$1/$2/$3';


/** Notifications **/
$route['internal/notify/user']['post'] = 'accounts/user_notify'; 
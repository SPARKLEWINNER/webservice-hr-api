<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'main';
$route['404_override'] = 'main/error_html';
$route['translate_uri_dashes'] = FALSE;

$route['internal/deploy'] = 'main/deploy';

/** Authentication **/
$route['internal/auth/login']['post'] = 'auth/login';
$route['internal/auth/logout']['post'] = 'auth/logout';
$route['internal/auth/forgot']['post'] = 'auth/forgot';
$route['internal/auth/reset']['patch'] = 'auth/reset';

/** Records **/
$route['internal/record/create']['post'] = 'record/create';
$route['internal/record/review_app']['post'] = 'record/review_app';

$route['internal/record/review']['patch'] = 'record/in_review';

$route['internal/record/(:any)']['get'] = 'record/applicants/$1';
$route['internal/record/specific/(:any)/(:any)']['get'] = 'record/applicants_specific/$1/$2';
$route['internal/record/specific/reviews/(:any)/(:any)']['get'] = 'record/applicants_specific_reviews/$1/$2';


$route['internal/record/show/(:num)/(:num)/(:num)']['get'] = 'record/records/$1/$2/$3';
$route['internal/record/remove']['post'] = 'record/remove';
$route['internal/record/apply_review']['patch'] = 'record/review_record';



/** Exams **/
$route['internal/exam/iq/create']['post'] = 'exams/iq_create';

$route['internal/user/(:any)']['get'] = 'accounts/user/$1';
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
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
$route['internal/record/review_store_app']['post'] = 'record/review_store_app';
$route['internal/record/exam']['post'] = 'record/exam_take';
$route['internal/record/upload/documents']['post'] = 'record/upload_documents';

$route['internal/record/review']['patch'] = 'record/in_review';

$route['internal/record/(:any)']['get'] = 'record/applicants/$1';
$route['internal/record/(:any)/(:num)']['get'] = 'record/applicants_status/$1/$2';
$route['internal/record/sort/(:any)/(:any)/(:num)']['get'] = 'record/applicants_weekly/$1/$2/$3';
$route['internal/record/pool/(:any)/(:any)/(:num)']['get'] = 'record/applicants_pool/$1/$2/$3';
$route['internal/record/specific/(:any)/(:any)']['get'] = 'record/applicants_specific/$1/$2';
$route['internal/record/specific/reviews/(:any)/(:any)']['get'] = 'record/applicants_specific_reviews/$1/$2';


$route['internal/record/apply_review']['patch'] = 'record/review_record';
$route['internal/record/bypass']['patch'] = 'record/review_bypass_record';

$route['internal/stores/(:any)']['get'] = 'record/stores_record/$1';

$route['internal/wage/create']['post'] = 'record/wage_create_record';
$route['internal/wage/assign']['post'] = 'record/wage_assign_record';
$route['internal/wages/(:any)']['get'] = 'record/wages/$1';


/* -- Emails */
$route['internal/resend/email']['post'] = 'system/resend_email';
$route['internal/system/update/email']['patch'] = 'system/update_email';

/* -- People */
$route['internal/system/create/people']['post'] = 'system/create_people';
$route['internal/system/update/people/password']['post'] = 'system/update_people';
$route['internal/system/update/people/reset']['post'] = 'system/reset_password';
$route['internal/system/assign/people']['post'] = 'system/assign_people';
$route['internal/system/people/(:any)']['get'] = 'system/peoples/$1';
$route['internal/system/specific/people/(:any)/(:num)']['get'] = 'system/people_specific/$1/$2';

/* -- Jobs */
$route['internal/system/create/jobs']['post'] = 'system/create_job';
$route['internal/system/jobs/(:any)/(:any)']['get'] = 'system/jobs_records/$1/$2';
$route['internal/system/specific/jobs/(:any)/(:any)']['get'] = 'system/job_specific_records/$1/$2';

/* -- Stores */
$route['internal/system/create/store']['post'] = 'system/create_store';

/* -- Exams */
$route['internal/system/create/exams']['post'] = 'system/create_exams';
$route['internal/system/update/exams']['patch'] = 'system/update_exams';
$route['internal/system/remove/exams/(:any)']['delete'] = 'system/remove_exams/$1';

/* Documents CMS */
$route['internal/system/create/requirements']['post'] = 'system/create_requirements';
$route['internal/system/update/requirements']['post'] = 'system/update_requirements';

/* User */
$route['internal/user/(:any)']['get'] = 'accounts/user/$1';

/* Supervisors */
$route['internal/record/ts/specific/(:any)/(:any)']['get'] = 'record/applicants_ts_specific/$1/$2';
$route['internal/record/ts/specific/reviews/(:any)/(:any)']['get'] = 'record/applicants_ts_specific_reviews/$1/$2';
$route['internal/stores/ts/(:num)/(:any)']['get'] = 'record/store_people_record/$1/$2';

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


/* Read Document */
$route['uploads/docs/(:any)/(:any)'] = 'main/view_document/$1/$2';

/* Profile */
$route['internal/profile/reports']['post'] = 'system/create_report';

/* Computations */
$route['internal/record/dtr/create']['post'] = 'system/create_dtr';
$route['internal/record/payroll/get/(:any)/(:any)']['get'] = 'system/payroll_record/$1/$2';

/* Logs */
$route['internal/emails/(:any)']['get'] = 'logs/emails_record/$1';
$route['internal/logs/(:any)']['get'] = 'logs/logs_record/$1';
$route['internal/application/logs/(:any)']['get'] = 'logs/application_record/$1';
$route['internal/activity/logs/(:any)']['get'] = 'logs/activity_record/$1';
$route['internal/exams/logs/(:any)']['get'] = 'logs/exam_logs_record/$1';

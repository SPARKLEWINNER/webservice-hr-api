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
$route['internal/auth/validate']['post'] = 'auth/me/$1';


$route['internal/v1/auth/login']['post'] = 'auth/workplace_login';

/** Applicant - Record **/
$route['internal/record/create']['post'] = 'record/applicant_create'; // step 1
$route['internal/record/exam']['post'] = 'record/applicant_exam_create'; // step 2
$route['internal/record/upload/documents']['post'] = 'record/applicant_document_create'; // step 5
$route['internal/record/documents/(:any)']['get'] = 'record/applicant_documents/$1'; // step 5.5
$route['internal/record/documents/uploaded/(:any)/(:any)']['get'] = 'record/applicant_document_lists/$1/$2'; // step 5.1 - admin


/* Recruitment */
$route['internal/record/review_app']['post'] = 'recruitment/review_create';
$route['internal/record/review_app/documents']['post'] = 'recruitment/review_create_document';
$route['internal/record/recruitment/final']['post'] = 'recruitment/recruitment_final';

$route['internal/record/review']['patch'] = 'recruitment/review_update';

$route['internal/record/(:any)']['get'] = 'recruitment/list_applicants/$1'; // summation for all
$route['internal/record/(:any)/(:num)']['get'] = 'recruitment/list_applicants_status/$1/$2'; // with filter (day/weekly) && no. of days
$route['internal/record/sort/(:any)/(:any)/(:num)']['get'] = 'recruitment/list_applicants_datecreated/$1/$2/$3';
$route['internal/record/pool/(:any)/(:any)/(:num)']['get'] = 'recruitment/list_applicants_datecreated_get/$1/$2/$3';

$route['internal/record/document/update']['post'] = 'record/applicant_document_archive';
/* Store */
$route['internal/system/create/store']['post'] = 'store/store_new'; // admin
$route['internal/system/update/store/account']['post'] = 'store/store_new_password'; // admin

$route['internal/record/review_store_app']['post'] = 'store/review_create'; // ts

$route['internal/stores/ts/(:num)/(:any)']['get'] = 'store/list_applicants/$1/$2'; // ts
$route['internal/stores/(:any)']['get'] = 'store/list_stores/$1';
$route['internal/store/(:num)/(:any)']['get'] = 'store/store_details/$1/$2'; // admin
$route['internal/stores/accounts/(:any)']['get'] = 'store/list_stores_accounts/$1'; // ts accounts


$route['internal/stores/dtr/list/(:any)/(:any)']['get'] = 'system/list_dtr/$1/$2';
$route['internal/stores/wage/(:any)/(:any)']['get'] = 'system/list_wage/$1/$2';

/* Finance */
$route['internal/wage/create']['post'] = 'finance/wage_create_record';
$route['internal/wage/assign']['post'] = 'finance/wage_assign_record';
$route['internal/wages/(:any)']['get'] = 'finance/list_wages/$1';

$route['internal/record/documents/(:any)/(:num)']['get'] = 'record/applicants_specific_reviews_documents/$1/$2'; // with filter (day/weekly) && no. of days
$route['internal/record/documents/specific/notices/(:num)']['get'] = 'record/applicants_specific_documents/$1';

$route['internal/record/specific/(:any)/(:any)']['get'] = 'record/applicants_specific/$1/$2';
$route['internal/record/specific/reviews/(:any)/(:any)']['get'] = 'record/applicants_specific_reviews/$1/$2';
$route['internal/record/specific/reviews/documents/(:any)/(:any)']['get'] = 'record/applicants_specific_reviews__documents/$1/$2';
$route['internal/record/specific/documents/(:any)/(:any)']['get'] = 'record/applicant_document_lists/$1/$2';

$route['internal/record/bypass']['patch'] = 'record/review_bypass_record';



/* -- Emails */
$route['internal/resend/email']['post'] = 'system/create_email_request';
$route['internal/system/update/email']['patch'] = 'system/update_email';

/* -- People */
$route['internal/system/create/people']['post'] = 'system/create_people';
$route['internal/system/update/people/password']['post'] = 'system/update_people';
$route['internal/system/update/people/reset']['post'] = 'system/reset_password';
$route['internal/system/assign/people']['post'] = 'system/people_assign_store';
$route['internal/system/people/(:any)']['get'] = 'system/peoples/$1';
$route['internal/system/specific/people/(:any)/(:num)']['get'] = 'system/people_specific/$1/$2';

/* -- Jobs */
$route['internal/system/create/jobs']['post'] = 'system/create_job';
$route['internal/system/jobs/(:any)']['get'] = 'system/jobs_records/$1';
$route['internal/system/specific/jobs/(:any)/(:any)']['get'] = 'system/job_specific_records/$1/$2';


/* -- Exams */
$route['internal/system/create/exams']['post'] = 'system/create_exams';
$route['internal/system/update/exams']['patch'] = 'system/update_exams';
$route['internal/system/remove/exams/(:any)']['delete'] = 'system/remove_exams/$1';

/* Documents CMS */
$route['internal/system/create/requirements']['post'] = 'system/requirements_create';
$route['internal/system/update/requirements']['post'] = 'system/requirements_update';

$route['internal/system/create/upload/status']['post'] = 'system/upload_status_create';
$route['internal/system/update/upload/status']['patch'] = 'system/upload_status_update';


/* Supervisors */
$route['internal/record/ts/specific/(:any)/(:any)']['get'] = 'record/applicants_ts_specific/$1/$2';
$route['internal/record/ts/specific/reviews/(:any)/(:any)']['get'] = 'record/applicants_ts_specific_reviews/$1/$2';


/* Read Document */
$route['uploads/docs/(:any)/(:any)/(:any)'] = 'main/view_document/$1/$2/$3';

/* Profile */
$route['internal/profile/reports']['post'] = 'system/report_create';

/* Computations */
$route['internal/record/dtr/create']['post'] = 'system/create_dtr';
$route['internal/record/payroll/get/(:any)/(:any)']['get'] = 'system/payroll_record/$1/$2';

/* Logs */
$route['internal/emails/(:any)']['get'] = 'logs/list_email_records/$1';
$route['internal/logs/(:any)']['get'] = 'logs/list_logs_record/$1';
$route['internal/application/logs/(:any)']['get'] = 'logs/list_applicants_record/$1';
$route['internal/activity/logs/(:any)']['get'] = 'logs/activity_record/$1';
$route['internal/exams/logs/(:any)']['get'] = 'logs/exam_logs_record/$1';

/* Mail */

$route['internal/exams/email']['post'] = 'logs/exams_email';

/* Training */
$route['internal/record/training/(:any)']['get'] = 'training/list_employee/$1'; // summation for all
$route['internal/record/training/(:any)/(:num)']['get'] = 'training/list_employee_status/$1/$2';
$route['internal/record/employee/specific/(:any)']['get'] = 'training/employee_specific/$1';



/** Accounts **/
$route['internal/account/create']['post'] = 'accounts/create';
$route['internal/account/show/(:num)/(:num)']['get'] = 'accounts/user/$1/$2';
$route['internal/account/update']['post'] = 'accounts/user_rate';
$route['internal/account/user/update']['patch'] = 'accounts/update_user';
$route['internal/account/user/update/credentials/(:num)']['patch'] = 'accounts/update_user_password/$1';
$route['internal/account/token/update']['patch'] = 'accounts/register_token';
$route['internal/user/(:any)']['get'] = 'accounts/user/$1';
$route['internal/user/update/(:num)']['patch'] = 'accounts/user_update/$1';
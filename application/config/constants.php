<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


define('SITE_NAME', '7 Star');
define('MAIN_SITE', 'https://7star.com.ph');
define('VERSION', '1');

if ($_SERVER['HTTP_HOST'] == "localhost") {
    // define('DEFAULT_URI','http://api.sparkles.com.ph/');
    define('DEFAULT_URI', 'http://localhost/webservice-hr-api/');
} else {
    define('DEFAULT_URI', 'http://apex.sparkles.com.ph/');
    // define('DEFAULT_URI','http://staging.api.sparkles.com.ph/');

}
/* Default Settings */
define('EMAIL_ADMIN', 'system@sparkles.com.ph');
define('EMAIL_FROM', 'system@api.sparkles.com.ph');
define('EMAIL_HOST', 'mail.api.sparkles.com.ph');
define('EMAIL_PORT', '465');
define('EMAIL_USERNAME', EMAIL_FROM);
define('EMAIL_PASSWORD', 'devteam2020');

/* Email Subjects */
define('EMAIL_NEW_APPLICANT', 'Account Credentials');
define('EMAIL_FORGOT_PASSWORD', 'Forgot Password');


/* Send Grid */

define('EMAIL_SG_ENDPOINT', 'https://api.sendgrid.com/v3/mail/send');
define('EMAIL_SG_TOKEN', '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');

/* SMSON */

define('SMS_KEY', '23JFgZwUnzZkkxtOHmPZ8TxeDQJ');
define('SMS_SECRET', 'ga7oMAoBgtGe51PF4PWjvrxBmrpGI8CH4Fx5dAsD');

/* Send Grid - Templates */
define('EMAIL_SGTEMPLATE_NEW_ACC', 'd-fe50c5b5224042f1b1b0638b8ff21b08');
define('EMAIL_SGTEMPLATE_NEW_EMPLOYEE', 'd-7c8ed25584454e49a362ea84f1b1bbc9');
define('EMAIL_SGTEMPLATE_EXAMRETAKE', 'd-0a238c782cbd4721bef0973f260288db');


define('EMAIL_SGTEMPLATE_FORGOTPASSWORD', 'd-5c0682d342434f419e2fb878eac1f37d');

define('PORTAL_LINK', 'http://portal.sparkles.com.ph/');

define('AWS_LAMBDA_UPLOAD', 'https://vubo3l0xb9.execute-api.us-east-2.amazonaws.com/upload-api');
define('AWS_S3_URL', 'https://oheast2-upload-s3.s3.us-east-2.amazonaws.com/');


define("MEMBER_URL", "https://member-staging.netlify.app");
define("MEMBER_URL_SYZYGY", "https://member.syzygy.com.ph");
define("MEMBER_URL_7STAR", "https://member.7star.com.ph");

define("STAFF_URL", "https://staff-staging.netlify.app");
define("STAFF_URL_SYZYGY", "https://staff.syzygy.com.ph");
define("STAFF_URL_7STAR", "https://staff.7star.com.ph");

define("WORKPLACE_URL", "https://workplace-staging.netlify.app");
define("WORKPLACE_URL_SYZYGY", "https://workplace.syzygy.com.ph");
define("WORKPLACE_URL_7STAR", "https://workplace.7star.com.ph");


define("AWS_PROFILE_URI", "https://oheast2-upload-s3.s3.us-east-2.amazonaws.com/");

define("CURRENT_YEAR", 2022);

// define('PORTAL_LINK','http://localhost:3000/webservice-hr-portal/');
// define('PORTAL_LINK','https://portal-sparkles.netlify.app/');
// define('PORTAL_LINK','http://localhost:3000/webservice-hr-portal/');
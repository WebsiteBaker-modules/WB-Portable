<?php
/**
 *
 * @category        frontend
 * @package         account
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: signup2.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/account/signup2.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */
// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { die("Cannot access this file directly"); }

// Create new frontend object
if (!isset($wb) || !($wb instanceof frontend)) {
    if( !class_exists('wb', false) ){ require(WB_PATH."/framework/class.wb.php"); }
    $wb = new frontend();
}

/* 
if (!$wb->checkFTAN())
{
    $sInfo = strtoupper(basename(__DIR__).'_'.basename(__FILE__, '.'.PAGE_EXTENSION)).'::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $error[] =  $sDEBUG.$MESSAGE['GENERIC_SECURITY_ACCESS']."\n";
    return;
}
*/

// Get details entered
$groups_id = FRONTEND_SIGNUP;
$active = 1;
$username = strtolower(strip_tags($wb->get_post('username')));
$display_name = strip_tags($wb->get_post('display_name'));
$email = $wb->get_post('email');
/*
// Check values
if($groups_id == "") {
    $wb->print_error($MESSAGE['USERS_NO_GROUP'], $js_back, false);
}
*/

// Check if username already exists
$sql = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` WHERE `username` = \''.$username.'\'';
if ($database->get_one($sql)) {
    $error[] = $MESSAGE['USERS_USERNAME_TAKEN']."\n";
}
if(!preg_match('/^[a-z]{1}[a-z0-9_-]{2,}$/i', $username)) {
    $error[] =  $MESSAGE['USERS_NAME_INVALID_CHARS']."\n";
}
$sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'users` ';
$sql .= 'WHERE  `display_name` LIKE \''.$display_name.'\'';
if ($database->get_one($sql) > 0) {
    $error[] = $MESSAGE['USERS_DISPLAYNAME_TAKEN'].'';
} 
if($email != "") {
    if($wb->validate_email($email) == false) {
        $error[] = $MESSAGE['USERS_INVALID_EMAIL']."\n";
    }
} else {
    $error[] = $MESSAGE['SIGNUP_NO_EMAIL']."\n";
}

$email = $database->escapeString($email);
$search = array('{SERVER_EMAIL}');
$replace = array( SERVER_EMAIL);
// Captcha
if(ENABLED_CAPTCHA) {
    $MESSAGE['MOD_FORM_INCORRECT_CAPTCHA'] = str_replace($search,$replace,$MESSAGE['MOD_FORM_INCORRECT_CAPTCHA']);
    if(isset($_POST['captcha']) AND $_POST['captcha'] != ''){
        // Check for a mismatch
        if(!isset($_POST['captcha']) OR !isset($_SESSION['captcha']) OR $_POST['captcha'] != $_SESSION['captcha']) {
            $error[] = $MESSAGE['MOD_FORM_INCORRECT_CAPTCHA']."\n";
        }
    } else {
        $error[] = $MESSAGE['MOD_FORM_INCORRECT_CAPTCHA']."\n";
    }
}
if(isset($_SESSION['captcha'])) { unset($_SESSION['captcha']); }

// Generate a random password then update the database with it
$new_pass = '';
$salt = "abchefghjkmnpqrstuvwxyz0123456789";
srand((double)microtime()*1000000);
$i = 0;
while ($i <= 7) {
    $num = rand() % 33;
    $tmp = substr($salt, $num, 1);
    $new_pass = $new_pass . $tmp;
    $i++;
}
$md5_password = md5($new_pass);
// Check if the email already exists
$sql = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` WHERE `email` = \''.$database->escapeString($email).'\'';
if ($database->get_one($sql)) {
    if(isset($MESSAGE['USERS_EMAIL_TAKEN'])) {
        $error[] = $MESSAGE['USERS_EMAIL_TAKEN']."\n";
    } else {
        $error[] = $MESSAGE['USERS_INVALID_EMAIL']."\n";
    }
}

if(sizeof($error)==0){
    // MD5 supplied password
    $md5_password = md5($new_pass);
    // Insert the user into the database
    $sql  = 'INSERT INTO `'.TABLE_PREFIX.'users` SET '
          . '`group_id` = '.$database->escapeString($groups_id).', '
          . '`groups_id` = \''.$database->escapeString($groups_id).'\', '
          . '`active` = '.$database->escapeString($active).', '
          . '`username` = \''.$database->escapeString($username).'\', '
          . '`password` = \''.$database->escapeString($md5_password).'\', '
          . '`display_name` = \''.$database->escapeString($display_name).'\', '
          . '`home_folder` = \'\', '
          . '`email` = \''.$database->escapeString($email).'\', '
          . '`timezone` = \''.$database->escapeString(DEFAULT_TIMEZONE).'\', '
          . '`language` = \''.$database->escapeString(DEFAULT_LANGUAGE).'\''
          .'';
    $database->query($sql);
    if($database->is_error()) {
        // Error updating database
        $message = $database->get_error();
    } else {
        // Setup email to send
        $mail_to = $email;
        $mail_subject = $MESSAGE['SIGNUP2_SUBJECT_LOGIN_INFO'];

        // Replace placeholders from language variable with values
        $search = array('{LOGIN_DISPLAY_NAME}', '{LOGIN_WEBSITE_TITLE}', '{LOGIN_NAME}', '{LOGIN_PASSWORD}');
        $replace = array($display_name, WEBSITE_TITLE, $username, $new_pass); 
        $mail_message = str_replace($search, $replace, $MESSAGE['SIGNUP2_BODY_LOGIN_INFO']);

        // Try sending the email
        if($wb->mail(SERVER_EMAIL,$mail_to,$mail_subject,$mail_message)) {
            $display_form = false;
            $success[] = $MESSAGE['FORGOT_PASS_PASSWORD_RESET'];
        } else {
            $database->query("DELETE FROM `".TABLE_PREFIX."users` WHERE `username` = '$username'");
            $error[] = $MESSAGE['FORGOT_PASS_CANNOT_EMAIL']."\n";
        }
    }
}

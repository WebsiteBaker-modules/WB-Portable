<?php
/**
 *
 * @category        admin
 * @package         users
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/users/save.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

// Print admin header
require( dirname(dirname((__DIR__))).'/config.php' );
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// suppress to print the header, so no new FTAN will be set
$admin = new admin('Access', 'users_modify', false);

// Create a javascript back link
$js_back = ADMIN_URL.'/users/index.php';

if( !$admin->checkFTAN() )
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back );
}
// After check print the header
$admin->print_header();

$aInputs = array();

$aInputs = array_merge( $_POST );

// Check if user id is a valid number and doesnt equal 1
if(!isset($aInputs['user_id']) OR !is_numeric($aInputs['user_id']) OR $aInputs['user_id'] == 1) {
    header("Location: index.php");
    exit(0);
} else {
    $user_id = intval($aInputs['user_id']);
}

// Gather details entered
$groups_id = ( isset($aInputs['groups']) ? implode(",", $aInputs['groups']) : '');
$active = intval( is_array($aInputs['active'])  ?($aInputs['active'][0]):$aInputs['active']);

$username_fieldname = $admin->get_post_escaped('username_fieldname');
$username = strtolower($admin->get_post_escaped($username_fieldname));

$password = $admin->get_post('password');
$password2 = $admin->get_post('password2');
$display_name = $admin->get_post_escaped('display_name');
$email = $admin->get_post_escaped('email');
$home_folder = $admin->get_post_escaped('home_folder');
// Check values
if($groups_id == "") {
    $admin->print_error($MESSAGE['USERS_NO_GROUP'], $js_back);
}

if(!preg_match('/^[a-z]{1}[a-z0-9_-]{2,}$/i', $username)) {
    $admin->print_error( $MESSAGE['USERS_NAME_INVALID_CHARS'], $js_back);
}

if($password != "") {
    if(strlen($password) < 2) {
        $admin->print_error($MESSAGE['USERS_PASSWORD_TOO_SHORT'], $js_back);
    }
    if($password != $password2) {
        $admin->print_error($MESSAGE['USERS_PASSWORD_MISMATCH'], $js_back);
    }
}
    $md5_password =  md5($password);

if($email != "")
{
    if($admin->validate_email($email) == false)
    {
        $admin->print_error($MESSAGE['USERS']['INVALID_EMAIL'], $js_back);
    }
} else { // e-mail must be present
    $admin->print_error($MESSAGE['SIGNUP']['NO_EMAIL'], $js_back);
}

// Check if the email already exists
$sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
      . 'WHERE `email` = \''.$email.'\' '
      .   'AND `user_id` <> '.$user_id;

$results = $database->query($sql);
if($results->numRows() > 0)
{
    if(isset($MESSAGE['USERS']['EMAIL_TAKEN']))
    {
        $admin->print_error($MESSAGE['USERS_EMAIL_TAKEN'], $js_back);
    } else {
        $admin->print_error($MESSAGE['USERS_INVALID_EMAIL'], $js_back);
    }
}

// Prevent from renaming user to "admin"
if($username != 'admin') {
    $username_code = ", username = '$username'";
} else {
    $username_code = '';
}

// Update the database

$sql  = 'UPDATE `'.TABLE_PREFIX.'users` SET '
      . '`groups_id` = \''.$groups_id.'\', '
      . '`active` = '.$active.', '
      . ( ($username != 'admin') ? '`username` = \''.$username.'\', ':' ' )
      . '`display_name` = \''.$display_name.'\', '
      . '`home_folder` = \''.$home_folder.'\', '
      . '`email` = \''.$email.'\''
      . ( ($password == "") ? ' ': ', `password` = \''.$md5_password.'\' ' )
      . 'WHERE `user_id` = '.$user_id;

$database->query($sql);
if($database->is_error()) {
    $admin->print_error($database->get_error(),$js_back);
} else {
    $admin->print_success($MESSAGE['USERS']['SAVED']);
}

// Print admin footer
$admin->print_footer();

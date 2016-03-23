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
 * @version         $Id: add.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/users/add.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

// Print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// suppress to print the header, so no new FTAN will be set
$admin = new admin('Access', 'users_add',false);

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

// Get details entered
$groups_id = ( isset($aInputs['groups']) ? implode(",", $aInputs['groups']) : '');
$active = intval( is_array($aInputs['active'])  ?($aInputs['active'][0]):$aInputs['active']);
$username_fieldname = $admin->get_post('username_fieldname');
$username = strtolower($admin->get_post($username_fieldname));
$password = $admin->get_post('password');
$password2 = $admin->get_post('password2');
$display_name = $admin->get_post('display_name');
$email = $admin->get_post('email');
$home_folder = $admin->get_post('home_folder');
$default_language = DEFAULT_LANGUAGE;
$default_timezone = DEFAULT_TIMEZONE;

// Check values
if($groups_id == '') {
    $admin->print_error($MESSAGE['USERS_NO_GROUP'], $js_back);
}
if(!preg_match('/^[a-z]{1}[a-z0-9_-]{2,}$/i', $username)) {
    $admin->print_error( $MESSAGE['USERS_NAME_INVALID_CHARS'].' / '.
                      $MESSAGE['USERS_USERNAME_TOO_SHORT'], $js_back);
}
if(strlen($password) < 2) {
    $admin->print_error($MESSAGE['USERS_PASSWORD_TOO_SHORT'], $js_back);
}
if($password != $password2) {
    $admin->print_error($MESSAGE['USERS_PASSWORD_MISMATCH'], $js_back);
}
if($email != '')
{
    if($admin->validate_email($email) == false)
    {
        $admin->print_error($MESSAGE['USERS_INVALID_EMAIL'], $js_back);
    }
} else { // e-mail must be present
    $admin->print_error($MESSAGE['SIGNUP_NO_EMAIL'], $js_back);
}

// choose group_id from groups_id - workaround for still remaining calls to group_id (to be cleaned-up)
$gid_tmp = explode(',', $groups_id);
if(in_array('1', $gid_tmp)) $group_id = '1'; // if user is in administrator-group, get this group
else $group_id = $gid_tmp[0]; // else just get the first one
unset($gid_tmp);

// Check if username already exists
$sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
      . 'WHERE `username` = \''.$username.'\' ';

$results = $database->query($sql);
if($results->numRows() > 0) {
    $admin->print_error($MESSAGE['USERS_USERNAME_TAKEN'], $js_back);
}

// Check if the email already exists
$sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
      . 'WHERE `email` = \''.$email.'\' ';

$results = $database->query($sql);
if($results->numRows() > 0)
{
    if(isset($MESSAGE['USERS_EMAIL_TAKEN']))
    {
        $admin->print_error($MESSAGE['USERS_EMAIL_TAKEN'], $js_back);
    } else {
        $admin->print_error($MESSAGE['USERS_INVALID_EMAIL'], $js_back);
    }
}

// MD5 supplied password
$md5_password = md5($password);

// Insert the user into the database
$sql = // add the Admin user
     'INSERT INTO `'.TABLE_PREFIX.'users` SET '
    .    '`group_id`='.$database->escapeString($group_id).', '
    .    '`groups_id`=\''.$database->escapeString($groups_id).'\', '
    .    '`active`=\''.$database->escapeString($active).'\', '
    .    '`username`=\''.$database->escapeString($username).'\', '
    .    '`password`=\''.$database->escapeString($md5_password).'\', '
    .    '`remember_key`=\'\', '
    .    '`last_reset`=0, '
    .    '`display_name`=\''.$database->escapeString($display_name).'\', '
    .    '`email`=\''.$database->escapeString($email).'\', '
    .    '`timezone`=\''.$database->escapeString($default_timezone).'\', '
    .    '`date_format`=\'M d Y\', '
    .    '`time_format`=\'g:i A\', '
    .    '`language`=\''.$database->escapeString($default_language).'\', '
    .    '`home_folder`=\''.$database->escapeString($home_folder).'\', '
    .    '`login_when`=\''.time().'\', '
    .    '`login_ip`=\'\' '
    .    '';
$database->query($sql);
if($database->is_error()) {
    $admin->print_error($database->get_error());
} else {
    $admin->print_success($MESSAGE['USERS_ADDED']);
}

// Print admin footer
$admin->print_footer();

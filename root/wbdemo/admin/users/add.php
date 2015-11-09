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
require( dirname(dirname((__dir__))).'/config.php' );
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
/**
 * 
$groups_id = (isset($_POST['groups'])) ? implode(",", $admin->add_slashes($_POST['groups'])) : ''; //should check permissions
$groups_id = trim($groups_id, ','); // there will be an additional ',' when "Please Choose" was selected, too
$active = $admin->add_slashes($_POST['active'][0]);
$username_fieldname = $admin->get_post_escaped('username_fieldname');
$username = strtolower($admin->get_post_escaped($username_fieldname));
$password = $admin->get_post('password');
$password2 = $admin->get_post('password2');
$display_name = $admin->get_post_escaped('display_name');
$email = $admin->get_post_escaped('email');
$home_folder = $admin->get_post_escaped('home_folder');
 */

// Get details entered
$groups_id = ( isset($aInputs['groups']) ? implode(",", $aInputs['groups']) : '');
$active = intval( is_array($aInputs['active'])  ?($aInputs['active'][0]):$aInputs['active']);
$username_fieldname = $admin->get_post_escaped('username_fieldname');
$username = strtolower($admin->get_post_escaped($username_fieldname));
$password = $admin->get_post('password');
$password2 = $admin->get_post('password2');
$display_name = $admin->get_post_escaped('display_name');
$email = $admin->get_post_escaped('email');
$home_folder = $admin->get_post_escaped('home_folder');
$default_language = DEFAULT_LANGUAGE;
$default_timezone = DEFAULT_TIMEZONE;

// Check values
if($groups_id == '') {
    $admin->print_error($MESSAGE['USERS']['NO_GROUP'], $js_back);
}
if(!preg_match('/^[a-z]{1}[a-z0-9_-]{2,}$/i', $username)) {
    $admin->print_error( $MESSAGE['USERS_NAME_INVALID_CHARS'].' / '.
                      $MESSAGE['USERS_USERNAME_TOO_SHORT'], $js_back);
}
if(strlen($password) < 2) {
    $admin->print_error($MESSAGE['USERS']['PASSWORD_TOO_SHORT'], $js_back);
}
if($password != $password2) {
    $admin->print_error($MESSAGE['USERS']['PASSWORD_MISMATCH'], $js_back);
}
if($email != '')
{
    if($admin->validate_email($email) == false)
    {
        $admin->print_error($MESSAGE['USERS']['INVALID_EMAIL'], $js_back);
    }
} else { // e-mail must be present
    $admin->print_error($MESSAGE['SIGNUP']['NO_EMAIL'], $js_back);
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
    $admin->print_error($MESSAGE['USERS']['USERNAME_TAKEN'], $js_back);
}

// Check if the email already exists
$sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
      . 'WHERE `email` = \''.$email.'\' ';

$results = $database->query($sql);
if($results->numRows() > 0)
{
    if(isset($MESSAGE['USERS']['EMAIL_TAKEN']))
    {
        $admin->print_error($MESSAGE['USERS']['EMAIL_TAKEN'], $js_back);
    } else {
        $admin->print_error($MESSAGE['USERS']['INVALID_EMAIL'], $js_back);
    }
}

// MD5 supplied password
$md5_password = md5($password);

// Inser the user into the database

$sql  = 'INSERT INTO `'.TABLE_PREFIX.'users` SET '
      . '`group_id` = '.$group_id.', '
      . '`groups_id` = \''.$groups_id.'\', '
      . '`active` = '.$active.', '
      . '`username` = \''.$username.'\', '
      . '`password` = \''.$md5_password.'\', '
      . '`display_name` = \''.$display_name.'\', '
      . '`home_folder` = \''.$home_folder.'\', '
      . '`email` = \''.$email.'\', '
      . '`timezone` = \''.$default_timezone.'\', '
      . '`language` = \''.$default_language.'\''
      .'';
$database->query($sql);
if($database->is_error()) {
    $admin->print_error($database->get_error());
} else {
    $admin->print_success($MESSAGE['USERS']['ADDED']);
}

// Print admin footer
$admin->print_footer();

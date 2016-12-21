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
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// suppress to print the header, so no new FTAN will be set
$admin = new admin('Access', 'users_modify', false);
$oTrans = Translate::getInstance();
$oTrans->enableAddon('admin\\users');

// Create a javascript back link
$js_back = ADMIN_URL.'/users/index.php';

if( !$admin->checkFTAN() )
{
    $admin->print_header();
    $sInfo = strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION).'::');
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$oTrans->MESSAGE_GENERIC_SECURITY_ACCESS, $js_back );
}

// After check print the header
$admin->print_header();
/*
$sLanguagesAddonDefaultFile = WB_PATH.'/account/languages/EN.php';
if (is_readable($sLanguagesAddonDefaultFile)){include $sLanguagesAddonDefaultFile;}
$sLanguagesAddonFile = WB_PATH.'/account/languages/'.LANGUAGE.'.php';
if (is_readable($sLanguagesAddonFile)){include $sLanguagesAddonFile;}
*/
$aInputs = array();
$aErrorMessage = array();
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

$password = $admin->get_post('password');
$password2 = $admin->get_post('password2');
$display_name = $admin->StripCodeFromText(($admin->get_post('display_name')));
$email = $admin->StripCodeFromText($admin->get_post('email'));
$home_folder = $admin->get_post('home_folder');
// Check values
if($groups_id == "") {
    $aErrorMessage[] = ($oTrans->MESSAGE_USERS_NO_GROUP);
}

if($password != "") {
    if(strlen($password) < 2) {
        $aErrorMessage[] = ($oTrans->MESSAGE_USERS_PASSWORD_TOO_SHORT);
    }
    if($password != $password2) {
        $aErrorMessage[] = ($oTrans->MESSAGE_USERS_PASSWORD_MISMATCH);
    }
}
    $md5_password =  md5($password);

if($email != "")
{
    if($admin->validate_email($email) == false)
    {
        $aErrorMessage[] = ($oTrans->MESSAGE_USERS_INVALID_EMAIL);
    }
} else { // e-mail must be present
    $aErrorMessage[] = ($oTrans->MESSAGE_SIGNUP_NO_EMAIL);
}

// Check if the email already exists
$sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
      . 'WHERE `email` = \''.$email.'\' '
      .   'AND `user_id` <> '.$user_id;
if($database->get_one($sql))
{
    if(isset($oTrans->MESSAGE_USERS_EMAIL_TAKEN))
    {
        $aErrorMessage[] = ($oTrans->MESSAGE_USERS_EMAIL_TAKEN);
    }
}
$sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'users` ';
$sql .= 'WHERE `user_id` <> '.$user_id.' AND `display_name` LIKE \''.$display_name.'\'';
if ($numRow=$database->get_one($sql)) {
   $aErrorMessage[] = ( @$oTrans->MESSAGE_USERS_DISPLAYNAME_TAKEN?:$oTrans->MESSAGE_MEDIA_BLANK_NAME.' ('.$oTrans->TEXT_DISPLAY_NAME.')');
}

if (!sizeof($aErrorMessage)) {
// Update the database
$sql  = 'UPDATE `'.TABLE_PREFIX.'users` SET '
      . '`groups_id` = \''.$database->escapeString($groups_id).'\', '
      . '`active` = '.$database->escapeString($active).', '
      . '`display_name` = \''.$database->escapeString($display_name).'\', '
      . '`home_folder` = \''.$database->escapeString($home_folder).'\', '
      . '`email` = \''.$database->escapeString($email).'\''
      . ( ($password == "") ? ' ': ', `password` = \''.$database->escapeString($md5_password).'\' ' )
      . 'WHERE `user_id` = '.$database->escapeString($user_id);

    if (!$database->query($sql)) {
        if($database->is_error()) {
            $aErrorMessage[] = $database->get_error();
        }
    }
}
if (sizeof($aErrorMessage)) {
    $admin->print_error(implode('<br />', $aErrorMessage), $js_back);
} else {
    $admin->print_success($oTrans->MESSAGE_USERS_SAVED, $js_back);
}
// Print admin footer
$admin->print_footer();

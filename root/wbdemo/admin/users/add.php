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
$aErrorMessage = array();

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
/**

 * $sLanguagesAddonDefaultFile = WB_PATH.'/account/languages/EN.php';
 * if (is_readable($sLanguagesAddonDefaultFile)){include $sLanguagesAddonDefaultFile;}
 * $sLanguagesAddonFile = WB_PATH.'/account/languages/'.LANGUAGE.'.php';
 * if (is_readable($sLanguagesAddonFile)){include $sLanguagesAddonFile;}
 */
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $oTrans ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
$aInputs = array();
$aInputs = array_merge( $_POST );
// Get details entered
$groups_id = ( isset($aInputs['groups']) ? implode(",", $aInputs['groups']) : '');
$active = intval( is_array($aInputs['active'])  ?($aInputs['active'][0]):$aInputs['active']);
$username_fieldname = $admin->get_post('username_fieldname');
$username = strtolower($admin->get_post($username_fieldname));
$password = $admin->get_post('password');
$password2 = $admin->get_post('password2');
$display_name = $admin->StripCodeFromText($admin->get_post('display_name'));
$email = $admin->StripCodeFromText($admin->get_post('email'));
$home_folder = $admin->get_post('home_folder');
$default_language = DEFAULT_LANGUAGE;
$default_timezone = DEFAULT_TIMEZONE;
/*----------------------------------------------------------------------------------------------------*/
    // Check values
    // Check if username already exists
    $sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
          . 'WHERE `username` = \''.$username.'\' ';
    if ($database->get_one($sql)) {
        $aErrorMessage[] = $oTrans->MESSAGE_USERS_USERNAME_TAKEN;
    }
    if(!preg_match('/^[a-z]{1}[a-z0-9_-]{2,}$/i', $username)) {
        $aErrorMessage[] = $oTrans->MESSAGE_USERS_NAME_INVALID_CHARS.' / '
                         . $oTrans->MESSAGE_USERS_USERNAME_TOO_SHORT;
    }
    if(strlen($password) < 2) {
        $aErrorMessage[] = $oTrans->MESSAGE_USERS_PASSWORD_TOO_SHORT;
    }
    if($password != $password2) {
        $aErrorMessage[] = $oTrans->MESSAGE_USERS_PASSWORD_MISMATCH;
    }
    $sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'users` ';
    $sql .= 'WHERE  `display_name` LIKE \''.$display_name.'\'';
    if ($database->get_one($sql) > 0) {
        $aErrorMessage[] = ( @$oTrans->MESSAGE_USERS_DISPLAYNAME_TAKEN?:$oTrans->MESSAGE_MEDIA_BLANK_NAME.' ('.$oTrans->TEXT_DISPLAY_NAME.')');
    }
    if($email != '')
    {
        // Check if the email already exists
        $sql  = 'SELECT `user_id` FROM `'.TABLE_PREFIX.'users` '
              . 'WHERE `email` = \''.$email.'\' ';
        if ($database->get_one($sql))
        {
            if(isset($oTrans->MESSAGE_USERS_EMAIL_TAKEN))
            {
                $aErrorMessage[] = $oTrans->MESSAGE_USERS_EMAIL_TAKEN;
            }
            if($admin->validate_email($email) == false)
            {
                $aErrorMessage[] = $oTrans->MESSAGE_USERS_INVALID_EMAIL;
            }
        }
    } else { // e-mail must be present
        $aErrorMessage[] = $oTrans->MESSAGE_SIGNUP_NO_EMAIL;
    }
    if($groups_id == '') {
        $aErrorMessage[] = $oTrans->MESSAGE_USERS_NO_GROUP;
    }
/*----------------------------------------------------------------------------------------------------*/
// choose group_id from groups_id - workaround for still remaining calls to group_id (to be cleaned-up)
$gid_tmp = explode(',', $groups_id);
if(in_array('1', $gid_tmp)) $group_id = '1'; // if user is in administrator-group, get this group
else $group_id = $gid_tmp[0]; // else just get the first one
unset($gid_tmp);

if (!sizeof($aErrorMessage)) {
// MD5 supplied password
$md5_password = md5($password);
$now = time();
// Insert the user into the database
$sql = // add the Admin user
     'INSERT INTO `'.TABLE_PREFIX.'users` SET '
    .    '`group_id`='.intval($group_id).', '
    .    '`groups_id`=\''.$database->escapeString($groups_id).'\', '
    .    '`active`=\''.$database->escapeString($active).'\', '
    .    '`username`=\''.$database->escapeString($username).'\', '
    .    '`password`=\''.$database->escapeString($md5_password).'\', '
    .    '`remember_key`=\'\', '
    .    '`last_reset`=0, '
    .    '`display_name`=\''.$database->escapeString($display_name).'\', '
    .    '`email`=\''.$database->escapeString($email).'\', '
    .    '`timezone`=\''.$database->escapeString($default_timezone).'\', '
    .    '`date_format`=\''.DEFAULT_DATE_FORMAT.'\', '
    .    '`time_format`=\''.DEFAULT_TIME_FORMAT.'\', '
    .    '`language`=\''.$database->escapeString($default_language).'\', '
    .    '`home_folder`=\''.$database->escapeString($home_folder).'\', '
    .    '`login_when`=\''.time().'\', '
    .    '`login_ip`=\'\' '
    .    '';
    if (!$database->query($sql)) {
        if($database->is_error()) {
            $aErrorMessage[] = $database->get_error();
        }
    }
}
if (sizeof($aErrorMessage)) {
    $admin->print_error(implode('<br />', $aErrorMessage), $js_back);
} else {
    $admin->print_success($oTrans->MESSAGE_USERS_ADDED, $js_back);
}
// Print admin footer
$admin->print_footer();

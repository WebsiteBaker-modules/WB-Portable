<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 * @description
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }

$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;
if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $sBacklink);
}
$admin->print_header();

if (!function_exists('emailAdmin')) {
    function emailAdmin() {
        global $database,$admin;
        $retval = $admin->get_email();
        if($admin->get_user_id()!='1') {
            $sql  = 'SELECT `email` FROM `'.TABLE_PREFIX.'users` '
                  . 'WHERE `user_id`=\'1\' ';
            $retval = $database->get_one($sql);
        }
        return $retval;
    }
}

// load module language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}

// This code removes any <?php tags and adds slashes
$friendly = array('&lt;', '&gt;', '?php');
$raw = array('<', '>', '');

//$header     = CleanInput('header');
$header = $admin->StripCodeFromText($admin->get_post('header'),true);
//$field_loop = CleanInput('field_loop');
$field_loop = $admin->StripCodeFromText($admin->get_post('field_loop'),true);
$footer = $admin->StripCodeFromText($admin->get_post('footer'),true);
//$email_to   = CleanInput('email_to');
$email_to   = $admin->StripCodeFromText($admin->get_post('email_to'), true);
$email_to   = $admin->StripCodeFromText($email_to != '' ? $email_to : emailAdmin());
$email_from = SERVER_EMAIL;
//$use_captcha =CleanInput('use_captcha');
$use_captcha = $admin->StripCodeFromText($admin->get_post('use_captcha'),true);

if( isset($_POST['email_fromname_field']) && ($_POST['email_fromname_field'] != '')) {
    $email_fromname = $admin->StripCodeFromText($admin->get_post('email_fromname_field'),true);
} else {
    $email_fromname = $admin->StripCodeFromText($admin->get_post('email_fromname'),true);
}

$email_fromname = ($email_fromname != '' ? $email_fromname : WBMAILER_DEFAULT_SENDERNAME);
$email_subject = ($admin->StripCodeFromText($admin->get_post('email_subject'),true));
$success_page = ($admin->StripCodeFromText($admin->get_post('success_page'),true));
$success_email_to = ($admin->StripCodeFromText($admin->get_post('success_email_to'),true));
$success_email_from = (SERVER_EMAIL);
$success_email_fromname = ($admin->StripCodeFromText($admin->get_post('success_email_fromname'),true));
$success_email_fromname = ($success_email_fromname != '' ? $success_email_fromname : $email_fromname);
$success_email_text = ($admin->StripCodeFromText($admin->get_post('success_email_text'),true));
$success_email_text = (($success_email_text != '') ? $success_email_text : '');
$success_email_subject = ($admin->StripCodeFromText($admin->get_post('success_email_subject'),true));
$success_email_subject = (($success_email_subject  != '') ? $success_email_subject : '');

if(!is_numeric($_POST['max_submissions'])) {
    $max_submissions = 50;
} else {
    $max_submissions = intval($_POST['max_submissions']);
}
if(!is_numeric($_POST['stored_submissions'])) {
    $stored_submissions = 100;
} else {
    $stored_submissions = intval($_POST['stored_submissions']);
}
if(!is_numeric($_POST['perpage_submissions'])) {
    $perpage_submissions = 10;
} else {
    $perpage_submissions = intval($_POST['perpage_submissions']);
}

// Make sure max submissions is not greater than stored submissions if stored_submissions <>0
if($max_submissions > $stored_submissions) {
    $max_submissions = $stored_submissions;
}
$sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );

$sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id;

// Update settings
$sql  = 'UPDATE `'.TABLE_PREFIX.'mod_form_settings` SET ' 
      . '`header` = \''.$database->escapeString($header).'\', '
      . '`field_loop` = \''.$database->escapeString($field_loop).'\', '
      . '`footer` = \''.$database->escapeString($footer).'\', '
      . '`email_to` = \''.$database->escapeString($email_to).'\', '
      . '`email_from` = \''.$database->escapeString($email_from).'\', '
      . '`email_fromname` = \''.$database->escapeString($email_fromname).'\', '
      . '`email_subject` = \''.$database->escapeString($email_subject).'\', '
      . '`success_page` = '.(int)$success_page.', '
      . '`success_email_to` = \''.$database->escapeString($success_email_to).'\', '
      . '`success_email_from` = \''.$database->escapeString($success_email_from).'\', '
      . '`success_email_fromname` = \''.$database->escapeString($success_email_fromname).'\', '
      . '`success_email_text` = \''.$database->escapeString($success_email_text).'\', '
      . '`success_email_subject` = \''.$database->escapeString($success_email_subject).'\', '
      . '`max_submissions` = \''.$database->escapeString($max_submissions).'\', '
      . '`stored_submissions` = \''.$database->escapeString($stored_submissions).'\', '
      . '`perpage_submissions` = \''.$database->escapeString($perpage_submissions).'\', '
      . '`use_captcha` = \''.$database->escapeString($use_captcha).'\' '
      . 'WHERE `section_id` = '.(int)$section_id.' ';

if($database->query($sql)) {

    $admin->print_success($TEXT['SUCCESS'], $sBacklink);
}
// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(), $sBacklink);
}
// Print admin footer
$admin->print_footer();

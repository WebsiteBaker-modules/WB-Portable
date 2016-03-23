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

$print_info_banner = true;
// Tells script to update when this page was last updated
$update_when_modified = false;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$sModulName = basename(__DIR__);
$sModulName = $sModulName;
$ModuleRel = '/modules/'.basename(__DIR__).'/';
$ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';

// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
include_once(WB_PATH .'/framework/module.functions.php');

// load module language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}


$sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );

$sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;

if (!$admin->checkFTAN())
{
//    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $sBacklink);
}

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

// Get Settings from DB $aSettings['
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_form_settings` '
      . 'WHERE `section_id` = '.(int)$section_id.'';
if($oSetting = $database->query($sql)) {
    $aSettings = $oSetting->fetchRow(MYSQLI_ASSOC);
    $aSettings['email_to'] = ( ($aSettings['email_to'] != '') ? $aSettings['email_to'] : emailAdmin());
    $aSettings['email_subject'] = ( ($aSettings['email_subject']  != '') ? $aSettings['email_subject'] : '' );
    $aSettings['success_email_subject'] = ($aSettings['success_email_subject']  != '') ? $aSettings['success_email_subject'] : '';
    $aSettings['success_email_from'] = $admin->add_slashes(SERVER_EMAIL);
    $aSettings['success_email_fromname'] = ($aSettings['success_email_fromname'] != '' ? $aSettings['success_email_fromname'] : WBMAILER_DEFAULT_SENDERNAME);
    $aSettings['success_email_subject'] = ($aSettings['success_email_subject']  != '') ? $aSettings['success_email_subject'] : '';
}

// Set raw html <'s and >'s to be replace by friendly html code
$raw = array('<', '>');
$friendly = array('&lt;', '&gt;');
/*
// check if backend.css file needs to be included into the <body></body> of modify.php
if(!method_exists($admin, 'register_backend_modfiles') && file_exists(WB_PATH ."/modules/form/backend.css")) {
    echo '<style type="text/css">';
    include(WB_PATH .'/modules/form/backend.css');
    echo "\n</style>\n";
}
*/
?>
<h2><?php echo $MOD_FORM['SETTINGS']; ?></h2>
<?php
// include the button to edit the optional module CSS files
// Note: CSS styles for the button are defined in backend.css (div class="mod_moduledirectory_edit_css")
// Place this call outside of any <form></form> construct!!!
if(function_exists('edit_module_css')) {
    edit_module_css('form');
}
?><form name="edit" action="<?php echo $ModuleUrl; ?>/save_settings.php" method="post" style="margin: 0;">
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="success_email_to" value="" />
<?php echo $admin->getFTAN(); ?>
<table  class="frm-table">
    <thead>
    <tr>
        <th colspan="2"><h3><?php echo $HEADING['GENERAL_SETTINGS']; ?></h3></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['CAPTCHA_VERIFICATION']; ?>:</td>
        <td>
            <input type="radio" name="use_captcha" id="use_captcha_true" value="1"<?php if($aSettings['use_captcha'] == true) { echo ' checked="checked"'; } ?> />
            <label for="use_captcha_true"><?php echo $TEXT['ENABLED']; ?></label>
            <input type="radio" name="use_captcha" id="use_captcha_false" value="0"<?php if($aSettings['use_captcha'] == false) { echo ' checked="checked"'; } ?> />
            <label for="use_captcha_false"><?php echo $TEXT['DISABLED']; ?></label>
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['MAX_SUBMISSIONS_PER_HOUR']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="max_submissions" style="width: 30px;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['max_submissions'])); ?>" />
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['SUBMISSIONS_STORED_IN_DATABASE']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="stored_submissions" style="width: 30px;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['stored_submissions'])); ?>" />
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['SUBMISSIONS_PERPAGE']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="perpage_submissions" style="width: 30px;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['perpage_submissions'])); ?>" />
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['HEADER']; ?>:</td>
        <td class="frm-setting_value">
            <textarea name="header" cols="80" rows="6" style="width: 98%; height: 80px;"><?php echo ($aSettings['header']); ?></textarea>
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['FIELD'].' '.$TEXT['LOOP']; ?>:</td>
        <td class="frm-setting_value">
            <textarea name="field_loop" cols="80" rows="6" style="width: 98%; height: 80px;"><?php echo ($aSettings['field_loop']); ?></textarea>
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['FOOTER']; ?>:</td>
        <td class="frm-setting_value">
            <textarea name="footer" cols="80" rows="6" style="width: 98%; height: 80px;"><?php echo str_replace($raw, $friendly, ($aSettings['footer'])); ?></textarea>
        </td>
    </tr>
    </tbody>
</table>
<!-- E-Mail Optionen -->
<table title="<?php echo $TEXT['EMAIL'].' '.$TEXT['SETTINGS']; ?>"  class="frm-table" style="margin-top: 3px;">
    <thead>
    <tr>
        <th colspan="2" ><h3><?php echo $TEXT['EMAIL'].' '.$TEXT['SETTINGS']; ?></h3></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['EMAIL'].' '.$MOD_FORM['TO']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="email_to" style="width: 98%;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['email_to'])); ?>" />
        </td>
    </tr>

    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['DISPLAY_NAME']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="email_fromname" id="email_fromname" style="width: 98%;" maxlength="255" value="<?php  echo $aSettings['email_fromname'];  ?>" />
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['EMAIL'].' '.$TEXT['SUBJECT']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="email_subject" style="width: 98%;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['email_subject'])); ?>" />
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    </tbody>
</table>
<!-- Erfolgreich Optionen -->
<table title="<?php echo $TEXT['EMAIL'].' '.$MOD_FORM['CONFIRM']; ?>"  class="frm-table "  style="margin-top: 3px;">
    <thead>
    <tr>
        <th colspan="2"><h3 class=""><?php echo $TEXT['EMAIL'].' '.$MOD_FORM['CONFIRM']; ?></h3></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['EMAIL'].' '.$MOD_FORM['TO']; ?>:</td>
        <td class="frm-setting_value "><p class="frm-warning"><?php echo  $MOD_FORM['RECIPIENT'] ?><br /><?php echo $MOD_FORM['SPAM']; ?> </p>   </td>
    </tr>
    <tr>
        <td colspan="2"><p class=""></p></td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $MOD_FORM['REPLYTO']; ?>:</td>
        <td class="frm-setting_value">
            <select name="success_email_to" style="width: 98%;">
            <option value="" onclick="javascript: document.getElementById('success_email_to').style.display = 'block';"><?php echo $TEXT['NONE']; ?></option>
<?php
            $success_email_to = str_replace($raw, $friendly, ($aSettings['success_email_to']));
            $sql  = 'SELECT `field_id`, `title` FROM `'.TABLE_PREFIX.'mod_form_fields` '
                  . 'WHERE `section_id` = '.(int)$section_id.' '
                  . '  AND  `type` = \'email\' '
                  . 'ORDER BY `position` ASC ';
            if($query_email_fields = $database->query($sql)) {
                if($query_email_fields->numRows() > 0) {
                    while($field = $query_email_fields->fetchRow(MYSQL_ASSOC)) {
?>
                        <option value="field<?php echo $field['field_id']; ?>"<?php if($success_email_to == 'field'.$field['field_id']) { echo ' selected'; $selected = true; } ?> onclick="javascript: document.getElementById('email_from').style.display = 'none';">
                            <?php echo $TEXT['FIELD'].': '.$field['title']; ?>
                        </option>
<?php
                    }
                }
            }
?>
            </select>
        </td>
    </tr>
    <tr>

        <td class="frm-setting_name"><?php echo $TEXT['DISPLAY_NAME']; ?>:</td>
        <td class="frm-setting_value">
            <?php $aSettings['success_email_fromname'] = ($aSettings['success_email_fromname'] != '' ? $aSettings['success_email_fromname'] : WBMAILER_DEFAULT_SENDERNAME); ?>
            <input type="text" name="success_email_fromname" style="width: 98%;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['success_email_fromname'])); ?>" />
        </td>
    </tr>

    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['EMAIL'].' '.$TEXT['SUBJECT']; ?>:</td>
        <td class="frm-setting_value">
            <input type="text" name="success_email_subject" style="width: 98%;" maxlength="255" value="<?php echo str_replace($raw, $friendly, ($aSettings['success_email_subject'])); ?>" />
        </td>
    </tr>
    <tr>
        <td class="frm-setting_name"><?php echo $TEXT['EMAIL'].' '.$TEXT['TEXT']; ?>:</td>
        <td class="frm-setting_value">
            <textarea name="success_email_text" cols="80" rows="1" style="width: 98%; height: 80px;"><?php echo str_replace($raw, $friendly, ($aSettings['success_email_text'])); ?></textarea>
        </td>
    </tr>
    <tr><td> </td></tr>
    <tr>
        <td class="frm-newsection"><?php echo $TEXT['SUCCESS'].' '.$TEXT['PAGE']; ?>:</td>
        <td class="frm-newsection">
<?php
            // Get exisiting pages and show the pagenames
            $aSelectPages = array();
            $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'pages`  '
                  . 'WHERE `visibility` <> \'deleted\' ';
            $old_page_id = $page_id;
            $query = $database->query($sql);
            while($mail_page = $query->fetchRow(MYSQL_ASSOC)) {
                if(!$admin->page_is_visible($mail_page)) { continue; }
                $page_id = $mail_page['page_id'];
                $success_page = $aSettings['success_page'];
              //    echo $success_page.':'.$aSettings['success_page'].':'; not vailde
                $aSelectPages[$page_id]['menu_title'] = $mail_page['menu_title'];
                $aSelectPages[$page_id]['success_page'] = $mail_page['page_id'];
                $aSelectPages[$page_id]['selected'] = ( ($success_page == $page_id)? ' selected="selected"':'');
             }

?>
            <select name="success_page">
            <option value=""><?php echo $TEXT['NONE']; ?></option>
            <?php
                foreach( $aSelectPages as $key=> $aValues ) {
                echo '<option value="'.$aValues['success_page'].'"'.$aValues['selected'].'>'.$aValues['menu_title'].'</option>';
                }
            ?>
            </select>
        </td>
    </tr>
    </tbody>
</table>

<table  class="frm-table">
    <tr>
        <td>
            <input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin-top: 5px;">
        </td>
        <td>
            <input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript:window.location='<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $old_page_id.'#'.$sSectionIdPrefix.$section_id; ?>';" style="width: 100px; margin-top: 5px;" />
        </td>
    </tr>
</table>
</form>
<?php

// Print admin footer
$admin->print_footer();

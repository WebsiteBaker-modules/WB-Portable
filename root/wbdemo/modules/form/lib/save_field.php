<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save_field.php 1919 2013-06-07 04:21:49Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/save_field.php $
 * @lastmodified    $Date: 2013-06-07 06:21:49 +0200 (Fr, 07. Jun 2013) $
 * @description
 */
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
// suppress to print the header, so no new FTAN will be set
$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
$sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );
$backUrl = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id;
// check FTAN
if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error( ''.$MESSAGE['GENERIC_SECURITY_ACCESS'], $backUrl );
}
// Get id
$field_id = intval($admin->checkIDKEY('field_id', false ));
if (!$field_id) {
    $admin->print_header();
    $admin->print_error( ''.$MESSAGE['GENERIC_SECURITY_ACCESS'].'', $backUrl );
}
$backModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id);
// After check print the header to get a new FTAN
$admin->print_header();
// Validate all fields
if( ($admin->get_post('title') == '') || ($admin->get_post('type') == '') ) {
    $admin->print_error($MESSAGE['GENERIC_FILL_IN_ALL'], $backModuleUrl );
} else {
    $title = $admin->StripCodeFromText(($admin->get_post('title')));
    $type = ($admin->get_post('type'));
    $required = (int) ($admin->get_post('required'));
}

// Update row
$sql  = 'UPDATE `'.TABLE_PREFIX.'mod_form_fields SET` '
      . 'title = \''.$database->escapeString($title).'\', '
      . 'type = \''.$database->escapeString($type).'\', '
      . 'required = \''.$database->escapeString($required).'\' '
      . 'WHERE field_id = '.(int)$field_id.' ';
if($database->query($sql)) { }

// If field type has multiple options, get all values and implode them
    $value = $extra = '';
    $list_count = $admin->get_post('list_count');
    if(is_numeric($list_count)) {
        $values = array();
        for($i = 1; $i <= $list_count; $i++) {
            if($admin->get_post('value'.$i) != '') {
                $values[] = str_replace(",","&#44;",$admin->get_post('value'.$i));
            }
        }
        $value = implode(',', $values);
    } else {
        $admin->print_error( ''.$MESSAGE['GENERIC_SECURITY_ACCESS'].'', $backUrl );
    }
/**
 * 
// Get extra fields for field-type-specific settings
if($admin->get_post('type') == 'textfield') {
    $extra = intval($admin->get_post('length'));
    $value = $admin->StripCodeFromText( $admin->get_post('value'));
} elseif($admin->get_post('type') == 'textarea') {
    $value = $admin->StripCodeFromText( $admin->get_post('value'));
    $extra = '';
//    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'heading') {
    $extra = $admin->StripCodeFromText( $admin->get_post('template'));
    if(trim($extra) == '') $extra = '<tr><td class="frm-field_heading" colspan="2">{TITLE}{FIELD}</td></tr>';
//    $extra = $admin->add_slashes($extra);
    $value = '';
//    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '', extra = '$extra' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'select') {
    $extra = intval($admin->get_post('size')).','.$admin->get_post('multiselect');
//    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'checkbox') {
    $extra = $admin->StripCodeFromText( $admin->get_post('seperator'));
//    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'radio') {
    $extra = $admin->StripCodeFromText( $admin->get_post('seperator'));
//    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
}
 */

// prepare sql-update
    switch($admin->get_post('type')):
        case 'textfield':
            $value = $admin->StripCodeFromText($admin->get_post('value'));
            $extra = intval($admin->get_post('length'));
            break;
        case 'textarea':
            $value = $admin->StripCodeFromText($admin->get_post('value'));
            $extra = '';
            break;
        case 'heading':
            $extra = $admin->StripCodeFromText( $admin->get_post('template'));
            if(trim($extra) == '') $extra = '<tr><td class="frm-field_heading" colspan="2">{TITLE}{FIELD}</td></tr>';
            break;
        case 'select':
            $extra = intval($admin->get_post('size')).','.$admin->get_post('multiselect');
            break;
        case 'checkbox':
            $extra = $admin->StripCodeFromText( $admin->get_post('seperator'));
            break;
        case 'radio':
            $extra = $admin->StripCodeFromText( $admin->get_post('seperator'));
            break;
        default:
            $value = '';
            $extra = '';
            break;
    endswitch;
    $sql  = 'UPDATE `'.TABLE_PREFIX.'mod_form_fields` SET '
          . '`value` = \''.$database->escapeString($value).'\', '
          . '`extra` = \''.$database->escapeString($extra).'\' '
          . 'WHERE `field_id` = \''.$database->escapeString($field_id).'\'';
    if( $database->query($sql) ) {
        $admin->print_success($TEXT['SUCCESS'], $backModuleUrl );
    }else {
        $admin->print_error($database->get_error(), $backModuleUrl );
    }
// Print admin footer
$admin->print_footer();

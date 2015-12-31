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
 * @version         $Id: add_field.php 1553 2011-12-31 15:03:03Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/form/add_field.php $
 * @lastmodified    $Date: 2011-12-31 16:03:03 +0100 (Sa, 31. Dez 2011) $
 * @description
 */

require( dirname(dirname((__DIR__))).'/config.php' );

// suppress to print the header, so no new FTAN will be set
$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = false;
// show the info banner
//$print_info_banner = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$BackUrl = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;
if(!$admin->checkFTAN('GET')) {
    $admin->print_header();
    $admin->print_error( 'FTAN::'.$MESSAGE['GENERIC_SECURITY_ACCESS'], $BackUrl );
}
    $admin->print_header();

//$sec_anchor = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? '#'.SEC_ANCHOR.$section['section_id'] : '' );

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_form_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);

 $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_form_fields` SET '
      . '`section_id` = '.$section_id.', '
      . '`page_id` = '.$page_id.', '
      . '`position` = '.$position.', '
      . '`title` = \'\', '
      . '`type` = \'\', '
      . '`required` = 0, '
      . '`value` = \'\', '
      . '`extra` = \'\' ';
// Insert new row into database
$database->query($sql);

// Get the id
$field_id = intval($database->getLastInsertId());

$ModifyUrl = WB_URL.'/modules/form/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id);
// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
    $admin->print_error($database->get_error(), $BackUrl );
} else {
    $admin->print_success($TEXT['SUCCESS'], $ModifyUrl );
}

// Print admin footer
$admin->print_footer();
exit();

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

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// Get id
$field_id = intval($admin->checkIDKEY('field_id', false, 'GET'));
if (!$field_id) {
 $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

$sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );

// Delete row
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_form_fields` '
          . 'WHERE `field_id` = '.$database->escapeString($field_id);
    $database->query($sql);

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');

// Create new order object an reorder
$order = new order(TABLE_PREFIX.'mod_form_fields', 'position', 'field_id', 'section_id');

if(!$order->clean($section_id)) {
    $admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id);
} else {
    $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id);
}

// Print admin footer
$admin->print_footer();

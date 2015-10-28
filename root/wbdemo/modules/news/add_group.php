<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: add_group.php 1538 2011-12-10 15:06:15Z Luisehahne $ 
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/add_group.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */

require('../../config.php');

// suppress to print the header, so no new FTAN will be set
$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = false;
// show the info banner
//$print_info_banner = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

if(!$admin->checkFTAN('GET')) {
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
}

// After check print the header
$admin->print_header();

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_news_groups', 'position', 'group_id', 'section_id');
$position = $order->get_new($section_id);

// Insert new row into database
$database->query("INSERT INTO ".TABLE_PREFIX."mod_news_groups (section_id,page_id,position,active) VALUES ('$section_id','$page_id','$position','1')");

// Get the id
$group_id = $admin->getIDKEY($database->get_one("SELECT LAST_INSERT_ID()"));

// Say that a new record has been added, then redirect to modify page
if($database->is_error()) {
   $admin->print_error($database->get_error(), WB_URL.'/modules/news/modify_group.php?page_id='.$page_id.'&section_id='.$section_id.'&group_id='.$group_id);
} else {
   $admin->print_success($TEXT['SUCCESS'],     WB_URL.'/modules/news/modify_group.php?page_id='.$page_id.'&section_id='.$section_id.'&group_id='.$group_id);
}

// Print admin footer
$admin->print_footer();

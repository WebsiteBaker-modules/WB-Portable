<?php
/**
 *
 * @category        modules
 * @package         menu_link
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/menu_link/save.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
*/

require( dirname(dirname((__DIR__))).'/config.php' );

$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
$backlink = ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id;
if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$backlink );
}
$admin->print_header();

// Update id, anchor and target
if(isset($_POST['menu_link'])) {
    $iTargetPageId = intval($admin->get_post('menu_link'));
    $iRedirectType = intval($admin->get_post('r_type'));
    $anchor = ($admin->get_post('page_target'));
    $sTarget = $admin->get_post('target');
    $extern='';
    if(isset($_POST['extern'])) {
        $extern = $admin->get_post('extern');
    }

    $table_pages = TABLE_PREFIX.'pages';
    $sql = 'UPDATE `'.TABLE_PREFIX.'pages` SET '
        .'`target` = \''.$database->escapeString($sTarget).'\' '
        .'WHERE `page_id` = '.$page_id;
    $database->query($sql);

    $sql = 'UPDATE `'.TABLE_PREFIX.'mod_menu_link` SET '
        .'`target_page_id` = '.$iTargetPageId.', '
        .'`redirect_type`  = '.$iRedirectType.', '
        .'`anchor` = \''.$database->escapeString($anchor).'\', '
        .'`extern` = \''.$database->escapeString($extern).'\' '
        .'WHERE `page_id` = '.$page_id;
    $database->query($sql);
}

// Check if there is a database error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(), $js_back);
} else {
    $admin->print_success($MESSAGE['PAGES']['SAVED'],$backlink );
}

// Print admin footer
$admin->print_footer();

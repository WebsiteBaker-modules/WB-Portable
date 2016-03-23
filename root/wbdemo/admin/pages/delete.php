<?php
/**
 *
 * @category        admin
 * @package         pages
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: delete.php 1457 2011-06-25 17:18:50Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/admin/pages/delete.php $
 * @lastmodified    $Date: 2011-06-25 19:18:50 +0200 (Sa, 25. Jun 2011) $
 *
 */


// Create new admin object and print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
$admin = new admin('Pages', 'pages_delete');

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');


if( (!($page_id = $admin->checkIDKEY('page_id', 0, $_SERVER['REQUEST_METHOD']))) )
{
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
    exit();
}

// Get perms
if (!$admin->get_page_permission($page_id,'admin')) {
    $admin->print_error($MESSAGE['PAGES_INSUFFICIENT_PERMISSIONS']);
}

// Find out more about the page
$query = "SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'";
$results = $database->query($query);
if($database->is_error()) {
    $admin->print_error($database->get_error());
}
if($results->numRows() == 0) {
    $admin->print_error($MESSAGE['PAGES_NOT_FOUND']);
}

$results_array = $results->fetchRow();

$visibility = $results_array['visibility'];

// Check if we should delete it or just set the visibility to 'deleted'
if(PAGE_TRASH != 'disabled' AND $visibility != 'deleted') {
    // Page trash is enabled and page has not yet been deleted
    // Function to change all child pages visibility to deleted
    function trash_subs($parent = 0) {
        global $database;
        // Query pages

        $sql = 'SELECT `page_id` FROM `'.TABLE_PREFIX.'pages` '
              .'WHERE `parent` = '.$parent.' '
              .'ORDER BY `position` ASC';
        if($oRes = $database->query($sql)) {
            // Check if there are any pages to show
            if($oRes->numRows() > 0) {
                // Loop through pages
                while($page = $oRes->fetchRow(MYSQLI_ASSOC)) {
                    // Update the page visibility to 'deleted'
                    $sql = 'UPDATE `'.TABLE_PREFIX.'pages` SET '
                          .'`visibility` = \'deleted\' '
                          .'WHERE `page_id` = '.$page['page_id'].' '
                          .'';
                    $database->query($sql);

                    if($database->is_error()) {
                        $admin->print_error($database->get_error());
                    }
                    // Run this function again for all sub-pages
                    trash_subs($page['page_id']);
                }
            }
        }
    }
    // Update the page visibility to 'deleted'
    $sql = 'UPDATE `'.TABLE_PREFIX.'pages` SET '
                      .'`visibility` = \'deleted\' '
                      .'WHERE `page_id` = '.$page_id.' '
                      .'';
                $database->query($sql);

                if($database->is_error()) {
                    $admin->print_error($database->get_error());
                }
    //
    // Run trash subs for this page
    trash_subs($page_id);
} else {
    // Really dump the page
    // Delete page subs
    $sub_pages = get_subs($page_id, array());
    foreach($sub_pages AS $sub_page_id) {
        delete_page($sub_page_id);
    }
    // Delete page
    delete_page($page_id);
}

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error());
} else {
    $admin->print_success($MESSAGE['PAGES_DELETED']);
}

// Print admin footer
$admin->print_footer();
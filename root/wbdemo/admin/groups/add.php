<?php
/**
 *
 * @category        admin
 * @package         groups
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: add.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/groups/add.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

// Print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }

// suppress to print the header, so no new FTAN will be set
$admin = new admin('Access', 'groups_add', false);
// Create a javascript back link
$js_back = ADMIN_URL.'/groups/index.php';

if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
}
// After check print the header
$admin->print_header();

// Gather details entered
$group_name = $database->escapeString(trim(strip_tags($admin->get_post('group_name'))));

// Check values
if($group_name == "") {
    $admin->print_error($MESSAGE['GROUPS_GROUP_NAME_BLANK'], $js_back);
}
$sql = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'groups` '
     . 'WHERE `name`=\''.$group_name.'\'';
if ($database->get_one($sql)) {
    $admin->print_error($MESSAGE['GROUPS_GROUP_NAME_EXISTS'], $js_back);  
}
$system_permissions = array();
// Get system and module permissions
require(ADMIN_PATH.'/groups/get_permissions.php');

// Update the database
$sql = 'INSERT INTO `'.TABLE_PREFIX.'groups` SET '
     .     '`name`=\''.$group_name.'\', '
     .     '`system_permissions`=\''.$database->escapeString($system_permissions).'\', '
     .     '`module_permissions`=\''.$database->escapeString($module_permissions).'\', '
     .     '`template_permissions`=\''.$database->escapeString($template_permissions).'\'';
if (($database->query($sql))) {
    $admin->print_success($MESSAGE['GROUPS_ADDED'], ADMIN_URL.'/groups/index.php');
} else {
    $admin->print_error($database->get_error());
}
// Print admin footer
$admin->print_footer();


<?php
/**
 *
 * @category        admin
 * @package         languages
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: uninstall.php 1467 2011-07-02 00:06:53Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/admin/languages/uninstall.php $
 * @lastmodified    $Date: 2011-07-02 02:06:53 +0200 (Sa, 02. Jul 2011) $
 * @description
 *
 */

// Include config file and admin class file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }

$admin = new admin('Addons', 'languages_uninstall', false);

$js_back = ADMIN_URL.'/languages/index.php';

if( !$admin->checkFTAN() )
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back );
}
// After check print the header
$admin->print_header();

// Check if user selected language
if(!isset($_POST['code']) || $_POST['code'] == "") {
    $code = '';
    $admin->print_error( $MESSAGE['GENERIC_FORGOT_OPTIONS'], $js_back );
} else {
    $code = $database->escapeString($_POST['code']); 
}

if (!preg_match('/^[A-Z]{2}$/', $code) && $code!='' ) {
    $admin->print_error( $MESSAGE['GENERIC_ERROR_OPENING_FILE'], $js_back );
}

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Check if the language exists
if(!file_exists(WB_PATH.'/languages/'.$code.'.php')) {
    $admin->print_error($MESSAGE['GENERIC_NOT_INSTALLED'], $js_back );
}

// Check if the language is in use
if($code == DEFAULT_LANGUAGE OR $code == LANGUAGE) {
    $admin->print_error($MESSAGE['GENERIC_CANNOT_UNINSTALL_IN_USE']);
} else {

    $query_users = $database->query("SELECT `user_id` FROM `".TABLE_PREFIX."users` WHERE `language` = '".$code."' LIMIT 1");

    if($query_users->numRows() > 0) {
        $admin->print_error($MESSAGE['GENERIC_CANNOT_UNINSTALL_IN_USE']);
    }
}

// Try to delete the language code
if(!unlink(WB_PATH.'/languages/'.$code.'.php')) {
    $admin->print_error($MESSAGE['GENERIC_CANNOT_UNINSTALL']);
} else {
    // Remove entry from DB
    $database->query("DELETE FROM `".TABLE_PREFIX."addons` WHERE `directory` = '".$code."' AND `type` = 'language'");
}

// Print success message
$admin->print_success($MESSAGE['GENERIC_UNINSTALLED']);

// Print admin footer
$admin->print_footer();

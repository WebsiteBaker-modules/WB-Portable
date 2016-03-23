<?php
/**
 *
 * @category        admin
 * @package         admintools
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: create.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/create.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

// Print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// Include the WB functions file
if( !defined('createFolderProtectFile') ){ require(WB_PATH.'/framework/functions.php');  }

// suppress to print the header, so no new FTAN will be set
$admin = new admin('Media', 'media_create', false);

// Get dir name and target location
$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$name = (isset(${$requestMethod}['name'])) ? ${$requestMethod}['name'] : '';

// Check to see if name or target contains ../
if(strstr($name, '..')) {
    $admin->print_header();
    $admin->print_error($MESSAGE['MEDIA_NAME_DOT_DOT_SLASH']);
}

// Remove bad characters
$name = trim(media_filename($name),'.');

// Target location
$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$target = (isset(${$requestMethod}['target'])) ? ${$requestMethod}['target'] : '';

if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
}
// After check print the header
$admin->print_header();

if (!check_media_path($target, false)) {
    $admin->print_error($MESSAGE['MEDIA_TARGET_DOT_DOT_SLASH']);
}

// Create relative path of the new dir name
$directory = WB_PATH.$target.'/'.$name;

// Check to see if the folder already exists
if(file_exists($directory)) {
    $admin->print_error($MESSAGE['MEDIA_DIR_EXISTS']);
}

//if ( sizeof(createFolderProtectFile( $directory )) )
if ( !make_dir( $directory ) )
{
    $admin->print_error($MESSAGE['MEDIA_DIR_NOT_MADE']);
} else {
    createFolderProtectFile($directory);
    $usedFiles = array();
    // feature freeze
    // require_once(ADMIN_PATH.'/media/dse.php');
    $admin->print_success($MESSAGE['MEDIA_DIR_MADE']);
}

// Print admin
$admin->print_footer();

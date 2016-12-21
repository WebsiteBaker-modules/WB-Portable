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
 * @version         $Id: delete.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/delete.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

require(dirname(dirname(__DIR__)).'/config.php');
// Create admin object
require_once(WB_PATH.'/framework/class.admin.php');
$admin = new admin('Media', 'media_delete', false);

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Get the current dir
$directory = $admin->get_get('dir');
$directory = ($directory == '/') ?  '' : $directory;

$dirlink = 'browse.php?dir='.$directory;
$rootlink = 'browse.php?dir=';

// Check to see if it contains ..
if (!check_media_path($directory)) {
    // $admin->print_header();
    $admin->print_error($MESSAGE['MEDIA_DIR_DOT_DOT_SLASH'],$rootlink,false );
}

// Get the file id
$file_id = intval($admin->checkIDKEY('id', false, $_SERVER['REQUEST_METHOD']))-1;
if ($file_id===false) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $dirlink,false);
}

// Get home folder not to show
$home_folders = get_home_folders();
$usedFiles = array();
// feature freeze
// require_once(ADMIN_PATH.'/media/dse.php');
/*

if(!empty($currentdir)) {
    $usedFiles = $Dse->getMatchesFromDir( $directory, DseTwo::RETURN_USED);
}
*/

$DIR  = array();
$FILE = array();
// Check for potentially malicious files
$forbidden_file_types  = preg_replace( '/\s*[,;\|#]\s*/','|',RENAME_FILES_ON_UPLOAD);
//$aDirList = glob (WB_PATH.MEDIA_DIRECTORY.'/'.$directory.'/*',GLOB_MARK|GLOB_NOSORT);
// Figure out what folder name the temp id is
if($handle = opendir(WB_PATH.MEDIA_DIRECTORY.'/'.$directory)) {
    // Loop through the files and dirs an add to list
   while (false !== ($file = readdir($handle))) {
        $info = pathinfo($file);
        $ext = isset($info['extension']) ? $info['extension'] : '';
        if(substr($file, 0, 1) != '.' AND $file != '.svn' AND $file != 'index.php') {
            if( !preg_match('/'.$forbidden_file_types.'$/i', $ext) ) {
                if(is_dir(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$file)) {
                    if(!isset($home_folders[$directory.'/'.$file])) {
                        $DIR[] = $file;
                    }
                } else {
                    $FILE[] = $file;
                }
            }
        }
    }
closedir($handle);
}

    $iSortFlags = ((version_compare(PHP_VERSION, '5.4.0', '<'))?SORT_REGULAR:SORT_NATURAL|SORT_FLAG_CASE);
    sort($DIR, $iSortFlags);
    sort($FILE, $iSortFlags);
    $aListDir = array_merge($DIR,$FILE);
    $temp_id = 0;
    if(isset($aListDir)) {
        foreach($aListDir AS $name) {
            if(!isset($delete_file) AND $file_id == $temp_id) {
                $delete_file = $name;
                $type = is_dir(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$delete_file)?'folder':'file';
            }
            $temp_id++;
        }
    }

// Check to see if we could find an id to match
if(!isset($delete_file)) {
    $admin->print_error($MESSAGE['MEDIA_FILE_NOT_FOUND'], $dirlink, false);
}
$relative_path = WB_PATH.MEDIA_DIRECTORY.'/'.$directory.'/'.$delete_file;
// Check if the file/folder exists
if(!file_exists($relative_path)) {
    $admin->print_error($MESSAGE['MEDIA_FILE_NOT_FOUND'], $dirlink, false);
}

// Find out whether its a file or folder
/**/
if($type == 'folder') {
    // Try and delete the directory
    if(rm_full_dir($relative_path)) {
        $admin->print_success($MESSAGE['MEDIA_DELETED_DIR'], $dirlink);
    } else {
        $admin->print_error($MESSAGE['MEDIA_CANNOT_DELETE_DIR'], $dirlink, false);
    }
} else {
    // Try and delete the file
    if(unlink($relative_path)) {
        $admin->print_success($MESSAGE['MEDIA_DELETED_FILE'], $dirlink);
    } else {
        $admin->print_error($MESSAGE['MEDIA_CANNOT_DELETE_FILE'], $dirlink, false);
    }
}

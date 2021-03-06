<?php
/**
 *
 * @category        admin
 * @package         media
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: rename2.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/rename2.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// Create admin object
$admin = new admin('Media', 'media_rename', false);

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Get the current dir
$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$directory = (isset(${$requestMethod}['dir'])) ? ${$requestMethod}['dir'] : '';
$directory = ($directory == '/') ?  '' : $directory;

$dirlink = 'browse.php?dir='.$directory;
$rootlink = 'browse.php?dir=';
// $file_id = intval($admin->get_post('id'));

// first Check to see if it contains ..
if (!check_media_path($directory)) {
    $admin->print_error($MESSAGE['MEDIA_DIR_DOT_DOT_SLASH'],$rootlink, false);
}

// Get the temp id
$iFileId = $file_id = intval($admin->checkIDKEY('id', false, $_SERVER['REQUEST_METHOD']));
if ($file_id===false) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$dirlink, false);
}

$DIR  = array();
$FILE = array();
// Check for potentially malicious files
$forbidden_file_types  = preg_replace( '/\s*[,;\|#]\s*/','|',RENAME_FILES_ON_UPLOAD);
// Get home folder not to show
$home_folders = get_home_folders();

// Figure out what folder name the temp id is
if($handle = opendir(WB_PATH.MEDIA_DIRECTORY.'/'.$directory)) {
    // Loop through the files and dirs an add to list
    $temp_id = 0;
    while (false !== ($file = readdir($handle))) {
        if(substr($file, 0, 1) != '.' AND $file != '.svn' AND $file != 'index.php') {
            $info = pathinfo($file);
            $ext = isset($info['extension']) ? $info['extension'] : '';
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
    sort($DIR,  $iSortFlags);
    sort($FILE, $iSortFlags);
    $aListDir = array_merge($DIR,$FILE);
    $temp_id = 0;
    if(isset($aListDir)) {
//        sort($aListDir, SORT_REGULAR|SORT_FLAG_CASE);
        foreach($aListDir AS $name)
        {
            if(!isset($rename_file)&& ($file_id == $temp_id)) {
                $rename_file = $name;
                $type = is_dir(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$rename_file)?'folder':'file';
            }
            $temp_id++;
        }
    }

$file_id = $admin->getIDKEY($file_id);

if(!isset($rename_file)) {
    $admin->print_error($MESSAGE['MEDIA_FILE_NOT_FOUND'], $dirlink, false);
}

// Check if they entered a new name
if(media_filename($admin->get_post('name')) == "") {
    $admin->print_error($MESSAGE['MEDIA_BLANK_NAME'], "rename.php?dir=$directory&id=$file_id", false);
} else {
    $old_name = $admin->get_post('old_name');
    $new_name =  media_filename($admin->get_post('name'));
}

// Check if they entered an extension
if($type == 'file') {
    if (strstr($new_name,'.')){
        $new_name = str_replace('.', '_', $new_name);
    }
    if(media_filename($admin->get_post('extension')) == "") {
        $name = $new_name;
    } else {
        $extension = media_filename($admin->get_post('extension'));
        $name = $new_name.'.'.trim($extension,'.');
    }
} elseif($type == 'folder') {
    $extension = '';
    $name = $new_name;
}

// Join new name and extension

$info = pathinfo(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$name);
$ext = isset($info['extension']) ? $info['extension'] : '';
$dots = (substr($info['basename'], 0, 1) == '.') || (substr($info['basename'], -1, 1) == '.');

if( preg_match('/'.$forbidden_file_types.'$/i', $ext) || $dots == '.' ) {
    $admin->print_error($MESSAGE['MEDIA_CANNOT_RENAME'], "rename.php?dir=$directory&id=$file_id", false);
}

// Check if the name contains ..
if(strstr($name, '..')) {
    $admin->print_error($MESSAGE['MEDIA_NAME_DOT_DOT_SLASH'], "rename.php?dir=$directory&id=$file_id", false);
}

// Check if the name is index.php
if($name == 'index.php') {
    $admin->print_error($MESSAGE['MEDIA_NAME_INDEX_PHP'], "rename.php?dir=$directory&id=$file_id", false);
}

// Check that the name still has a value
if($name == '') {
    $admin->print_error($MESSAGE['MEDIA_BLANK_NAME'], "rename.php?dir=$directory&id=$file_id", false);
}

$info = pathinfo(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$rename_file);
$ext = isset($info['extension']) ? $info['extension'] : '';
$dots = (substr($info['basename'], 0, 1) == '.') || (substr($info['basename'], -1, 1) == '.');

if( preg_match('/'.$forbidden_file_types.'$/i', $ext) || $dots == '.' ) {
    $admin->print_error($MESSAGE['MEDIA_CANNOT_RENAME'], "rename.php?dir=$directory&id=$file_id", false);
}

// Check if we should overwrite or not
if($admin->get_post('overwrite') != 'yes' AND file_exists(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$name) == true) {
    if($type == 'folder') {
        $admin->print_error($MESSAGE['MEDIA_DIR_EXISTS'], "rename.php?dir=$directory&id=$file_id", false);
    } else {
        $admin->print_error($MESSAGE['MEDIA_FILE_EXISTS'], "rename.php?dir=$directory&id=$file_id", false);
    }
}

// Try and rename the file/folder
if(rename(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$rename_file, WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$name)) {
    $usedFiles = array();
    // feature freeze
    // require_once(ADMIN_PATH.'/media/dse.php');
    $admin->print_success($MESSAGE['MEDIA_RENAMED'], $dirlink);
} else {
    $admin->print_error($MESSAGE['MEDIA_CANNOT_RENAME'], "rename.php?dir=$directory&id=$file_id", false);
}

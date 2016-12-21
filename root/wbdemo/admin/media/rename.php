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
 * @version         $Id: rename.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/rename.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

require(dirname(dirname(__DIR__)).'/config.php');

// Create admin object
require_once(WB_PATH.'/framework/class.admin.php');
$admin = new admin('Media', 'media_rename', false);

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Get the current dir
$directory = $admin->get_get('dir');
$directory = ($directory == '/') ?  '' : $directory;

$dirlink = 'browse.php?dir='.$directory;
$rootlink = 'browse.php?dir=';
// $file_id = intval($admin->get_get('id'));

// first Check to see if it contains ..
if (!check_media_path($directory)) {
    $admin->print_error($MESSAGE['MEDIA_DIR_DOT_DOT_SLASH'],$rootlink, false);
}

// Get the temp id
$file_id = intval($admin->checkIDKEY('id', false, $_SERVER['REQUEST_METHOD']))-1;
if ($file_id===false) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$dirlink, false);
}

$DIR  = array();
$FILE = array();
// Get home folder not to show
$home_folders = get_home_folders();
// Check for potentially malicious files
$forbidden_file_types  = preg_replace( '/\s*[,;\|#]\s*/','|',RENAME_FILES_ON_UPLOAD);
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
        foreach($aListDir AS $name)
        {
            if(!isset($rename_file)&& ($file_id == $temp_id)) {
                $rename_file = $name;
                $type = is_dir(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$rename_file)?'folder':'file';
            }
            $temp_id++;
        }
    }
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.$file_id.'=='.$temp_id.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $rename_file.' '.$type ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aListDir ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/

if(!isset($rename_file)) {
    $admin->print_error($MESSAGE['MEDIA_FILE_NOT_FOUND'], $dirlink, false);
}

$sExtension = '';
$sBasename = $rename_file;
preg_match (
    '/^(?:.*?[\/])?([^\/]*?)\.([^\.]*)$/iU',
    str_replace('\\', '/', $rename_file),
    $aMatches
);
if (sizeof($aMatches) == 3) {
    $sBasename  = $aMatches[1];
    $sExtension = $aMatches[2];
}

// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('media_rename.htt')));
$template->set_file('page', 'media_rename.htt');
$template->set_block('page', 'main_block', 'main');
//echo WB_PATH.'/media/'.$directory.'/'.$rename_file;
if($type == 'folder') {
    $template->set_var('DISPlAY_EXTENSION', 'hide');
    $extension = '';
} else {
    $template->set_var('DISPlAY_EXTENSION', '');
    $extension = strstr($rename_file, '.');
}

if($type == 'folder') {
    $type = $TEXT['FOLDER'];
} else {
    $type = $TEXT['FILE'];
}

$template->set_var(array(
                    'THEME_URL' => THEME_URL,
                    'FILENAME' => $rename_file,
                    'BASENAME' => $sBasename,
                    'DIR' => $directory,
                    'FILE_ID' => $admin->getIDKEY($file_id),
                    // 'FILE_ID' => $file_id,
                    'TYPE' => $type,
                    'EXTENSION' => $sExtension,
                    'FTAN' => $admin->getFTAN()
                )
            );


// Insert language text and messages
$template->set_var(array(
                    'TEXT_TO' => $TEXT['TO'],
                    'TEXT_RENAME' => $TEXT['RENAME'],
                    'TEXT_CANCEL' => $TEXT['CANCEL'],
                    'TEXT_UP' => $TEXT['UP'],
                    'TEXT_OVERWRITE_EXISTING' => $TEXT['OVERWRITE_EXISTING']
                )
            );

// Parse template object
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');

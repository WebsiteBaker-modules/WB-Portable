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
 * @version         $Id: browse.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/browse.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

// Create admin object
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }


$admin = new admin('Media', 'media', false);

$starttime = explode(" ", microtime());
$starttime = $starttime[0]+$starttime[1];

// Include the WB functions file
if(!function_exists('check_media_path')) { require(WB_PATH.'/framework/functions.php'); }
include ('parameters.php');

// check if theme language file exists for the language set by the user (e.g. DE, EN)
if(file_exists(THEME_PATH .'/languages/EN.php')) {
require(THEME_PATH .'/languages/EN.php');
}
if(file_exists(THEME_PATH .'/languages/'.LANGUAGE .'.php')) {
    require(THEME_PATH .'/languages/'.LANGUAGE .'.php');
}

// Byte convert for filesize
function byte_convert($bytes) {
    $symbol = array(' bytes', ' KB', ' MB', ' GB', ' TB');
    $exp = 0;
    $converted_value = 0;
    if( $bytes > 0 ) {
        $exp = floor( log($bytes)/log(1024) );
        $converted_value = ( $bytes/pow(1024,floor($exp)) );
    }
    return sprintf( '%.2f '.$symbol[$exp], $converted_value );
}

// Get file extension
function get_filetype($fname) {
    $pathinfo = pathinfo($fname);
    $extension = (isset($pathinfo['extension'])) ? strtolower($pathinfo['extension']) : '';
    return $extension;
}

// Get file extension for icons
function get_filetype_icon($fname) {
    $pathinfo = pathinfo($fname);
    $extension = (isset($pathinfo['extension'])) ? strtolower($pathinfo['extension']) : '';
    if (file_exists(THEME_PATH.'/images/files/'.$extension.'.png')) {
        return $extension;
    } else {
        return 'blank_16';
    }
}

function ToolTip($name, $detail = '')
{
//    parse_str($name, $array);
//    $name = $array['img'];
    $parts = explode(".", $name);
    $ext = strtolower( end($parts));
    if (strpos('.gif.jpg.jpeg.png.bmp.', $ext))
    {
        $retVal = 'onmouseover="return overlib('.
            '\'<img src=\\\''.($name).'\\\''.
            'alt=\\\'\\\' '.
            'maxwidth=\\\'300\\\' '.
            'maxheight=\\\'300\\\' />\','.
//            '>\','.
//            'CAPTION,\''.basename($name).'\','.
            'FGCOLOR,\'#ffffff\','.
            'BGCOLOR,\'#557c9e\','.
            'BORDER,1,'.
            'FGCOLOR, \'#ffffff\','.
            'BGCOLOR,\'#557c9e\','.
            'CAPTIONSIZE,\'12px\','.
            'CLOSETEXT,\'X\','.
            'CLOSECOLOR,\'#ffffff\','.
            'CLOSESIZE,\'14px\','.
            'VAUTO,'.
            'HAUTO,'.
            ''.
//            'STICKY,'.
            'MOUSEOFF,'.
            'WRAP,'.
            'CELLPAD,5'.
            ''.
            ''.
            ''.
            ')" onmouseout="return nd()"';
        return $retVal;
//        return ('onmouseover="return overlib(\'<img src=\\\''.($name).'\\\' maxwidth=\\\'600\\\'  maxheight=\\\'600\\\'>\',BORDER,1,FGCOLOR, \'#ffffff\',VAUTO,WIDTH)" onmouseout="return nd()" ');
    } else {
        return '';
    }
}

function fsize($size) {
   if($size == 0) return("0 Bytes");
   $filesizename = array(" bytes", " kB", " MB", " GB", " TB");
   return round($size/pow(1024, ($i = floor(log($size, 1024)))), 1) . $filesizename[$i];
}

// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('media_browse.htt')));
$template->set_file('page', 'media_browse.htt');
$template->set_block('page', 'main_block', 'main');
// Get the current dir
//$currentHome = $admin->get_home_folder();
$currentHome = (defined('HOME_FOLDERS') && HOME_FOLDERS) ? $admin->get_home_folder() : '';

// set directory if you call from menu
$directory =    (($currentHome) AND (!array_key_exists('dir',$_GET)))
                ?
                $currentHome
                :
                $admin->strip_slashes($admin->get_get('dir')) ;

// check for correct directory
if ($currentHome && stripos(WB_PATH.MEDIA_DIRECTORY.$directory,WB_PATH.MEDIA_DIRECTORY.$currentHome)===false) {
    $directory = $currentHome;
}
if($directory == '/' OR $directory == '\\') {$directory = '';}

$sBackLink = WB_PATH.MEDIA_DIRECTORY.$directory;
if(!is_readable( $sBackLink )) {
$directory = dirname($directory);
// reload parent page to rebuild the dropdowns
echo "<script type=\"text/javascript\">
<!--
// Get the location object
var locationObj = document.location;
// Set the value of the location object
parent.document.location = 'index.php';
-->
</script>";
}

$dir_backlink = 'browse.php?dir='.$directory;

// Check to see if it contains ../
if (!check_media_path($directory)) {
    // $admin->print_header();
    $admin->print_error($MESSAGE['MEDIA_DIR_DOT_DOT_SLASH']);
}

if(!file_exists(WB_PATH.MEDIA_DIRECTORY.$directory)) {
    // $admin->print_header();
    $admin->print_error($MESSAGE['MEDIA_DIR_DOES_NOT_EXIST']);
}

// Check to see if the user wanted to go up a directory into the parent folder
if($admin->get_get('up') == 1) {
    $parent_directory = dirname($directory);
    header("Location: browse.php?dir=$parent_directory");
    exit(0);
}

if ($_SESSION['GROUP_ID'] != 1 && (isset($pathsettings['global']['admin_only']) && $pathsettings['global']['admin_only']) ) { // Only show admin the settings link
    $template->set_var('DISPLAY_SETTINGS', 'hide');
}

// Workout the parent dir link
$parent_dir_link = ADMIN_URL.'/media/browse.php?dir='.$directory.'&amp;up=1';
// Workout if the up arrow should be shown
if(($directory == '') or ($directory==$currentHome)) {
    $display_up_arrow = 'hide';
} else {
    $display_up_arrow = '';
}

// Insert values
$template->set_var(array(
                    'THEME_URL' => THEME_URL,
                    // 'THEME_URL' => '',
                    'CURRENT_DIR' => $directory,
                    'PARENT_DIR_LINK' => $parent_dir_link,
                    'DISPLAY_UP_ARROW' => $display_up_arrow,
                    'INCLUDE_PATH' => WB_URL.'/include'
                )
            );

// Get home folder not to show
//$home_folders = get_home_folders();
$home_folders = (defined('HOME_FOLDERS') && HOME_FOLDERS) ? get_home_folders() : array();

// Generate list
$template->set_block('main_block', 'list_block', 'list');

$usedFiles = array();
// require_once(ADMIN_PATH.'/media/dse.php');
// $filename =  $currentdir;
if(!empty($currentdir)) {
    $usedFiles = $Dse->getMatchesFromDir( $currentdir, DseTwo::RETURN_USED);
}

$DIR  = array();
$FILE = array();
// Check for potentially malicious files
$forbidden_file_types  = preg_replace( '/\s*[,;\|#]\s*/','|',RENAME_FILES_ON_UPLOAD);
if($handle = opendir(WB_PATH.MEDIA_DIRECTORY.'/'.$directory)) {
    // Loop through the files and dirs an add to list
   while (false !== ($file = readdir($handle))) {
        $info = pathinfo($file);
        $ext = isset($info['extension']) ? $info['extension'] : '';
        if(substr($file, 0, 1) != '.' AND $file != '.svn' AND $file != 'index.php') {
            if( !preg_match('/'.$forbidden_file_types.'$/i', $ext) ) {
                if(is_dir(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$file)) {
//                    if( !isset($home_folders[$directory.'/'.$file]) ) {
                    if(!isset($home_folders[$directory.'/'.$file]) || $currentHome =='' )
                    {
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
    // Now parse these values to the template
    $temp_id = 0;
    $row_bg_color = 'FFF';
    if(isset($aListDir)) {
        foreach($aListDir AS $name)
        {
            $sFileName = WB_PATH.'/'.MEDIA_DIRECTORY.$directory.'/'.$name;
            if (is_dir($sFileName)){
            $link_name = str_replace(' ', '%20', $name);
            $temp_id++;
            $template->set_var(array(
                                'NAME' => $name,
                                'NAME_SLASHED' => addslashes($name),
                                'TEMP_ID' => $admin->getIDKEY($temp_id),
                                // 'TEMP_ID' => $temp_id,
                                'LINK' => "browse.php?dir=$directory/$link_name",
                                'LINK_TARGET' => '_self',
                                'ROW_BG_COLOR' => $row_bg_color,
                                'FT_ICON' => THEME_URL.'/images/folder_16.png',
                                'FILETYPE_ICON' => THEME_URL.'/images/folder_16.png',
                                'MOUSEOVER' => '',
                                'IMAGEDETAIL' => '',
                                'SIZE' => '--',
                                'DATE' => '',
                                'PREVIEW' => '',
                                'IMAGE_TITLE' => $name,
                                'IMAGE_EXIST' => 'blank_16.gif'
                            )
                        );
            $template->parse('list', 'list_block', true);
            // Code to alternate row colors
            $row_bg_color = (($row_bg_color == 'FFF') ?'ECF1F3':'FFF');
    }else {
        $filepreview = array('jpg','gif','tif','tiff','png','txt','css','js','cfg','conf','pdf','zip','gz','doc');
            $size = filesize(WB_PATH.'/'.MEDIA_DIRECTORY.$directory.'/'.$name);
            $bytes = byte_convert($size);
            $fdate = filemtime(WB_PATH.'/'.MEDIA_DIRECTORY.$directory.'/'.$name);
            $date = gmdate(DATE_FORMAT.' '.TIME_FORMAT, $fdate);
            $filetypeicon = get_filetype_icon(WB_URL.MEDIA_DIRECTORY.$directory.'/'.$name);
            $filetype = get_filetype(WB_URL.MEDIA_DIRECTORY.$directory.'/'.$name);

            if (in_array($filetype, $filepreview)) {
                $preview = 'preview';
            } else {
                $preview = '';
            }
            $temp_id++;
            $imgdetail = '';
            // $icon = THEME_URL.'/images/blank_16.gif';
            $icon = '';
            $tooltip = '';
            if (!$pathsettings['global']['show_thumbs']) {
                $info = getimagesize(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$name);
                if ($info[0]) {
                    $imgdetail = fsize(filesize(WB_PATH.MEDIA_DIRECTORY.$directory.'/'.$name)).'<br /> '.$info[0].' x '.$info[1].' px';
                    $icon = 'thumb.php?t=1&amp;img='.$directory.'/'.$name;
                    $tooltip = ToolTip('thumb.php?t=2&amp;img='.$directory.'/'.$name);
                }
            }
            $filetype_url = THEME_URL.'/images/files/'.$filetypeicon.'.png';
            $template->set_var(array(
                                'NAME' => $name,
                                'NAME_SLASHED' => addslashes($name),
                                'TEMP_ID' => $admin->getIDKEY($temp_id),
                                // 'TEMP_ID' => $temp_id,
                                'LINK' => WB_URL.MEDIA_DIRECTORY.$directory.'/'.$name,
                                'LINK_TARGET' => '_blank',
                                'ROW_BG_COLOR' => $row_bg_color,
                                'FT_ICON' => empty($icon) ? $filetype_url : $icon,
                                'FILETYPE_ICON' => $filetype_url,
                                'MOUSEOVER' => $tooltip,
                                'IMAGEDETAIL' => $imgdetail,
                                'SIZE' => $bytes,
                                'DATE' => $date,
                                'PREVIEW' => $preview,
                                'IMAGE_TITLE' => $name,
                                'IMAGE_EXIST' =>  'blank_16.gif'
                            )
                        );
            $template->parse('list', 'list_block', true);
            // Code to alternate row colors
            if($row_bg_color == 'FFF') {
                $row_bg_color = 'ECF1F3';
            } else {
                $row_bg_color = 'FFF';
            }
    }
        } #foreach
    }

// If no files are in the media folder say so
if($temp_id == 0) {
    $template->set_var('DISPLAY_LIST_TABLE', 'hide');
} else {
    $template->set_var('DISPLAY_NONE_FOUND', 'hide');
}

//if($currentHome=='') {
if( !in_array($admin->get_username(), explode('/',$directory)) ) {
// Insert permissions values
    if($admin->get_permission('media_rename') != true) {
        $template->set_var('DISPLAY_RENAME', 'hide');
    }
    if($admin->get_permission('media_delete') != true) {
        $template->set_var('DISPLAY_DELETE', 'hide');
    }
}

// Insert language text and messages
$template->set_var(array(
                    'MEDIA_DIRECTORY' => MEDIA_DIRECTORY,
                    'TEXT_CURRENT_FOLDER' => $TEXT['CURRENT_FOLDER'],
                    'TEXT_RELOAD' => $TEXT['RELOAD'],
                    'TEXT_RENAME' => $TEXT['RENAME'],
                    'TEXT_DELETE' => $TEXT['DELETE'],
                    'TEXT_SIZE' => $TEXT['SIZE'],
                    'TEXT_DATE' => $TEXT['DATE'],
                    'TEXT_NAME' => $TEXT['NAME'],
                    'TEXT_TYPE' => $TEXT['TYPE'],
                    'TEXT_UP' => $TEXT['UP'],
                    'NONE_FOUND' => $MESSAGE['MEDIA_NONE_FOUND'],
                    'CHANGE_SETTINGS' => $TEXT['MODIFY_SETTINGS'],
                    'CONFIRM_DELETE' => $MESSAGE['MEDIA_CONFIRM_DELETE']
                )
            );

// Parse template object
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');
/*
$endtime=explode(" ", microtime());
$endtime=$endtime[0]+$endtime[1];
$debugVMsg = '';
if($admin->ami_group_member('1') ) {
    $debugVMsg  = "<p>Mask loaded in ".round($endtime - $starttime,6)." Sec,&nbsp;&nbsp;";
    $debugVMsg .= "Memory in use ".number_format(memory_get_usage(true), 0, ',', '.')."&nbsp;Byte,&nbsp;&nbsp;";
    $debugVMsg .= sizeof(get_included_files())."&nbsp;included files</p>";
    // $debugVMsg = print_message($debugVMsg,'#','debug',-1,false);
    print $debugVMsg.'<br />';
 }
*/
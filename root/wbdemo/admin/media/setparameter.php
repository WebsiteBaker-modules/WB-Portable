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
 * @version         $Id: setparameter.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/setparameter.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
$admin = new admin('Media', 'media', false);
// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

// check if theme language file exists for the language set by the user (e.g. DE, EN)
if(file_exists(THEME_PATH .'/languages/EN.php')) {
require(THEME_PATH .'/languages/EN.php');
}
if(file_exists(THEME_PATH .'/languages/'.LANGUAGE .'.php')) {
    require(THEME_PATH .'/languages/'.LANGUAGE .'.php');
}
//Save post vars to the parameters file
if ( !is_null($admin->get_post("save"))) {
/*
    if (!$admin->checkFTAN())
    {
        $admin->print_error('::'.$MESSAGE['GENERIC_SECURITY_ACCESS'],'browse.php',false);
    }
*/

    if(DEFAULT_THEME != ' wb_theme') {
        //Check for existing settings entry, if not existing, create a record first!
        if (!$database->query ( "SELECT * FROM ".TABLE_PREFIX."settings where `name`='mediasettings'" )) {
            $database->query ( "INSERT INTO ".TABLE_PREFIX."settings (`name`,`value`) VALUES ('mediasettings','')" );
        }
    } else {
        $pathsettings = array();
    }

    $dirs = directory_list(WB_PATH.MEDIA_DIRECTORY);
    $dirs[] = WB_PATH.MEDIA_DIRECTORY;
    foreach($dirs AS $name) {
        $r = str_replace(WB_PATH, '', $name);
        $r = str_replace(array('/',' '),'_',$r);
        $w = (int)$admin->get_post($r.'-w');
        $h = (int)$admin->get_post($r.'-h');
        $pathsettings[$r]['width']=$w;
        $pathsettings[$r]['height']=$h;
    }
    $pathsettings['global']['admin_only'] = ($admin->get_post('admin_only')!=''?'checked':'');
    $pathsettings['global']['show_thumbs'] = ($admin->get_post('show_thumbs')!=''?'checked':'');
    $fieldSerialized = serialize($pathsettings);
    $database->query ( "UPDATE ".TABLE_PREFIX."settings SET `value` = '$fieldSerialized' WHERE `name`='mediasettings'" );
    header ("Location: browse.php");
}

include ('parameters.php');
if ($_SESSION['GROUP_ID'] != 1 && $pathsettings['global']['admin_only']) {
    echo "Sorry, settings not available";
    exit();
}

// Read data to display
$caller = "setparameter";

// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('setparameter.htt')));
$template->set_file('page', 'setparameter.htt');
$template->set_block('page', 'main_block', 'main');
if ($_SESSION['GROUP_ID'] != 1) {
    $template->set_var('DISPLAY_ADMIN', 'hide');
}
$template->set_var(array(
                'TEXT_HEADER' => $TEXT['TEXT_HEADER'],
                'SAVE_TEXT' => $TEXT['SAVE'],
                'BACK' => $TEXT['BACK'],
            )
        );

$template->set_block('main_block', 'list_block', 'list');
$row_bg_color = '';
$dirs = directory_list(WB_PATH.MEDIA_DIRECTORY);
$dirs[] = WB_PATH.MEDIA_DIRECTORY;

$array_lowercase = array_map('strtolower', $dirs);
array_multisort($array_lowercase, SORT_ASC, SORT_STRING, $dirs);
$id=0;
foreach($dirs AS $name) {
    $relative = str_replace(WB_PATH, '', $name);
    $safepath = str_replace(array('/',' '),'_',$relative);
    $cur_width = $cur_height = '';
    if (isset($pathsettings[$safepath]['width'])) $cur_width = $pathsettings[$safepath]['width'];
    if (isset($pathsettings[$safepath]['height'])) $cur_height = $pathsettings[$safepath]['height'];
    $cur_width = ($cur_width ? (int)$cur_width : '-');
    $cur_height = ($cur_height ? (int)$cur_height : '-');
    $id++;

    if($row_bg_color == 'DEDEDE') $row_bg_color = 'EEEEEE';
    else $row_bg_color = 'DEDEDE';
    $template->set_var(array(
                    'ADMIN_URL' => ADMIN_URL,
                    'PATH_NAME' => $relative,
                    'WIDTH' => $TEXT['WIDTH'],
                    'HEIGHT' => $TEXT['HEIGHT'],
                    'FIELD_NAME_W' => $safepath.'-w',
                    'FIELD_NAME_H' => $safepath.'-h',
                    'CUR_WIDTH' => $cur_width,
                    'CUR_HEIGHT' => $cur_height,
                    'SETTINGS' => $TEXT['SETTINGS'],
                    'ADMIN_ONLY' => $TEXT['ADMIN_ONLY'],
                    'ADMIN_ONLY_SELECTED' => $pathsettings['global']['admin_only'],
                    'NO_SHOW_THUMBS' => $TEXT['NO_SHOW_THUMBS'],
                    'NO_SHOW_THUMBS_SELECTED' => $pathsettings['global']['show_thumbs'],
                    'ROW_BG_COLOR' => $row_bg_color,
                    'FTAN' => $admin->getFTAN(),
                    'FILE_ID' => $id,
                )
        );
    $template->parse('list', 'list_block', true);
}

$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');
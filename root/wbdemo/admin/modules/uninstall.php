<?php
/**
 *
 * @category        admin
 * @package         modules
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: uninstall.php 1467 2011-07-02 00:06:53Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/admin/modules/uninstall.php $
 * @lastmodified    $Date: 2011-07-02 02:06:53 +0200 (Sa, 02. Jul 2011) $
 *
 */

// Include config file and admin class file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// Include the WB functions file
if ( !function_exists( 'get_modul_version' ) ) { require(WB_PATH.'/framework/functions.php'); }

if (!function_exists("replace_all")) {
    function replace_all ($aStr = "", &$aArray ) {
        foreach($aArray as $k=>$v) $aStr = str_replace("{{".$k."}}", $v, $aStr);
        return $aStr;
    }
}

$admin = new admin('Addons', 'modules_uninstall', false);

$js_back = ADMIN_URL.'/modules/index.php';

if( !$admin->checkFTAN() )
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back );
}
// After check print the header
$admin->print_header();

// Check if user selected module
if(!isset($_POST['file']) || $_POST['file'] == "") {
    $admin->print_error( $MESSAGE['GENERIC_FORGOT_OPTIONS'], $js_back );
} else {
    $sAddonsFile = $admin->StripCodeFromText($_POST['file']);
}
// Extra protection
if(trim($sAddonsFile) == '') {
    $admin->print_error($MESSAGE['GENERIC_ERROR_OPENING_FILE'], $js_back );
}
// check whether the module is core
$aPreventFromUninstall = array ( 'captcha_control', 'jsadmin', 'output_filter', 'wysiwyg', 'menu_link' );
if(
    preg_match('/'.$sAddonsFile.'/si', implode('|', $aPreventFromUninstall ))
) {
    $temp = array ('name' => $file );
    $msg = replace_all( $MESSAGE['MEDIA_CANNOT_DELETE_DIR'], $temp );
    $admin->print_error( $msg );
}
// Check if the module exists
if(!is_dir(WB_PATH.'/modules/'.$sAddonsFile) ) {
    $admin->print_error($MESSAGE['GENERIC_NOT_INSTALLED'], $js_back );
}
$sql  = 'SELECT `section_id`, `page_id` FROM `'.TABLE_PREFIX.'sections` '
      . 'WHERE `module`=\''.$database->escapeString($sAddonsFile).'\'';
if( $oAddon = $database->query($sql)) {

    if ( $oAddon->numRows() > 0) {

        /**
        *    Modul is in use, so we have to warn the user
        */
        if (!array_key_exists("CANNOT_UNINSTALL_IN_USE_TMPL", $MESSAGE['GENERIC'])) {
            $add = $oAddon->numRows() == 1 ? "this page" : "these pages";
            $msg_template_str  = "<br /><br />{{type}} <b>{{type_name}}</b> could not be uninstalled because it is still in use on {{pages}}";
            $msg_template_str .= ":<br /><i>click for editing.</i><br /><br />";
        } else {
            $msg_template_str = $MESSAGE['GENERIC_CANNOT_UNINSTALL_IN_USE_TMPL'];
            $temp = explode(";",$MESSAGE['GENERIC_CANNOT_UNINSTALL_IN_USE_TMPL_PAGES']);
            $add = $oAddon->numRows() == 1 ? $temp[0] : $temp[1];
        }
        /**
        *    The template-string for displaying the Page-Titles ... in this case as a link
        */
        $page_template_str = "- <b><a href='../pages/sections.php?page_id={{id}}'>{{title}}</a></b><br />";

        $values = array ('type' => 'Modul', 'type_name' => $sAddonsFile, 'pages' => $add );
        $msg = replace_all ( $msg_template_str,  $values );

        $page_names = "";

        while ($data = $oAddon->fetchRow(MYSQLI_ASSOC) ) {
            $sql  = 'SELECT `page_title` FROM `'.TABLE_PREFIX.'pages` '
                  . 'WHERE `page_id`= '.(int)$data['page_id'];
            $oPage = $database->query($sql);
            $aPage = $oPage->fetchRow( MYSQLI_ASSOC );
            $aPageInfo = array(
                'id'    => $data['page_id'], 
                'title' => $aPage['page_title']
            );

            $page_names .= replace_all ( $page_template_str, $aPageInfo );
        }

        /**
        *    Printing out the error-message and die().
        */
        $admin->print_error(str_replace ($TEXT['FILE'], "Modul", $MESSAGE['GENERIC_CANNOT_UNINSTALL_IN_USE']).$msg.$page_names);
    }
} else {
    $admin->print_error($MESSAGE['GENERIC_CANNOT_UNINSTALL']);
}
// Check if we have permissions on the directory
if(!is_writable(WB_PATH.'/modules/'.$sAddonsFile)) {
    $admin->print_error($MESSAGE['GENERIC_CANNOT_UNINSTALL']);
}

// Run the modules uninstall script if there is one
if(file_exists(WB_PATH.'/modules/'.$sAddonsFile.'/uninstall.php')) {
    require(WB_PATH.'/modules/'.$sAddonsFile.'/uninstall.php');
}

// Try to delete the module dir
if(!rm_full_dir(WB_PATH.'/modules/'.$sAddonsFile)) {
    $admin->print_error($MESSAGE['GENERIC_CANNOT_UNINSTALL']);
} else {
    // Remove entry from DB
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'addons` '
          . 'WHERE `type` = \'module\' '
          .   'AND `directory` = \''.$database->escapeString($sAddonsFile).'\' ';
    $database->query($sql);
}

// Print success message
$admin->print_success($MESSAGE['GENERIC_UNINSTALLED']);

// Print admin footer
$admin->print_footer();

<?php
/**
 *
 * @category        admin
 * @package         modules
 * @author          Ryan Djurovich, Christian Sommer, WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: manual_install.php 1603 2012-02-08 03:08:19Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/admin/modules/manual_install.php $
 * @lastmodified    $Date: 2012-02-08 04:08:19 +0100 (Mi, 08. Feb 2012) $
 *
 */

/**
 * check if there is anything to do
 */

/**
 * check if user has permissions to access this file
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

// check user permissions for admintools (redirect users with wrong permissions)
$admin = new admin('Admintools', 'admintools', false, false);

if (!(isset($_POST['action']) && in_array($_POST['action'], array('install', 'upgrade', 'uninstall')))) { die(header('Location: index.php?advanced')); }
if (!(isset($_POST['file']) && $_POST['file'] != '' && (strpos($_POST['file'], '..') === false))){  die(header('Location: index.php?advanced'));  }

$sCallingScript = $_SERVER["SCRIPT_NAME"];

$js_back = ADMIN_URL . '/modules/index.php?advanced';

if( !$admin->checkFTAN() )
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back);
}

if ($admin->get_permission('admintools') == false) { 
    $admin->print_header();
    $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
  }

// check if the referer URL if available
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] :
    (isset($HTTP_SERVER_VARS['HTTP_REFERER']) ? $HTTP_SERVER_VARS['HTTP_REFERER'] : '');
$referer = '';
// if referer is set, check if script was invoked from "admin/modules/index.php"
$required_url = ADMIN_URL . '/modules/index.php';
if ($referer != '' && (!(strpos($referer, $required_url) !== false || strpos($referer, $required_url) !== false))) 
{ 
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back);
}

// include WB functions file
require_once(WB_PATH . '/framework/functions.php');

// load WB language file
require_once(WB_PATH . '/languages/' . LANGUAGE .'.php');

// create Admin object with admin header
$admin = new admin('Addons', '', true, false);
$aValideActions = array( 'uninstall', 'install', 'upgrade' );

/**
 * Manually execute the specified module file (install.php, upgrade.php or uninstall.php)
 */
//$sModName = ($_POST['file']);
// Check if user selected module
if(!isset($_POST['file']) || $_POST['file'] == "") {
    $admin->print_error( $MESSAGE['GENERIC_FORGOT_OPTIONS'], $js_back );
} else {
    $sAddonName = $admin->StripCodeFromText($_POST['file']);
}

$sAction = $admin->StripCodeFromText( $_POST['action'] );
$sAction = ( in_array($sAction, $aValideActions) ? $sAction : 'upgrade' );

// Extra protection
if(trim($sAddonName) == '') {
    $admin->print_error($MESSAGE['GENERIC_ERROR_OPENING_FILE'], $js_back );
}
// check whether the module is core
$aPreventFromUninstall = array ( 'captcha_control', 'jsadmin', 'output_filter', 'wysiwyg', 'menu_link' );
if(
    $sAction == 'uninstall' &&
    preg_match('/'.$sAddonsFile.'/si', implode('|', $aPreventFromUninstall ))
) {
    $temp = array ('name' => $file );
    $msg = replace_all( $MESSAGE['MEDIA_CANNOT_DELETE_DIR'], $temp );
    $admin->print_error( $msg );
}

// check if specified module folder exists
$sAddonRelPath = '/modules/'.$sAddonName;
// let the old variablename if module use it

if (!file_exists( WB_PATH.$sAddonRelPath.'/'.$sAction. '.php'))
{
    $admin->print_header();
    $admin->print_error($TEXT['NOT_FOUND'].': <tt>"'.$sAddonName.'/'.$sAction.'.php"</tt> ', $js_back);
}
// include modules install.php script
if( in_array($sAction, $aValideActions) ) {
    require(WB_PATH.$sAddonRelPath . '/' . $sAction . '.php');
}
// load module info into database and output status message
load_module(WB_PATH.$sAddonRelPath, false);
$msg = $TEXT['EXECUTE'] . ': <tt>"'.$sAddonName.'/'.$sAction.'.php"</tt>';

switch ($sAction)
{
    case 'install':
        $admin->print_success($msg, $js_back);
        break;

    case 'upgrade':
        upgrade_module($sAddonName, false);
        $admin->print_success($msg, $js_back);
        break;
    
    case 'uninstall':
        $admin->print_success($msg, $js_back);
        break;
}

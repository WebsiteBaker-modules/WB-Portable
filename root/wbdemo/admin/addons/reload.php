<?php
/**
 *
 * @category        admin
 * @package         addons
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: reload.php 1603 2012-02-08 03:08:19Z Luisehahne $
 * @filesource        $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/admin/addons/reload.php $
 * @lastmodified    $Date: 2012-02-08 04:08:19 +0100 (Mi, 08. Feb 2012) $
 *
 */

/**
 * check if there is anything to do
 */
$post_check = array('reload_modules', 'reload_templates', 'reload_languages');
foreach ($post_check as $index => $key) {
    if (!isset($_POST[$key])) unset($post_check[$index]);
}
if (count($post_check) == 0) die(header('Location: index.php?advanced'));

/**
 * check if user has permissions to access this file
 */
// include WB configuration file and WB admin class
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// check user permissions for admintools (redirect users with wrong permissions)
$admin = new admin('Admintools', 'admintools', false, false);

if ($admin->get_permission('admintools') == false) die(header('Location: ../../index.php'));

// check if the referer URL if available
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] :
    (isset($HTTP_SERVER_VARS['HTTP_REFERER']) ? $HTTP_SERVER_VARS['HTTP_REFERER'] : '');
$referer = '';
// if referer is set, check if script was invoked from "admin/modules/index.php"
$required_url = ADMIN_URL . '/addons/index.php';
if ($referer != '' && (!(strpos($referer, $required_url) !== false || strpos($referer, $required_url) !== false)))
    die(header('Location: ../../index.php'));

// include WB functions file
require_once(WB_PATH . '/framework/functions.php');

// load WB language file
require_once(WB_PATH . '/languages/' . LANGUAGE .'.php');

// create Admin object with admin header
$admin = new admin('Addons', '', false, false);
$js_back = ADMIN_URL . '/addons/index.php?advanced';

if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
}

/**
 * delete no existing addons in table
 */
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
      . 'ORDER BY `type`, `directory` ';
if ( $oAddons = $database->query( $sql ) ) {
    while ( $aAddon = $oAddons->fetchRow( MYSQLI_ASSOC ) ) {
        $delAddon = 'DELETE  FROM `'.TABLE_PREFIX.'addons` WHERE `addon_id`='.(int)$aAddon['addon_id'];
        $sAddonFile = WB_PATH.'/'.$aAddon['type'].'s/'.$aAddon['directory'];
        switch ($aAddon['type']):
            case 'language':
                if ( !file_exists( $sAddonFile.'.php' ) )
                { 
                    $oDelResult = $database->query( $delAddon );
                }
                break;
            default:
                if ( !file_exists( $sAddonFile ) )
                { 
                    $oDelResult = $database->query( $delAddon );
//                    echo $sAddonFile.'<br />';
                }
            break;
        endswitch;
    }
}
/**
 * 
 * Reload all specified Addons
 */
$msg = array();
$table = TABLE_PREFIX . 'addons';

foreach ($post_check as $key) {
    switch ($key) {
        case 'reload_modules':
            $aAddonList = glob(WB_PATH.'/modules/*', GLOB_ONLYDIR );
            foreach( $aAddonList as $sAddonFile ) {
                if (is_readable( $sAddonFile )) {
                    load_module( $sAddonFile );
                }
            }
            // add success message
            $msg[] = $MESSAGE['ADDON_MODULES_RELOADED'];
            unset($aAddonList);
            break;

        case 'reload_templates':
            $aAddonList = glob(WB_PATH.'/templates/*', GLOB_ONLYDIR );
            foreach( $aAddonList as $sAddonFile ) {
                if (is_readable( $sAddonFile )) {
                    load_template( $sAddonFile );
                }
            }
            // add success message
            $msg[] = $MESSAGE['ADDON_TEMPLATES_RELOADED'];
            unset($aAddonList);
            break;

        case 'reload_languages':
            $aAddonList = glob(WB_PATH.'/languages/*.php' );
            foreach( $aAddonList as $sAddonFile ) {
                if (is_readable( $sAddonFile )) {
                    load_language( $sAddonFile );
                }
            }
            // add success message
            $msg[] = $MESSAGE['ADDON_LANGUAGES_RELOADED'];
            unset($aAddonList);
            break;

    }
}

// output success message
$admin->print_header();
$admin->print_success(implode($msg, '<br />'), $js_back);
$admin->print_footer();

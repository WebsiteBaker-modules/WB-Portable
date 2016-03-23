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
 * @version         $Id: index.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/modules/index.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
 */

// Print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
$admin = new admin('Addons', 'modules');
// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('modules.htt')));
// $template->debug = true;
$template->set_file ('page', 'modules.htt');
$template->set_block('page', 'main_block', 'main');

$template->set_block('main_block', 'module_install_block', 'module_install');
// Insert values into module list
$template->set_block('main_block', 'module_detail_block', 'module_detail');
$template->set_block('module_detail_block', 'module_detail_select_block', 'module_detail_select');

$template->set_block('main_block', 'module_uninstall_block', 'module_uninstall');
$template->set_block('module_uninstall_block', 'module_uninstall_select_block', 'module_uninstall_select');
$aPreventFromUninstall = array ( 'captcha_control', 'jsadmin', 'output_filter', 'wysiwyg', 'menu_link' );
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
      . 'WHERE `type` =\'module\''
      . 'ORDER BY `name`';
if($oAddons = $database->query($sql)) {

    while ($aAddon = $oAddons->fetchRow( MYSQLI_ASSOC )) {
        if( !$admin->get_permission( $aAddon['directory'], 'module' )) { continue; }
        $template->set_var('VALUE', $aAddon['directory']);
        $template->set_var('NAME', $aAddon['name']);
        $template->parse('module_detail_select', 'module_detail_select_block', true);
        if (!preg_match('/'.$aAddon['directory'].'/si', implode('|', $aPreventFromUninstall))) {
            $template->set_var('UNINSTALL_VALUE', $aAddon['directory']);
            $template->set_var('UNINSTALL_NAME', $aAddon['name']);
            $template->parse('module_uninstall_select', 'module_uninstall_select_block', true);
        }
    }
}

$show_block = false;
$template->set_block('main_block', 'module_advanced_block', 'module_advanced');
$template->set_block('module_advanced_block', 'manuell_install_block', 'manuell_install');
$template->set_block('module_advanced_block', 'manuell_upgrade_block', 'manuell_upgrade');
$template->set_block('module_advanced_block', 'manuell_uninstall_block', 'manuell_uninstall');
// Insert modules which includes a install.php file to install list
$module_files = glob(WB_PATH . '/modules/*', GLOB_ONLYDIR|GLOB_NOSORT );
natcasesort($module_files);
$template->set_block('manuell_install_block', 'manuell_install_select_block', 'manuell_install_select');
foreach ($module_files as $index => $sAddsonsPath) 
{
    if( !$admin->get_permission( basename($sAddsonsPath), 'module' )) { continue; }
    if (is_dir($sAddsonsPath)) {
        if (is_readable($sAddsonsPath . '/info.php')) {
           require $sAddsonsPath . '/info.php';
        }
        if (file_exists($sAddsonsPath . '/install.php')) {
            $show_block = true;
            $template->set_var('INSTALL_VISIBLE', '');
            $template->set_var('INSTALL_VALUE', basename($sAddsonsPath));
            $template->set_var('INSTALL_NAME', ( @$module_name ?: basename($sAddsonsPath)) );
            $template->parse('manuell_install_select', 'manuell_install_select_block', true);
        } else {
//          echo ''.basename($sAddsonsPath).'/install.php<br />';
        }
    } else {
        unset($module_files[$index]);
    }
}
    $template->set_block('manuell_upgrade_block', 'manuell_upgrade_select_block', 'manuell_upgrade_select');
    $template->set_block('manuell_uninstall_block', 'manuell_uninstall_select_block', 'manuell_uninstall_select');
    $oAddons->rewind();
    while ($aAddon = $oAddons->fetchRow( MYSQLI_ASSOC )) {
        if( !$admin->get_permission( $aAddon['directory'], 'module' )) { continue; }
        $sAddsonsPath = WB_PATH.'/modules/'.$aAddon['directory'];
        if (file_exists($sAddsonsPath.'/upgrade.php')) {
            $show_block = true;
            $template->set_var('UPGRADE_VISIBLE', '');
            $template->set_var('UPGRADE_VALUE', $aAddon['directory']);
            $template->set_var('UPGRADE_NAME', $aAddon['name']);
            $template->parse('manuell_upgrade_select', 'manuell_upgrade_select_block', true);
        } else {
//          echo ''.$sAddsonsPath.'/upgrade.php<br />';
        }
        if (!preg_match('/'.$aAddon['directory'].'/si', implode('|', $aPreventFromUninstall))) {
            $show_block = true;
            $template->set_var('UNINSTALL_VISIBLE', '');
            $template->set_var('UNINSTALL_VALUE', $aAddon['directory']);
            $template->set_var('UNINSTALL_NAME', $aAddon['name']);
            $template->parse('manuell_uninstall_select', 'manuell_uninstall_select_block', true);
        } else {
//          echo ''.$sAddsonsPath.'/uninstall.php<br />';
        }
    }

// Insert permissions values and show or hidden blocks
if($admin->get_permission('modules_install') != true) {
    $template->set_block ('module_install', '');
    $template->set_block ('manuell_install', '');
} else {
    $template->parse('module_install', 'module_install_block', true);
    $template->parse('manuell_install', 'manuell_install_block', true);
}
if($admin->get_permission('modules_uninstall') != true) {
    $template->set_block ('module_uninstall', '');
    $template->set_block ('manuell_uninstall', '');
} else {
    $template->parse('module_uninstall', 'module_uninstall_block', true);
    $template->parse('manuell_uninstall', 'manuell_uninstall_block', true);
}
if($admin->get_permission('modules_view') != true) {
    $template->set_block('module_detail', '');
    $template->set_block('manuell_upgrade', '');
} else {
    $template->parse('module_detail', 'module_detail_block', true);
    $template->parse('manuell_upgrade', 'manuell_upgrade_block', true);
}
// only show block if there is something to show
if(!$show_block || count($module_files) == 0 || !isset($_GET['advanced']) || $admin->get_permission('admintools') != true) {
    $template->set_block('module_advanced', '');
} else {
    $template->parse('module_advanced', 'module_advanced_block', true);
}

$template->set_block('main_block', 'addon_template_block', 'addon_template');
if($admin->get_permission('templates_view') != true) {
    $template->set_block ('addon_template', '');
} else {
    $template->parse('addon_template', 'addon_template_block', true);
}

$template->set_block('main_block', 'addon_language_block', 'addon_language');
if($admin->get_permission('languages_view') != true) {
    $template->set_block ('addon_language', '');
} else {
    $template->parse('addon_language', 'addon_language_block', true);
}

$template->set_block('main_block', 'addon_module_block', 'addon_module');
if($admin->get_permission('admintools') != true) {
    $template->set_block ('addon_module', '');
} else {
    $template->parse('addon_module', 'addon_module_block', true);
}

// Insert language headings
$template->set_var(array(
                    'HEADING_INSTALL_MODULE' => $HEADING['INSTALL_MODULE'],
                    'HEADING_UNINSTALL_MODULE' => $HEADING['UNINSTALL_MODULE'],
                    'OVERWRITE_NEWER_FILES' => $MESSAGE['ADDON_OVERWRITE_NEWER_FILES'],
                    'HEADING_MODULE_DETAILS' => $HEADING['MODULE_DETAILS'],
                    'HEADING_INVOKE_MODULE_FILES' => $HEADING['INVOKE_MODULE_FILES']
                    )
                );
// insert urls
$template->set_var(array(
                    'ADMIN_URL' => ADMIN_URL,
                    'WB_URL' => WB_URL,
                    'THEME_URL' => THEME_URL,
                    'FTAN' => $admin->getFTAN()
                    )
                );
// Insert language text and messages
$template->set_var(array(
    'URL_TEMPLATES'  => $admin->get_permission('templates') ? ADMIN_URL . '/templates/index.php' : '#',
    'URL_LANGUAGES'  => $admin->get_permission('languages') ? ADMIN_URL . '/languages/index.php'  : '#',
    'URL_ADVANCED'   => $admin->get_permission('admintools') ? ADMIN_URL . '/modules/index.php?advanced' : '#',
    'MENU_LANGUAGES' => $admin->get_permission('languages') ? $MENU['LANGUAGES'] : '&#160;&#160;&#160;',
    'MENU_TEMPLATES' => $admin->get_permission('templates') ? $MENU['TEMPLATES'] : '&#160;&#160;&#160;',
    'TEXT_ADVANCED'  => $admin->get_permission('admintools') ? $TEXT['ADVANCED'] : '&#160;&#160;&#160;',
    'TEXT_INSTALL'   => $TEXT['INSTALL'],
    'TEXT_UNINSTALL' => $TEXT['UNINSTALL'],
    'TEXT_VIEW_DETAILS'  => $TEXT['VIEW_DETAILS'],
    'TEXT_PLEASE_SELECT' => $TEXT['PLEASE_SELECT'],
    'TEXT_MANUAL_INSTALLATION' => $MESSAGE['ADDON_MANUAL_INSTALLATION'],
    'TEXT_MANUAL_INSTALLATION_WARNING' => $MESSAGE['ADDON_MANUAL_INSTALLATION_WARNING'],
    'TEXT_EXECUTE' => $TEXT['EXECUTE'],
    'TEXT_FILE'    => $TEXT['FILE']
    )
);

// Parse template object
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');

// Print admin footer
$admin->print_footer();

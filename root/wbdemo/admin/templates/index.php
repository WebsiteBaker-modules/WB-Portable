<?php
/**
 *
 * @category        admin
 * @package         templates
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: index.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/templates/index.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
 */

// Print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
$admin = new admin('Addons', 'templates');

// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('templates.htt')));
// $template->debug = true;
$template->set_file('page', 'templates.htt');
$template->set_block('page', 'main_block', 'main');
$template->set_var('FTAN', $admin->getFTAN());

// Insert values into template list
$template->set_block('main_block', 'template_detail_block', 'template_detail');
$template->set_block('template_detail_block', 'template_detail_select_block', 'template_detail_select');
$template->set_block('main_block', 'template_uninstall_block', 'template_uninstall');
$template->set_block('template_uninstall_block', 'template_uninstall_select_block', 'template_uninstall_select');
$aPreventFromUninstall = array (' wb_theme ', ' WbTheme ', ' default ', ' default_theme ', ' DefaultTheme ');
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
      . 'WHERE `type` = \'template\' '
//      .   'AND `directory `NOT IN ('.$aPreventFromUninstall.') '
      . 'ORDER BY `name`';
if($oAddons = $database->query( $sql )) {
    while($aAddon = $oAddons->fetchRow( MYSQLI_ASSOC )) {
        if( !$admin->get_permission( $aAddon['directory'], 'template' )) { continue; } 
        $template->set_var('DETAIL_VALUE', $aAddon['directory']);
        $template->set_var('DETAIL_NAME', $aAddon['name']);
        $template->parse('template_detail_select', 'template_detail_select_block', true);
        if (!preg_match('/'.$aAddon['directory'].'/si', implode('|', $aPreventFromUninstall))) {
//        if ( file_exists( WB_PATH.'/templates/'.$aAddon['directory'].'/uninstall.php') ) {
            $template->set_var('UNINSTALL_VALUE', $aAddon['directory']);
            $template->set_var('UNINSTALL_NAME', $aAddon['name']);
            $template->parse('template_uninstall_select', 'template_uninstall_select_block', true);
        }
    }
}

// Insert permissions values
$template->set_block('main_block', 'template_install_block', 'template_install');
if($admin->get_permission('templates_install') != true) {
    $template->set_var('DISPLAY_INSTALL', '');
    $template->set_block('template_install', '');
} else {
    $template->parse('template_install', 'template_install_block', true);
}
if($admin->get_permission('templates_uninstall') != true) {
    $template->set_var('DISPLAY_UNINSTALL', '');
    $template->set_block('template_uninstall', '');
} else {
    $template->parse('template_uninstall', 'template_uninstall_block', true);
}
if($admin->get_permission('templates_view') != true) {
    $template->set_var('DISPLAY_LIST', '');
    $template->set_block('template_detail', '');
} else {
    $template->parse('template_detail', 'template_detail_block', true);
}

$template->set_block('main_block', 'addon_module_block', 'addon_module');
if($admin->get_permission('modules_view') != true) {
    $template->set_block ('addon_module', '');
} else {
    $template->parse('addon_module', 'addon_module_block', true);
}

$template->set_block('main_block', 'addon_language_block', 'addon_language');
if($admin->get_permission('languages_view') != true) {
    $template->set_block ('addon_language', '');
} else {
    $template->parse('addon_language', 'addon_language_block', true);
}

$template->set_block('main_block', 'addon_template_block', 'addon_template');
if($admin->get_permission('admintools') != true) {
    $template->set_block ('addon_template', '');
} else {
    $template->parse('addon_template', 'addon_template_block', true);
}



// Insert language headings
$template->set_var(array(
                    'HEADING_INSTALL_TEMPLATE' => $HEADING['INSTALL_TEMPLATE'],
                    'HEADING_UNINSTALL_TEMPLATE' => $HEADING['UNINSTALL_TEMPLATE'],
                    'HEADING_TEMPLATE_DETAILS' => $HEADING['TEMPLATE_DETAILS']
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
    'URL_MODULES' => $admin->get_permission('modules') ? ADMIN_URL . '/modules/index.php'  : '',
    'URL_LANGUAGES' => $admin->get_permission('languages') ? ADMIN_URL . '/languages/index.php'  : '',
    'URL_ADVANCED' => '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;',
    'MENU_LANGUAGES' => $admin->get_permission('languages') ? $MENU['LANGUAGES'] : '&#160;&#160;&#160;',
    'MENU_MODULES' => $admin->get_permission('modules') ? $MENU['MODULES'] : '&#160;&#160;&#160;',
    'TEXT_INSTALL' => $TEXT['INSTALL'],
    'TEXT_UNINSTALL' => $TEXT['UNINSTALL'],
    'TEXT_VIEW_DETAILS' => $TEXT['VIEW_DETAILS'],
    'TEXT_PLEASE_SELECT' => $TEXT['PLEASE_SELECT'],
    'CHANGE_TEMPLATE_NOTICE' => $MESSAGE['TEMPLATES']['CHANGE_TEMPLATE_NOTICE']
    )
);

// Parse template object
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');

// Print admin footer
$admin->print_footer();

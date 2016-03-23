<?php
/**
 *
 * @category        admin
 * @package         languages
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: index.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/languages/index.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 * @description
 *
 */

// Print admin header
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
$admin = new admin('Addons', 'languages');

// Setup template object, parse vars to it, then parse it
// Create new template object
$template = new Template(dirname($admin->correct_theme_source('languages.htt')));
// $template->debug = true;
$template->set_file('page', 'languages.htt');
$template->set_block('page', 'main_block', 'main');

// Insert values into language list
$template->set_block('main_block', 'language_detail_block', 'language_detail');
$template->set_block('language_detail_block', 'language_detail_select_block', 'language_detail_select');
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
      . 'WHERE `type` =\'language\''
      . 'ORDER BY `directory`';
if($oAddons = $database->query($sql)) {
    while($aAddon = $oAddons->fetchRow( MYSQLI_ASSOC )) {
        if( !$admin->get_permission( $aAddon['directory'], 'language' )) { continue; }
        $template->set_var('VALUE', $aAddon['directory']);
        $template->set_var('NAME', $aAddon['name'].' ('.$aAddon['directory'].')');
        $template->parse('language_detail_select', 'language_detail_select_block', true);
    }
}
$template->set_block('main_block', 'language_uninstall_block', 'language_uninstall');
    $template->set_block('language_uninstall_block', 'language_uninstall_select_block', 'language_uninstall_select');
    $oAddons->rewind();
    while($aAddon = $oAddons->fetchRow( MYSQLI_ASSOC )) {
        if( !$admin->get_permission( $aAddon['directory'], 'language' )) { continue; }
        $template->set_var('VALUE', $aAddon['directory']);
        $template->set_var('NAME', $aAddon['name'].' ('.$aAddon['directory'].')');
        $template->parse('language_uninstall_select', 'language_uninstall_select_block', true);
    }


// Insert permissions values
$template->set_block('main_block', 'language_install_block', 'language_install');
if($admin->get_permission('languages_install') != true) {
    $template->set_var('DISPLAY_INSTALL', '');
    $template->set_block('language_install', '');
} else {
    $template->parse('language_install', 'language_install_block', true);
}
if($admin->get_permission('languages_uninstall') != true) {
    $template->set_var('DISPLAY_UNINSTALL', '');
    $template->set_block('language_uninstall', '');
} else {
    $template->parse('language_uninstall', 'language_uninstall_block', true);
}
if($admin->get_permission('languages_view') != true) {
    $template->set_var('DISPLAY_LIST', '');
    $template->set_block('language_detail', '');
} else {
    $template->parse('language_detail', 'language_detail_block', true);
}

$template->set_block('main_block', 'addon_template_block', 'addon_template');
if($admin->get_permission('templates_view') != true) {
    $template->set_block ('addon_template', '');
} else {
    $template->parse('addon_template', 'addon_template_block', true);
}

$template->set_block('main_block', 'addon_module_block', 'addon_module');
if($admin->get_permission('modules_view') != true) {
    $template->set_block ('addon_module', '');
} else {
    $template->parse('addon_module', 'addon_module_block', true);
}

$template->set_block('main_block', 'addon_language_block', 'addon_language');
if($admin->get_permission('admintools') != true) {
    $template->set_block ('addon_language', '');
} else {
    $template->parse('addon_language', 'addon_language_block', true);
}

// Insert language headings
$template->set_var(array(
                'HEADING_INSTALL_LANGUAGE' => $HEADING['INSTALL_LANGUAGE'],
                'HEADING_UNINSTALL_LANGUAGE' => $HEADING['UNINSTALL_LANGUAGE'],
                'HEADING_LANGUAGE_DETAILS' => $HEADING['LANGUAGE_DETAILS']
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
    'URL_MODULES' => $admin->get_permission('modules') ? ADMIN_URL . '/modules/index.php' : '#',
    'URL_TEMPLATES' => $admin->get_permission('templates') ? ADMIN_URL . '/templates/index.php' : '#',
    'URL_ADVANCED' => '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;',
    'MENU_MODULES' => $admin->get_permission('modules') ? $MENU['MODULES'] : '&#160;&#160;&#160;',
    'MENU_TEMPLATES' => $admin->get_permission('templates') ? $MENU['TEMPLATES'] : '&#160;&#160;&#160;',
    'TEXT_INSTALL' => $TEXT['INSTALL'],
    'TEXT_UNINSTALL' => $TEXT['UNINSTALL'],
    'TEXT_VIEW_DETAILS' => $TEXT['VIEW_DETAILS'],
    'TEXT_PLEASE_SELECT' => $TEXT['PLEASE_SELECT']
    )
);

// Parse template object
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');

// Print admin footer
$admin->print_footer();

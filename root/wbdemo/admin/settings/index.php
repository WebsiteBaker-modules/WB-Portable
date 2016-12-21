<?php
/**
 *
 * @category        admin
 * @package         settings
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: index.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/settings/index.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
 */
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
/*---------------------------------------------------------------------------------------------------*/
$bAdvanced = intval ((@intval($_GET['advanced'])) ?: 0);
if($bAdvanced) {
    $admin = new admin('Settings', 'settings_advanced');
} else {
    $admin = new admin('Settings', 'settings_basic');
}
/*---------------------------------------------------------------------------------------------------*/
// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');
require_once(WB_PATH.'/framework/functions-utf8.php');
$cfg = array(
    'website_signature' => (defined('WEBSITE_SIGNATURE')?WEBSITE_SIGNATURE:'')
);
foreach($cfg as $key=>$value) {
    db_update_key_value('settings', $key, $value);
}

// Setup template object, parse vars to it, then parse it
// Create new template object
    $template = new Template(dirname($admin->correct_theme_source('settings.htt')), 'remove');
// $template->debug = true;
    $template->set_file('page',  'settings.htt');
    $template->set_block('page', 'main_block', 'main');
/*---------------------------------------------------------------------------------------------------*/
    $template->set_block('main_block', 'show_page_level_limit_block', 'show_page_level_limit');
    $template->set_block('main_block', 'show_checkbox_1_block',       'show_checkbox_1');
    $template->set_block('main_block', 'show_checkbox_2_block',       'show_checkbox_2');
    $template->set_block('main_block', 'show_checkbox_3_block',       'show_checkbox_3');
    $template->set_block('main_block', 'show_redirect_timer_block',   'show_redirect_timer');
    $template->set_block('main_block', 'show_php_error_level_block',  'show_php_error_level');
    $template->set_block('main_block', 'show_wysiwyg_block',          'show_wysiwyg');
    $template->set_block('main_block', 'show_charset_block',          'show_charset');
    $template->set_block('main_block', 'show_search_block',           'show_search');
    $template->set_block('main_block', 'show_access_block',           'show_access');
    $template->set_block('main_block', 'show_chmod_js_block',         'show_chmod_js');
    $template->set_block('main_block', 'show_setting_js_block',       'show_setting_js');
/*---------------------------------------------------------------------------------------------------*/
// Query current settings in the db, then loop through them and print them
$query = "SELECT * FROM `".TABLE_PREFIX."settings`";
if($results = $database->query($query)) {
    $aSetting = array();
    $settings = array();
    while($aSetting = $results->fetchRow(MYSQLI_ASSOC))
    {
        $setting_name = $aSetting['name'];
        $setting_value = ( $setting_name != 'wbmailer_smtp_password' ) ? htmlspecialchars($aSetting['value']) : $aSetting['value'];
        $settings[$setting_name] = $setting_value;
        $template->set_var(strtoupper($setting_name),$setting_value);
    }
} else {

}
$SecureTokenLifeTime = $admin->getTokenLifeTime();
array_walk(
    $SecureTokenLifeTime,
    function (&$aItem) {
        $aItem /= 60;
    }
);
$template->set_var( $SecureTokenLifeTime );
/*---------------------------------------------------------------------------------------------------*/
$template->set_var('EDITOR_WEBSITE_HEADER', '');
$template->set_var('EDITOR_WEBSITE_FOOTER', '');
$template->set_var('EDITOR_WEBSITE_SIGNATURE', '');
if (defined('WYSIWYG_EDITOR') && is_readable(WB_PATH.'/modules/'.WYSIWYG_EDITOR.'/include1.php'))
{
    require(WB_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php');
    $template->set_block('main_block', 'show_website_header_block', 'show_website_header');
    if (defined('WYSIWYG_EDITOR')) {
        $template->set_block('show_website_header', '');
        $WebsiteHeader = show_wysiwyg_editor('website_header','content_header', WEBSITE_HEADER,'100%','200', 'WB_Mini', true);
        $template->set_var('EDITOR_WEBSITE_HEADER', $WebsiteHeader);
    } else {
        $template->parse('show_website_header','show_website_header_block',true);
    }
    $template->set_block('main_block', 'show_website_footer_block', 'show_website_footer');
    if (defined('WYSIWYG_EDITOR')) {
        $template->set_block('show_website_footer', '');
        $WebsiteFooter = show_wysiwyg_editor('website_footer','content_footer', WEBSITE_FOOTER, '100%','200', 'WB_Mini', true);
        $template->set_var('EDITOR_WEBSITE_FOOTER', $WebsiteFooter);
    } else {
        $template->parse('show_website_footer','show_website_footer_block',true);
    }
//
    $template->set_block('main_block', 'show_website_signature_block', 'show_website_signature');
    if (defined('WYSIWYG_EDITOR')) {
        $template->set_block('show_website_signature', '');
        $WebsiteSignature = show_wysiwyg_editor('website_header','content_header', WEBSITE_SIGNATURE,'100%','200', 'WB_Mini', true);
        $template->set_var('EDITOR_WEBSITE_SIGNATURE', $WebsiteSignature);
    } else {
        $template->parse('show_website_signature','show_website_signature_block',true);
    }
}
/*---------------------------------------------------------------------------------------------------*/
// Do the same for settings stored in config file as with ones in db
$database_type = '';
$is_advanced = (boolean)$bAdvanced;
// Tell the browser whether or not to show advanced options
if($is_advanced)
{
    $template->set_var('DISPLAY_ADVANCED', '');
    $template->set_var('ADVANCED_FILE_PERMS_ID', 'file_perms_box');
    $template->set_var('BASIC_FILE_PERMS_ID', 'hide');
    $template->set_var('ADVANCED_VALUE', 1);
    $template->set_var('ADVANCED_BUTTON', '&lt;&lt; '.$TEXT['HIDE_ADVANCED']);
    $template->set_var('ADVANCED_LINK', 'index.php?advanced=0');
} else {
    $template->set_var('DISPLAY_ADVANCED', ' style="display: none;"');
    $template->set_var('BASIC_FILE_PERMS_ID', 'file_perms_box');
    $template->set_var('ADVANCED_FILE_PERMS_ID', 'hide');
    $template->set_var('ADVANCED_VALUE', 0);
    $template->set_var('ADVANCED_BUTTON', $TEXT['SHOW_ADVANCED'].' &gt;&gt;');
    $template->set_var('ADVANCED_LINK', 'index.php?advanced=1');
}
/*---------------------------------------------------------------------------------------------------*/
    $query = 'SELECT * FROM `'.TABLE_PREFIX.'search` WHERE `extra` = \'\'';
    $results = $database->query($query);
    // Query current settings in the db, then loop through them and print them
    while($aSearch = $results->fetchRow(MYSQLI_ASSOC))
    {
        $search_name = $aSearch['name'];
        $search_value = htmlspecialchars(($aSearch['value']));
        switch($search_name) {
            // Search header
            case 'header':
                $template->set_var('SEARCH_HEADER', $search_value);
            break;
            // Search results header
            case 'results_header':
                $template->set_var('SEARCH_RESULTS_HEADER', $search_value);
            break;
            // Search results loop
            case 'results_loop':
                $template->set_var('SEARCH_RESULTS_LOOP', $search_value);
            break;
            // Search results footer
            case 'results_footer':
                $template->set_var('SEARCH_RESULTS_FOOTER', $search_value);
            break;
            // Search no results
            case 'no_results':
                $template->set_var('SEARCH_NO_RESULTS', $search_value);
            break;
            // Search footer
            case 'footer':
                $template->set_var('SEARCH_FOOTER', $search_value);
            break;
            // Search module-order
            case 'module_order':
                $template->set_var('SEARCH_MODULE_ORDER', $search_value);
            break;
            // Search max lines of excerpt
            case 'max_excerpt':
                $template->set_var('SEARCH_MAX_EXCERPT', $search_value);
            break;
            // time-limit
            case 'time_limit':
                $template->set_var('SEARCH_TIME_LIMIT', $search_value);
            break;
            // Search template
            case 'template':
                $search_template = $search_value;
            break;
        }
    }
/*---------------------------------------------------------------------------------------------------*/
    $template->set_var(array(
                        'WB_URL' => WB_URL,
                        'THEME_URL' => THEME_URL,
                        'ADMIN_URL' => ADMIN_URL,
                     ));
    $template->set_var('FTAN', $admin->getFTAN());
/*---------------------------------------------------------------------------------------------------*/
    // Insert page level limits
    $template->set_block('show_page_level_limit_block', 'page_level_limit_list_block', 'page_level_limit_list');
    $template->set_var('PAGE_LEVEL_LIMIT', $settings['page_level_limit']);
    // if select list
    for($i = 1; $i <= 10; $i++)
    {
        $template->set_var('NUMBER', $i);
        $template->set_var('SELECTED', ((PAGE_LEVEL_LIMIT == $i) ? ' selected="selected"' : '') );
        $template->parse('page_level_limit_list', 'page_level_limit_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert groups into signup list
    $template->set_block('main_block', 'group_list_block', 'group_list');
    $sqlGroup = 'SELECT `group_id`, `name` FROM `'.TABLE_PREFIX.'groups` '
              . 'WHERE `group_id` != 1'
              . '';
    if($results = $database->query($sqlGroup))
    {
        while($group = $results->fetchRow(MYSQLI_ASSOC))
        {
            $template->set_var('ID', $group['group_id']);
            $template->set_var('NAME', $group['name']);
            $template->set_var('SELECTED', ((FRONTEND_SIGNUP == $group['group_id']) ? ' selected="selected"' : '') );
            $template->parse('group_list', 'group_list_block', true);
        }
    } else {
        $template->set_var('ID', 'disabled');
        $template->set_var('NAME', $MESSAGE['GROUPS']['NO_GROUPS_FOUND']);
        $template->parse('group_list', 'group_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert default error reporting values
    $template->set_block('show_php_error_level_block', 'error_reporting_list_block',  'error_reporting_list');
    require(ADMIN_PATH.'/interface/er_levels.php');
    foreach($ER_LEVELS AS $value => $title)
    {
        $template->set_var('VALUE', $value);
        $template->set_var('NAME', $title);
        $template->set_var('SELECTED', ((ER_LEVEL == $value) ? ' selected="selected"' : '') );
        $template->parse('error_reporting_list', 'error_reporting_list_block', true);
    }
/*-------------------------------------------------------------------------------------*/
    // Insert WYSIWYG modules
    $template->set_block('show_wysiwyg_block', 'wysiwyg_list_block', 'wysiwyg_list');
    $file='none';
    $module_name=$TEXT['NONE'];
    $template->set_var('FILE', $file);
    $template->set_var('NAME', $module_name);
    $template->set_var('SELECTED', ((!defined('WYSIWYG_EDITOR') || $file == WYSIWYG_EDITOR) ? ' selected="selected"' : '') );
    $template->parse('wysiwyg_list', 'wysiwyg_list_block', true);
    $sqlEditor  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
          . 'WHERE `type` = \'module\' '
          .   'AND `function` = \'wysiwyg\' '
          . 'ORDER BY `name`';
    if($result = $database->query($sqlEditor))
    {
        while($aWysiwyg = $result->fetchRow(MYSQLI_ASSOC))
        {
            $template->set_var('FILE', $aWysiwyg['directory']);
            $template->set_var('NAME', $aWysiwyg['name']);
            $template->set_var('SELECTED', ((!defined('WYSIWYG_EDITOR') || $aWysiwyg['directory'] == WYSIWYG_EDITOR) ? ' selected="selected"' : '') );
            $template->parse('wysiwyg_list', 'wysiwyg_list_block', true);
        }
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert language values
    $template->set_block('main_block', 'language_list_block', 'language_list');
    $sqlLang  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
              . 'WHERE `type` = \'language\' '
              . 'ORDER BY `directory`';
    if($result = $database->query($sqlLang))
    {
        while($aLang = $result->fetchRow(MYSQLI_ASSOC)) {
            $langIcons = (empty($aLang['directory']) ? 'none' : strtolower($aLang['directory']));
            $template->set_var('CODE',        $aLang['directory']);
            $template->set_var('NAME',        $aLang['name']);
            $template->set_var('FLAG',        THEME_URL.'/images/flags/'.$langIcons);
            $template->set_var('SELECTED',    (DEFAULT_LANGUAGE == $aLang['directory'] ? ' selected="selected"' : '') );
            $template->parse('language_list', 'language_list_block', true);
        }
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert default timezone values
    $template->set_block('main_block', 'timezone_list_block', 'timezone_list');
    require(ADMIN_PATH.'/interface/timezones.php');
    foreach($TIMEZONES AS $hour_offset => $title)
    {
        // Make sure we dont list "System Default" as we are setting this value!
        if($hour_offset != '-20') {
            $template->set_var('VALUE', $hour_offset);
            $template->set_var('NAME', $title);
            $template->set_var('SELECTED', ( (DEFAULT_TIMEZONE == $hour_offset*60*60)?' selected="selected"':'' ) );
            $template->parse('timezone_list', 'timezone_list_block', true);
        }
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert default charset values
    $template->set_block('show_charset_block', 'charset_list_block', 'charset_list');
    require(ADMIN_PATH.'/interface/charsets.php');
    foreach($CHARSETS AS $code => $title) {
        $template->set_var('VALUE', $code);
        $template->set_var('NAME', $title);
        $template->set_var('SELECTED', ( (DEFAULT_CHARSET == $code)?' selected="selected"':'' ) );
        $template->parse('charset_list', 'charset_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert date format list
    $template->set_block('main_block', 'date_format_list_block', 'date_format_list');
    require(ADMIN_PATH.'/interface/date_formats.php');
    foreach($DATE_FORMATS AS $format => $title) {
        $format = str_replace('|', ' ', $format); // Add's white-spaces (not able to be stored in array key)
        if($format != 'system_default') {
            $template->set_var('VALUE', $format);
        } else {
            $template->set_var('VALUE', '');
        }
        $template->set_var('NAME', $title);
        $template->set_var('SELECTED', ( (DEFAULT_DATE_FORMAT == $format)?' selected="selected"':'' ) );
        $template->parse('date_format_list', 'date_format_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert time format list
    $template->set_block('main_block', 'time_format_list_block', 'time_format_list');
    require(ADMIN_PATH.'/interface/time_formats.php');
    foreach($TIME_FORMATS AS $format => $title) {
        $format = str_replace('|', ' ', $format); // Add's white-spaces (not able to be stored in array key)
        if($format != 'system_default') {
            $template->set_var('VALUE', $format);
        } else {
            $template->set_var('VALUE', '');
        }
        $template->set_var('NAME', $title);
        $template->set_var('SELECTED', ( (DEFAULT_TIME_FORMAT == $format)?' selected="selected"':'' ) );
        $template->parse('time_format_list', 'time_format_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert templates
    $template->set_block('main_block', 'template_list_block', 'template_list');
    $sqlTheme = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
              . 'WHERE `type` = \'template\' '
              .   'AND `function` != \'theme\' '
              . 'ORDER BY `name`';
    if($result = $database->query($sqlTheme)) {
//    $result = $database->query("SELECT * FROM `".TABLE_PREFIX."addons` WHERE `type` = 'template' AND `function` != 'theme' ORDER BY `name`");
//    if($result->numRows() > 0) {
        while($addon = $result->fetchRow( MYSQLI_ASSOC )) {
            $template->set_var('FILE', $addon['directory']);
            $template->set_var('NAME', $addon['name']);
            $template->set_var('SELECTED', (($addon['directory'] == DEFAULT_TEMPLATE)?' selected="selected"':'') );
            $template->parse('template_list', 'template_list_block', true);
        }
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert backend theme
    $template->set_block('main_block', 'theme_list_block', 'theme_list');
    $sqlTheme = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
              . 'WHERE `type` = \'template\' '
              .   'AND `function` = \'theme\' '
              . 'ORDER BY `name`';
    if($result = $database->query($sqlTheme)) {
        while($addon = $result->fetchRow( MYSQLI_ASSOC )) {
            $template->set_var('FILE', $addon['directory']);
            $template->set_var('NAME', $addon['name']);
            $template->set_var('SELECTED', (($addon['directory'] == DEFAULT_THEME)?' selected="selected"':'') );
            $template->parse('theme_list', 'theme_list_block', true);
        }
    }
/*---------------------------------------------------------------------------------------------------*/
// Insert templates for search settings
    $template->set_block('main_block', 'search_template_list_block', 'search_template_list');
    $search_template = ( ($search_template == DEFAULT_TEMPLATE) || ($search_template == '') ) ? '' : $search_template;
    $selected = ( ($search_template != DEFAULT_TEMPLATE) ) ?  ' selected="selected"' : '';
    $template->set_var(array(
            'FILE' => '',
            'NAME' => $TEXT['SYSTEM_DEFAULT'],
            'SELECTED' => $selected
        ));
    $template->parse('search_template_list', 'search_template_list_block', true);
    $sqlSearch = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
              . ' WHERE `type` = \'template\' '
              .    'AND `function` =\'template\' '
              . 'ORDER BY `name`';
    if ($result = $database->query($sqlSearch))
    {
        while($addon = $result->fetchRow(MYSQLI_ASSOC))
        {
            $template->set_var('FILE', $addon['directory']);
            $template->set_var('NAME', $addon['name']);
            $template->set_var('SELECTED', (($addon['directory'] == $search_template) ? ' selected="selected"' :  '') );
            $template->parse('search_template_list', 'search_template_list_block', true);
        }
    }
/*--------------------------------------------------------------------------------------------------------*/
    // Insert permissions values
    if($admin->get_permission('settings_advanced') != true)
    {
        $template->set_var('DISPLAY_ADVANCED_BUTTON', 'hide');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if multiple menus feature is enabled
    if(defined('MULTIPLE_MENUS') && MULTIPLE_MENUS == true)
    {
        $template->set_var('MULTIPLE_MENUS_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('MULTIPLE_MENUS_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if page languages feature is enabled
    if(defined('PAGE_LANGUAGES') && PAGE_LANGUAGES == true)
    {
            $template->set_var('PAGE_LANGUAGES_ENABLED', ' checked="checked"');
    } else {
            $template->set_var('PAGE_LANGUAGES_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if warn_page_leave feature is enabled
    if (defined('WARN_PAGE_LEAVE') && WARN_PAGE_LEAVE == true)
    {
        $template->set_var('WARN_PAGE_LEAVE_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('WARN_PAGE_LEAVE_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if smart login feature is enabled
    if(defined('SMART_LOGIN') && SMART_LOGIN == true)
    {
        $template->set_var('SMART_LOGIN_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('SMART_LOGIN_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    /* Make's sure GD library is installed */
    if(extension_loaded('gd') && function_exists('imageCreateFromJpeg'))
    {
        $template->set_var('GD_EXTENSION_ENABLED', '');
    } else {
        $template->set_var('GD_EXTENSION_ENABLED', ' style="display: none;"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if section blocks feature is enabled
    if(defined('SECTION_BLOCKS') && SECTION_BLOCKS == true)
    {
        $template->set_var('SECTION_BLOCKS_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('SECTION_BLOCKS_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if homepage redirection feature is enabled
    if(defined('HOMEPAGE_REDIRECTION') && HOMEPAGE_REDIRECTION == true)
    {
        $template->set_var('HOMEPAGE_REDIRECTION_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('HOMEPAGE_REDIRECTION_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if debug mode feature is enabled
    if(defined('DEBUG') && DEBUG == true)
    {
        $template->set_var('DEBUG_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('DEBUG_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if token_fingerprint feature is enabled
    if(defined('SEC_TOKEN_FINGERPRINT') && SEC_TOKEN_FINGERPRINT == true)
    {
        $template->set_var('FINGERPRINT_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('FINGERPRINT_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out which server os should be checked   {DISPLAY_CHMOD}
    if(OPERATING_SYSTEM == 'linux')
    {
        $template->set_var('LINUX_SELECTED', ' checked="checked"');
        $template->set_var('DISPLAY_CHMOD', ' style="display: block;"');
    } elseif(OPERATING_SYSTEM == 'windows') {
        $template->set_var('WINDOWS_SELECTED', ' checked="checked"');
        $template->set_var('DISPLAY_CHMOD', ' style="display: none;"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if manage sections feature is enabled
    if(MANAGE_SECTIONS)
    {
        $template->set_var('MANAGE_SECTIONS_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('MANAGE_SECTIONS_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out which wbmailer routine should be checked
    $template->set_var(array(
                'TEXT_WBMAILER_DEFAULT_SETTINGS_NOTICE' => $TEXT['WBMAILER_DEFAULT_SETTINGS_NOTICE'],
                'TEXT_WBMAILER_DEFAULT_SENDER_MAIL' => $TEXT['WBMAILER_DEFAULT_SENDER_MAIL'],
                'TEXT_WBMAILER_DEFAULT_SENDER_NAME' => $TEXT['WBMAILER_DEFAULT_SENDER_NAME'],
                'TEXT_WBMAILER_NOTICE' => $TEXT['WBMAILER_NOTICE'],
                'TEXT_WBMAILER_FUNCTION' => $TEXT['WBMAILER_FUNCTION'],
                'TEXT_WBMAILER_SMTP_HOST' => $TEXT['WBMAILER_SMTP_HOST'],
                'TEXT_WBMAILER_PHP' => $TEXT['WBMAILER_PHP'],
                'TEXT_WBMAILER_SMTP' => $TEXT['WBMAILER_SMTP'],
                'TEXT_WBMAILER_SMTP_AUTH' => $TEXT['WBMAILER_SMTP_AUTH'],
                'TEXT_WBMAILER_SMTP_AUTH_NOTICE' => $TEXT['REQUIRED'].' '.$TEXT['WBMAILER_SMTP_AUTH'],
                'TEXT_WBMAILER_SMTP_USERNAME' => $TEXT['WBMAILER_SMTP_USERNAME'],
                'TEXT_WBMAILER_SMTP_PASSWORD' => $TEXT['WBMAILER_SMTP_PASSWORD'],
                'SMTP_AUTH_SELECTED' => ' checked="checked"'
                ));
    if(WBMAILER_ROUTINE == 'phpmail')
    {
        $template->set_var('PHPMAIL_SELECTED', ' checked="checked"');
        $template->set_var('SMTP_VISIBILITY', ' style="display: none;"');
        $template->set_var('SMTP_VISIBILITY_AUTH', '');
        // $template->set_var('SMTP_AUTH_SELECTED', '');
    } elseif(WBMAILER_ROUTINE == 'smtp')
    {
        $template->set_var('SMTPMAIL_SELECTED', ' checked="checked"');
        $template->set_var('SMTP_VISIBILITY', '');
        $template->set_var('SMTP_VISIBILITY_AUTH', '');
    }
//$template->set_var('SMTP_AUTH_SELECTED',( (WBMAILER_SMTP_AUTH === true) ?' checked="checked"':'') );
    $template->set_block('show_access_block', 'smtp_port_list_block', 'smtp_port_list');
    $aSmtpPorts = array( '25', '465', '587', '2525');
    foreach($aSmtpPorts as $sPort)
    {
        $template->set_var('VALUE', $sPort);
        $template->set_var('PNAME', $sPort);
        $template->set_var('SELECTED', ((WBMAILER_SMTP_PORT == $sPort) ? ' selected="selected"' : '') );
        $template->parse('smtp_port_list', 'smtp_port_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    $template->set_block('show_access_block', 'smtp_secure_list_block', 'smtp_secure_list');
    $aSmtpSecure = array( 'TLS', 'SSL' );
    foreach($aSmtpSecure as $sSecure)
    {
        $template->set_var('VALUE', $sSecure);
        $template->set_var('SNAME', $sSecure);
        $template->set_var('SELECTED', ((WBMAILER_SMTP_SECURE == $sSecure) ? ' selected="selected"' : '') );
        $template->parse('smtp_secure_list', 'smtp_secure_list_block', true);
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if intro feature is enabled
    if(INTRO_PAGE)
    {
        $template->set_var('INTRO_PAGE_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('INTRO_PAGE_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if frontend login feature is enabled
    if(FRONTEND_LOGIN)
    {
        $template->set_var('PRIVATE_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('PRIVATE_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if page trash feature is disabled, in-line, or separate
    if(PAGE_TRASH == 'disabled')
    {
        $template->set_var('PAGE_TRASH_DISABLED', ' checked="checked"');
        $template->set_var('DISPLAY_PAGE_TRASH_SEPARATE', 'display: none;');
    } elseif(PAGE_TRASH == 'inline')
    {
        $template->set_var('PAGE_TRASH_INLINE', ' checked="checked"');
        $template->set_var('DISPLAY_PAGE_TRASH_SEPARATE', 'display: none;');
    } elseif(PAGE_TRASH == 'separate')
    {
        $template->set_var('PAGE_TRASH_SEPARATE', ' checked="checked"');
        $template->set_var('DISPLAY_PAGE_TRASH_SEPARATE', 'display: inline;');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if media home folde feature is enabled
    if(HOME_FOLDERS)
    {
        $template->set_var('HOME_FOLDERS_ENABLED', ' checked="checked"');
    } else {
        $template->set_var('HOME_FOLDERS_DISABLED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert search select
    if(SEARCH == 'private')
    {
        $template->set_var('PRIVATE_SEARCH', ' selected="selected"');
    } elseif(SEARCH == 'registered') {
        $template->set_var('REGISTERED_SEARCH', ' selected="selected"');
    } elseif(SEARCH == 'none') {
        $template->set_var('NONE_SEARCH', ' selected="selected"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out if 777 permissions are set
    if(STRING_FILE_MODE == '0777' AND STRING_DIR_MODE == '0777')
    {
        $template->set_var('WORLD_WRITEABLE_SELECTED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out which file mode boxes are checked
    if(extract_permission(STRING_FILE_MODE, 'u', 'r'))
    {
        $template->set_var('FILE_U_R_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'u', 'w'))
    {
        $template->set_var('FILE_U_W_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'u', 'e'))
    {
        $template->set_var('FILE_U_E_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'g', 'r'))
    {
        $template->set_var('FILE_G_R_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'g', 'w'))
    {
        $template->set_var('FILE_G_W_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'g', 'e'))
    {
        $template->set_var('FILE_G_E_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'o', 'r'))
    {
        $template->set_var('FILE_O_R_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'o', 'w'))
    {
        $template->set_var('FILE_O_W_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_FILE_MODE, 'o', 'e'))
    {
        $template->set_var('FILE_O_E_CHECKED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Work-out which dir mode boxes are checked
    if(extract_permission(STRING_DIR_MODE, 'u', 'r'))
    {
        $template->set_var('DIR_U_R_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'u', 'w'))
    {
        $template->set_var('DIR_U_W_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'u', 'e'))
    {
        $template->set_var('DIR_U_E_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'g', 'r'))
    {
        $template->set_var('DIR_G_R_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'g', 'w'))
    {
        $template->set_var('DIR_G_W_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'g', 'e'))
    {
        $template->set_var('DIR_G_E_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'o', 'r'))
    {
        $template->set_var('DIR_O_R_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'o', 'w'))
    {
        $template->set_var('DIR_O_W_CHECKED', ' checked="checked"');
    }
    if(extract_permission(STRING_DIR_MODE, 'o', 'e'))
    {
        $template->set_var('DIR_O_E_CHECKED', ' checked="checked"');
    }
/*---------------------------------------------------------------------------------------------------*/
    $template->set_var(array(
                        'PAGES_DIRECTORY' => PAGES_DIRECTORY,
                        'MEDIA_DIRECTORY' => MEDIA_DIRECTORY,
                        'PAGE_EXTENSION' => PAGE_EXTENSION,
                        'PAGE_SPACER' => PAGE_SPACER,
                        'TABLE_PREFIX' => TABLE_PREFIX
                     ));
/*---------------------------------------------------------------------------------------------------*/
    // Insert Server Email value into template
    $template->set_var('SERVER_EMAIL', SERVER_EMAIL);
/*---------------------------------------------------------------------------------------------------*/
    // Insert language headings
    $template->set_var(array(
                    'HEADING_GENERAL_SETTINGS' => $HEADING['GENERAL_SETTINGS'],
                    'HEADING_DEFAULT_SETTINGS' => $HEADING['DEFAULT_SETTINGS'],
                    'HEADING_SEARCH_SETTINGS' => $HEADING['SEARCH_SETTINGS'],
                    'HEADING_SERVER_SETTINGS' => $HEADING['SERVER_SETTINGS'],
                    'HEADING_WBMAILER_SETTINGS' => $HEADING['WBMAILER_SETTINGS'],
                    'HEADING_ADMINISTRATION_TOOLS' => $HEADING['ADMINISTRATION_TOOLS']
                    )
            );
/*---------------------------------------------------------------------------------------------------*/
    $template->set_block('show_access_block', 'input_pages_directory_block', 'input_pages_directory');
    $template->set_block('show_access_block', 'show_pages_directory_block',  'show_pages_directory');
    $sql = 'SELECT COUNT(`page_id`) `numRows` FROM `'.TABLE_PREFIX.'pages` ';
    if (!$database->get_one($sql) ) {
        $template->parse('input_pages_directory', 'input_pages_directory_block', true);
        $template->set_block('show_pages_directory', '');
    } else {
        $template->parse('show_pages_directory', 'show_pages_directory_block', true);
        $template->set_block('input_pages_directory', '');
    }
/*---------------------------------------------------------------------------------------------------*/
    // Insert language text and messages
    $template->set_var(array(
                    'TEXT_WEBSITE_TITLE' => $TEXT['WEBSITE_TITLE'],
                    'TEXT_WEBSITE_DESCRIPTION' => $TEXT['WEBSITE_DESCRIPTION'],
                    'TEXT_WEBSITE_KEYWORDS' => $TEXT['WEBSITE_KEYWORDS'],
                    'TEXT_WEBSITE_HEADER' => $TEXT['WEBSITE_HEADER'],
                    'TEXT_WEBSITE_FOOTER' => $TEXT['WEBSITE_FOOTER'],
                    'TEXT_WEBSITE_SIGNATURE' => 'Signature',
                    'TEXT_HEADER' => $TEXT['HEADER'],
                    'TEXT_FOOTER' => $TEXT['FOOTER'],
                    'TEXT_VISIBILITY' => $TEXT['VISIBILITY'],
                    'TEXT_RESULTS_HEADER' => $TEXT['RESULTS_HEADER'],
                    'TEXT_RESULTS_LOOP' => $TEXT['RESULTS_LOOP'],
                    'TEXT_RESULTS_FOOTER' => $TEXT['RESULTS_FOOTER'],
                    'TEXT_NO_RESULTS' => $TEXT['NO_RESULTS'],
                    'TEXT_TEXT' => $TEXT['TEXT'],
                    'TEXT_DEFAULT' => $TEXT['DEFAULT'],
                    'TEXT_LANGUAGE' => $TEXT['LANGUAGE'],
                    'TEXT_TIMEZONE' => $TEXT['TIMEZONE'],
                    'TEXT_CHARSET' => $TEXT['CHARSET'],
                    'TEXT_DATE_FORMAT' => $TEXT['DATE_FORMAT'],
                    'TEXT_TIME_FORMAT' => $TEXT['TIME_FORMAT'],
                    'TEXT_TEMPLATE' => $TEXT['TEMPLATE'],
                    'TEXT_THEME' => $TEXT['THEME'],
                    'TEXT_WYSIWYG_EDITOR' => $TEXT['WYSIWYG_EDITOR'],
                    'TEXT_PAGE_LEVEL_LIMIT' => $TEXT['PAGE_LEVEL_LIMIT'],
                    'TEXT_INTRO_PAGE' => $TEXT['INTRO_PAGE'],
                    'TEXT_FRONTEND' => $TEXT['FRONTEND'],
                    'TEXT_LOGIN' => $TEXT['LOGIN'],
                    'TEXT_REDIRECT_AFTER' => $TEXT['REDIRECT_AFTER'],
                    'TEXT_SIGNUP' => $TEXT['SIGNUP'],
                    'TEXT_PHP_ERROR_LEVEL' => $TEXT['PHP_ERROR_LEVEL'],
                    'TEXT_PAGES_DIRECTORY' => $TEXT['PAGES_DIRECTORY'],
                    'TEXT_MEDIA_DIRECTORY' => $TEXT['MEDIA_DIRECTORY'],
                    'TEXT_PAGE_EXTENSION' => $TEXT['PAGE_EXTENSION'],
                    'TEXT_PAGE_SPACER' => $TEXT['PAGE_SPACER'],
                    'TEXT_RENAME_FILES_ON_UPLOAD' => $TEXT['RENAME_FILES_ON_UPLOAD'],
                    'TEXT_APP_NAME' => $TEXT['APP_NAME'],
                    'TEXT_SESSION_IDENTIFIER' => $TEXT['SESSION_IDENTIFIER'],
                    'TEXT_SEC_ANCHOR' => $TEXT['SEC_ANCHOR'],
                    'TEXT_SERVER_OPERATING_SYSTEM' => $TEXT['SERVER_OPERATING_SYSTEM'],
                    'TEXT_LINUX_UNIX_BASED' => $TEXT['LINUX_UNIX_BASED'],
                    'TEXT_WINDOWS' => $TEXT['WINDOWS'],
                    'TEXT_ADMIN' => $TEXT['ADMIN'],
                    'TEXT_TYPE' => $TEXT['TYPE'],
                    'TEXT_DATABASE' => $TEXT['DATABASE'],
                    'TEXT_HOST' => $TEXT['HOST'],
                    'TEXT_USERNAME' => $TEXT['USERNAME'],
                    'TEXT_PASSWORD' => $TEXT['PASSWORD'],
                    'TEXT_NAME' => $TEXT['NAME'],
                    'TEXT_TABLE_PREFIX' => $TEXT['TABLE_PREFIX'],
                    'TEXT_SAVE' => $TEXT['SAVE'],
                    'TEXT_RESET' => $TEXT['RESET'],
                    'TEXT_CHANGES' => $TEXT['CHANGES'],
                    'TEXT_ENABLED' => $TEXT['ENABLED'],
                    'TEXT_DISABLED' => $TEXT['DISABLED'],
                    'TEXT_MANAGE_SECTIONS' => $HEADING['MANAGE_SECTIONS'],
                    'TEXT_MANAGE' => $TEXT['MANAGE'],
                    'TEXT_SEARCH' => $TEXT['SEARCH'],
                    'TEXT_PUBLIC' => $TEXT['PUBLIC'],
                    'TEXT_PRIVATE' => $TEXT['PRIVATE'],
                    'TEXT_REGISTERED' => $TEXT['REGISTERED'],
                    'TEXT_NONE' => $TEXT['NONE'],
                    'TEXT_FILES' => strtoupper(substr($TEXT['FILES'], 0, 1)).substr($TEXT['FILES'], 1),
                    'TEXT_DIRECTORIES' => $TEXT['DIRECTORIES'],
                    'TEXT_FILESYSTEM_PERMISSIONS' => $TEXT['FILESYSTEM_PERMISSIONS'],
                    'TEXT_USER' => $TEXT['USER'],
                    'TEXT_GROUP' => $TEXT['GROUP'],
                    'TEXT_OTHERS' => $TEXT['OTHERS'],
                    'TEXT_READ' => $TEXT['READ'],
                    'TEXT_WRITE' => $TEXT['WRITE'],
                    'TEXT_EXECUTE' => $TEXT['EXECUTE'],
                    'TEXT_WARN_PAGE_LEAVE' => '',
                    'TEXT_SMART_LOGIN' => $TEXT['SMART_LOGIN'],
                    'TEXT_MULTIPLE_MENUS' => $TEXT['MULTIPLE_MENUS'],
                    'TEXT_HOMEPAGE_REDIRECTION' => $TEXT['HOMEPAGE_REDIRECTION'],
                    'TEXT_SECTION_BLOCKS' => $TEXT['SECTION_BLOCKS'],
                    'TEXT_PLEASE_SELECT' => $TEXT['PLEASE_SELECT'],
                    'TEXT_PAGE_TRASH' => $TEXT['PAGE_TRASH'],
                    'TEXT_PAGE_LANGUAGES' => $TEXT['PAGE_LANGUAGES'],
                    'TEXT_INLINE' => $TEXT['INLINE'],
                    'TEXT_SEPARATE' => $TEXT['SEPARATE'],
                    'TEXT_HOME_FOLDERS' => $TEXT['HOME_FOLDERS'],
                    'TEXT_WYSIWYG_STYLE' => $TEXT['WYSIWYG_STYLE'],
                    'TEXT_WORLD_WRITEABLE_FILE_PERMISSIONS' => $TEXT['WORLD_WRITEABLE_FILE_PERMISSIONS'],
                    'MODE_SWITCH_WARNING' => $MESSAGE['SETTINGS']['MODE_SWITCH_WARNING'],
                    'WORLD_WRITEABLE_WARNING' => $MESSAGE['SETTINGS']['WORLD_WRITEABLE_WARNING'],
                    'TEXT_MODULE_ORDER' => $TEXT['MODULE_ORDER'],
                    'TEXT_MAX_EXCERPT' => $TEXT['MAX_EXCERPT'],
                    'TEXT_TIME_LIMIT' => $TEXT['TIME_LIMIT']
                    ));
/*---------------------------------------------------------------------------------------------------*/
if($is_advanced)
{
    $template->parse('show_page_level_limit', 'show_page_level_limit_block', true);
    $template->parse('show_checkbox_1',       'show_checkbox_1_block', true);
    $template->parse('show_checkbox_2',       'show_checkbox_2_block', true);
    $template->parse('show_checkbox_3',       'show_checkbox_3_block', true);
    $template->parse('show_php_error_level',  'show_php_error_level_block', true);
    $template->parse('show_charset',          'show_charset_block', true);
    $template->parse('show_wysiwyg',          'show_wysiwyg_block', true);
    $template->parse('show_search',           'show_search_block', true);
    $template->parse('show_redirect_timer',   'show_redirect_timer_block', true);
}else {
    $template->set_block('show_page_level_limit', '');
    $template->set_block('show_checkbox_1', '');
    $template->set_block('show_checkbox_2', '');
    $template->set_block('show_checkbox_3', '');
    $template->set_block('show_php_error_level', '');
    $template->set_block('show_charset', '');
    $template->set_block('show_wysiwyg', '');
    $template->set_block('show_search', '');
    $template->set_block('show_redirect_timer', '');
}
if($is_advanced && $admin->get_user_id()=='1')
{
    $template->parse('show_access', 'show_access_block', true);
    $template->parse('show_chmod_js', 'show_chmod_js_block', true);
    $template->parse('show_setting_js', 'show_setting_js_block', true);
}else {
    $template->set_block('show_access_block', '');
    $template->set_block('show_chmod_js_block', '');
    $template->set_block('show_setting_js_block', '');
}
/*---------------------------------------------------------------------------------------------------*/
// Parse template objects output
$template->parse('main', 'main_block', false);
$template->pparse('output', 'page');

$admin->print_footer();

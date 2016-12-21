<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * phpMyAdmin sample configuration, you can use it as base for
 * manual configuration. For easier setup you can use setup/
 *
 * All directives are explained in documentation in the doc/ folder
 * or at <http://docs.phpmyadmin.net/>.
 *
 * @package PhpMyAdmin
 */

/*
 * This is needed for cookie based authentication to encrypt password in
 * cookie
 */
$cfg['blowfish_secret'] = 'w6nTD{KTfFa7yj]@]_Kba^2FnKu$%B2lrQ~w+ZlynqF'; /* YOU MUST FILL IN THIS FOR COOKIE AUTH! */

/*
 * Servers configuration
 */
$i = 0;

/*
 * First server
 */
$i++;
/* Authentication type */
$cfg['Servers'][$i]['auth_type'] = 'cookie';
/* Server parameters
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'usbw';
*/
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['AllowNoPassword'] = false;

/*
 * phpMyAdmin configuration storage settings.
 */

/* User used to manipulate with storage
 $cfg['Servers'][$i]['controlhost'] = '';
 $cfg['Servers'][$i]['controlport'] = '';
 $cfg['Servers'][$i]['controluser'] = 'pma';
 $cfg['Servers'][$i]['controlpass'] = 'pmapass';
*/

/* Storage database and tables phpMyAdmin */

 $cfg['Servers'][$i]['pmadb'] = 'phpmyadmin';
 $cfg['Servers'][$i]['bookmarktable'] = 'pma_bookmark';
 $cfg['Servers'][$i]['column_info'] = 'pma_column_info';
 $cfg['Servers'][$i]['history'] = 'pma_history';
 $cfg['Servers'][$i]['navigationhiding'] = 'pma_navigationhiding';
 $cfg['Servers'][$i]['pdf_pages'] = 'pma_pdf_pages';
 $cfg['Servers'][$i]['recent'] = 'pma__recent';
 $cfg['Servers'][$i]['relation'] = 'pma__relation';
 $cfg['Servers'][$i]['table_coords'] = 'pma__table_coords';
 $cfg['Servers'][$i]['table_info'] = 'pma__table_info';
 $cfg['Servers'][$i]['table_uiprefs'] = 'pma__table_uiprefs';
 $cfg['Servers'][$i]['tracking'] = 'pma_tracking';
 $cfg['Servers'][$i]['userconfig'] = 'pma_userconfig';
 $cfg['Servers'][$i]['users'] = 'pma_users';
 $cfg['Servers'][$i]['usergroups'] = 'pma__usergroups';
 $cfg['Servers'][$i]['favorite'] = 'pma_favorite';
 $cfg['Servers'][$i]['savedsearches'] = 'pma__savedsearches';
 $cfg['Servers'][$i]['central_columns'] = 'pma__central_columns';
 $cfg['Servers'][$i]['designer_settings'] = 'pma__designer_settings';
 $cfg['Servers'][$i]['export_templates'] = 'pma__export_templates';

 /* Contrib / Swekey authentication */
// $cfg['Servers'][$i]['auth_swekey_config'] = '/etc/swekey-pma.conf';

/*
 * Directories for saving/loading files from server
 */
//$cfg['UploadDir'] = '';
//$cfg['SaveDir'] = '';

/**
 * Whether to display icons or text or both icons and text in table row
 * action segment. Value can be either of 'icons', 'text' or 'both'.
 */
//$cfg['RowActionType'] = 'both';

/**
 * Defines whether a user should be displayed a "show all (records)"
 * button in browse mode or not.
 * default = false
 */
//$cfg['ShowAll'] = true;

/**
 * Number of rows displayed when browsing a result set. If the result
 * set contains more rows, "Previous" and "Next".
 * default = 30
 */
//$cfg['MaxRows'] = 50;

/**
 * disallow editing of binary fields
 * valid values are:
 *   false    allow editing
 *   'blob'   allow editing except for BLOB fields
 *   'noblob' disallow editing except for BLOB fields
 *   'all'    disallow editing
 * default = blob
 */
//$cfg['ProtectBinary'] = 'false';

/**
 * Default language to use, if not browser-defined or user-defined
 * (you find all languages in the locale folder)
 * uncomment the desired line:
 * default = 'en'
 */
//$cfg['DefaultLang'] = 'en';
//  $cfg['DefaultLang'] = 'de';

/**
 * How many columns should be used for table display of a database?
 * (a value larger than 1 results in some information being hidden)
 * default = 1
 */
//$cfg['PropertiesNumColumns'] = 2;

/**
 * Set to true if you want DB-based query history.If false, this utilizes
 * JS-routines to display query history (lost by window close)
 *
 * This requires configuration storage enabled, see above.
 * default = false
 */
//$cfg['QueryHistoryDB'] = true;

/**
 * When using DB-based query history, how many entries should be kept?
 *
 * default = 25
 */
//$cfg['QueryHistoryMax'] = 100;

/**
 * Should error reporting be enabled for JavaScript errors
 *
 * default = 'ask'
 */
 $cfg['SendErrorReports'] = 'ask';


$cfg['DefaultLang'] = 'de';
$cfg['ServerDefault'] = 1;
$cfg['Export']['file_template_table'] = 'local_@TABLE@_%Y%m%d_%H%M%S';
$cfg['Export']['file_template_database'] = 'local_@DATABASE@_%Y%m%d_%H%M%S';
$cfg['Export']['file_template_server'] = 'local_@SERVER@_%Y%m%d_%H%M%S';
$cfg['SaveCellsAtOnce'] = true;
$cfg['ShowPhpInfo'] = true;
//$cfg['UploadDir'] = '/UploadDir';
//$cfg['SaveDir'] = '/SaveDir';
$cfg['ActionLinksMode'] = 'icons';
$cfg['UserprefsDeveloperTab'] = true;
$cfg['Error_Handler']['display'] = true;
$cfg['SaveCellsAtOnce'] = true;
$cfg['Export']['method'] = 'custom';
$cfg['DefaultConnectionCollation'] = 'utf8_unicode_ci';
$cfg['DefaultQueryTable'] = 'SELECT * FROM @TABLE@';

$cfg['ShowDbStructureCreation'] = true;
$cfg['ShowDbStructureLastUpdate'] = true;

$cfg['QueryHistoryDB'] = true;
$cfg['DisplayServersList'] = true;
$cfg['DBG']['sql'] = true;
$cfg['PmaNoRelation_DisableWarning'] = true;

/*
 * You can find more configuration options in the documentation
 * in the doc/ folder or at <http://docs.phpmyadmin.net/>.
 */

/*
 * End of servers configuration
 */


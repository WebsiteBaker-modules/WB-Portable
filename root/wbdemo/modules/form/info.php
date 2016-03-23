<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       2009-2013, WebsiteBaker Org. e.V.
 * @link            http://www.websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: info.php 1898 2013-04-03 17:47:13Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/info.php $
 * @lastmodified    $Date: 2013-04-03 19:47:13 +0200 (Mi, 03. Apr 2013) $
 * @description
 */

// Must include code to stop this file being access directly
if(!defined('WB_URL')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
}
/* -------------------------------------------------------- */
$module_directory = 'form';
$module_name = 'Form Modul v3.0.0';
$module_function = 'page';
$module_version = '3.0.0';
$module_platform = '2.8.3';
$module_author = 'Ryan Djurovich & Rudolph Lartey - additions John Maats - PCWacht, dev-team';
$module_license = 'GNU General Public License';
$module_description = 'This module allows you to create customised online forms, such as a feedback form. '.
'Thank-you to Rudolph Lartey who help enhance this module, providing code for extra field types, etc.';

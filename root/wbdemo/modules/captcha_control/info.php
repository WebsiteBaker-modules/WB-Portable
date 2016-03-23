<?php
/**
 *
 * @category        modules
 * @package         captcha_control
 * @author          WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: info.php 1536 2011-12-10 04:22:29Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/captcha_control/info.php $
 * @lastmodified    $Date: 2011-12-10 05:22:29 +0100 (Sa, 10. Dez 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
$module_directory   = 'captcha_control';
$module_name        = 'Captcha and Spam-Protection (ASP) Control';
$module_function    = 'tool';
$module_version     = '1.2.0';
$module_platform    = '2.8.3 SP6';
$module_author      = 'Thomas Hornik (thorn)';
$module_license     = 'GNU General Public License';
$module_description = 'Admin-Tool to control CAPTCHA and ASP';

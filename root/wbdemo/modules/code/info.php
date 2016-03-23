<?php
/**
 *
 * @category        modules
 * @package         code
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: info.php 1389 2011-01-16 12:39:50Z FrankH $
 * @filesource      $HeadURL: http://svn.websitebaker.org/branches/2.8.x/wb/modules/code/info.php $
 * @lastmodified    $Date: 2011-01-16 13:39:50 +0100 (So, 16. Jan 2011) $
 *
*/

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

$module_directory   = 'code';
$module_name        = 'Code 3.0.0';
$module_function    = 'page';
$module_version     = '3.0.0';
$module_platform    = '2.8.3';
$module_author      = 'Ryan Djurovich';
$module_license     = 'GNU General Public License';
$module_description = 'This module allows you to execute PHP commands (limit access to users you trust!!)';


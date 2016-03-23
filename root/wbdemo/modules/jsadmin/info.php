<?php
/**
 *
 * @category        modules
 * @package         jsadmin
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: info.php 1540 2011-12-11 21:43:16Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/info.php $
 * @lastmodified    $Date: 2011-12-11 22:43:16 +0100 (So, 11. Dez 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

$module_directory = 'jsadmin';
$module_name = 'Javascript Admin';
$module_function = 'tool';
$module_version = '2.0.0';
$module_platform = '2.8.3';
$module_author = 'Stepan Riha, Swen Uth';
$module_license    = 'BSD License';
$module_description = 'This module adds Javascript functionality to the Website Baker Admin to improve some of the UI interactions. Uses the YahooUI library.';

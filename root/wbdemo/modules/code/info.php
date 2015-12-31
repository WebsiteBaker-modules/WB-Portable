<?php
/**
 *
 * @category        modules
 * @package         code
 * @author          WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: info.php 1389 2011-01-16 12:39:50Z FrankH $
 * @filesource        $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/code/info.php $
 * @lastmodified    $Date: 2011-01-16 13:39:50 +0100 (So, 16. Jan 2011) $
 *
*/

// Must include code to stop this file being access directly
/* -------------------------------------------------------- */
if(defined('WB_PATH') == false)
{
    // Stop this file being access directly
        die('<head><title>Access denied</title></head><body><h2 style="color:red;margin:3em auto;text-align:center;">Cannot access this file directly</h2></body></html>');
}
/* -------------------------------------------------------- */

$module_directory    = 'code';
$module_name        = 'Code';
$module_function    = 'page';
$module_version        = '2.8.4';
$module_platform    = '2.7 | 2.8.x';
$module_author        = 'Ryan Djurovich';
$module_license        = 'GNU General Public License';
$module_description    = 'This module allows you to execute PHP commands (limit access to users you trust!!)';


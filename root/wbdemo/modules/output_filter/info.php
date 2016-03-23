<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @copyright       WebsiteBaker Org. e.V.
 * @author          Christian Sommer
 * @author          Dietmar Wöllbrink
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3-SP4 and higher
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: info.php 1626 2012-02-29 22:45:20Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/output_filter/info.php $
 * @lastmodified    $Date: 2012-02-29 23:45:20 +0100 (Mi, 29. Feb 2012) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

$module_directory = 'output_filter';
$module_name = 'Output Filter Frontend v1.0.0 ';
$module_function = 'tool';
$module_version = '1.0.0';
$module_platform = '2.8.3 SP6';
$module_author = 'Christian Sommer(doc), Manuela v.d. Decken(DarkViper), Dietmar Wöllbrink(luisehahne)';
$module_license = 'GNU General Public License';
$module_description = 'This module allows to filter the output before displaying it on the frontend. Support for filtering mailto links and mail addresses in strings.';

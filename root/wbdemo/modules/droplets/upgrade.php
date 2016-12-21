<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade.php 16 2016-09-13 20:52:49Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/upgrade.php $
 * @lastmodified    $Date: 2016-09-13 22:52:49 +0200 (Di, 13. Sep 2016) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if (defined('WB_PATH') == false) {
    die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).'');
} else {
/* -------------------------------------------------------- */
    if(!function_exists('insertDropletFile')) {require('droplets.functions.php');}
    $msg = array();
    // create tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        if (!$database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, true )){
            echo $msg[] = $database->get_error();
        } else {
        }
        $sBaseDir = realpath(dirname(__FILE__).'/example/');
        $sBaseDir    = rtrim(str_replace('\\', '/', $sBaseDir), '/').'/';
        $aDropletFiles = getDropletFromFiles($sBaseDir);
        $bOverwriteDroplets = false;
        insertDropletFile($aDropletFiles, $database, $admin,$msg,$bOverwriteDroplets);
    }
}


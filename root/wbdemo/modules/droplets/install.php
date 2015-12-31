<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: install.php 1544 2011-12-15 15:57:59Z Luisehahne $
 * @filesource        $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/install.php $
 * @lastmodified    $Date: 2011-12-15 16:57:59 +0100 (Do, 15. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {

    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
}
/* -------------------------------------------------------- */

    // global $admin;

    $msg = array();
    $sql  = 'DROP TABLE IF EXISTS `'.TABLE_PREFIX.'mod_droplets` ';
    if( !$database->query($sql) ) {
        $msg[] = $database->get_error();
    }

    $sql  = 'CREATE TABLE IF NOT EXISTS `'.TABLE_PREFIX.'mod_droplets` ( ';
    $sql .= '`id` INT NOT NULL auto_increment, ';
    $sql .= '`name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL, ';
    $sql .= '`code` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL , ';
    $sql .= '`description` TEXT  CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, ';
    $sql .= '`modified_when` INT NOT NULL default \'0\', ';
    $sql .= '`modified_by` INT NOT NULL default \'0\', ';
    $sql .= '`active` INT NOT NULL default \'0\', ';
    $sql .= '`admin_edit` INT NOT NULL default \'0\', ';
    $sql .= '`admin_view` INT NOT NULL default \'0\', ';
    $sql .= '`show_wysiwyg` INT NOT NULL default \'0\', ';
    $sql .= '`comments` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL, ';
    $sql .= 'PRIMARY KEY ( `id` ) ';
    $sql .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
    if( !$database->query($sql) ) {
        $msg[] = $database->get_error();
    }
      if(!function_exists('insertDropletFile')) { require('droplets.functions.php'); }
      $sBaseDir = rtrim(str_replace('\\', '/',realpath(dirname(__FILE__).'/example/')), '/').'/';
        $aDropletFiles = getDropletFromFiles($sBaseDir);
        $bOverwriteDroplets = false;
        insertDropletFile($aDropletFiles,$msg,$bOverwriteDroplets);

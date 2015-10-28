<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @copyright       WebsiteBaker Org. e.V.
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: install.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/output_filter/install.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) { throw new RuntimeException('Illegal access'); }
/* -------------------------------------------------------- */

    $sTable = TABLE_PREFIX .'mod_output_filter';
    $database->query("DROP TABLE IF EXISTS `$sTable`");

    $sql = 'DROP TABLE IF EXISTS `'.$sTable.'`';
    $database->query($sql);
    $sql = 'CREATE TABLE `'.$sTable.'` ('
         . '`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\','
         . '`value` text COLLATE utf8_unicode_ci NOT NULL, '
         . 'PRIMARY KEY (`name`) '
         . ') ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
    $database->query($sql);

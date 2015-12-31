<?php
/**
 *
 * @category        modules
 * @package         menu_link
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: install.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/menu_link/install.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

$table = TABLE_PREFIX ."mod_menu_link";
$database->query("DROP TABLE IF EXISTS `$table`");
$sql = '
    CREATE TABLE IF NOT EXISTS `'.TABLE_PREFIX.'mod_menu_link` (
        `section_id` INT(11) NOT NULL DEFAULT \'0\',
        `page_id` INT(11) NOT NULL DEFAULT \'0\',
        `target_page_id` INT(11) NOT NULL DEFAULT \'0\',
        `redirect_type` INT NOT NULL DEFAULT \'301\',
        `anchor` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL DEFAULT \'0\' ,
        `extern` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL DEFAULT \'\' ,
        PRIMARY KEY (`section_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
';
$database->query($sql);

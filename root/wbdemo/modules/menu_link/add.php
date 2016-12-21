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
 * @version         $Id: add.php 1457 2011-06-25 17:18:50Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/wysiwyg/modify.php $
 * @lastmodified    $Date: 2011-01-11 20:29:52 +0100 (Di, 11 Jan 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

    // Insert new row into database
    $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_menu_link` SET '
         . '`page_id` = '.$database->escapeString($page_id).', '
         . '`section_id` = '.$database->escapeString($section_id).', '
         . '`target_page_id` = 0, '
         . '`redirect_type`  = \'301\', '
         . '`anchor` = 0, '
         . '`extern` = \'\' ';
    $database->query($sql);


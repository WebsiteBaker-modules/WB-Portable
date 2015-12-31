<?php
/**
 *
 * @category        modules
 * @package         wrapper
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: add.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/wrapper/install.php $
 * @lastmodified    $Date: 2011-01-10 13:21:47 +0100 (Mo, 10 Jan 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Insert an extra row into the database

    // Insert new row into database
    $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_wrapper` SET '
         . '`section_id` = '.$section_id.', '
         . '`page_id` = '.$page_id.', '
         . '`height` = 400, '
         . '`url` = \'\' ';
    $database->query($sql);

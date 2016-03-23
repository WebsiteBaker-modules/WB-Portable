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
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/code/info.php $
 * @lastmodified    $Date: 2011-01-16 13:39:50 +0100 (So, 16. Jan 2011) $
 *
*/
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
// Delete record from the database
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_code` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
}

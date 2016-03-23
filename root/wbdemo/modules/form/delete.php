<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: delete.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/delete.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 * @description
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
    // Delete page from mod_wysiwyg
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_form_fields` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_form_settings` '
          . 'WHERE `section_id` = '.$database->escapeString($section_id);
    $database->query($sql);
}

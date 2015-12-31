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
 * @version         $Id: add_droplet.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/add_droplet.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
$droplet_id = 0;
if($admin->get_permission('admintools') == true) {

    $modified_when = time();
    $modified_by = intval($admin->get_user_id());

    // Insert new row into database
    $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets` SET '
    . '`name` = \'\', '
    . '`code` = \'\', '
    . '`description` = \'\', '
    . '`comments` = \'\', '
    . '`active` = 1, '
    . '`admin_edit` = 0, '
    . '`admin_view` = 0, '
    . '`show_wysiwyg`= 0, '
    . '`modified_when` = '.$modified_when.', '
    . '`modified_by` = '.$modified_by.' ';
    $database->query($sql);

    // Get the id 
    $droplet_id = intval( $database->getLastInsertId());

}

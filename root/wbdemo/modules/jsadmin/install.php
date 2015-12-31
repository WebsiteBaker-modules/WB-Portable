<?php
/**
 *
 * @category        modules
 * @package         JsAdmin
 * @author          WebsiteBaker Project, modified by Swen Uth for WebsiteBaker 2.7
 * @copyright       Stepan Riha, WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: install.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/install.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
*/

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

// add new rows to table "settings"

$msg = array ();
$table = TABLE_PREFIX ."mod_jsadmin";
$jsadminDefault = array (
    array ( 'id' => '1','name' => 'mod_jsadmin_persist_order','value' => '1' ),
    array ( 'id' => '2','name' => 'mod_jsadmin_ajax_order_pages','value' => '1' ),
    array ( 'id' => '3','name' => 'mod_jsadmin_ajax_order_sections','value' => '1' ),
);

$database->query("DROP TABLE IF EXISTS `$table`");
$sql = 'CREATE TABLE IF NOT EXISTS `'.TABLE_PREFIX.'mod_jsadmin` ('
    . ' `id` INT(11) NOT NULL DEFAULT \'0\','
    . ' `name` VARCHAR(255) NOT NULL DEFAULT \'0\','
    . ' `value` INT(11) NOT NULL DEFAULT \'0\','
    . ' PRIMARY KEY ( `id` )'
    . ' ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';

if($database->query($sql) ) {

    for($x=0;$x<sizeof($jsadminDefault); $x++) {
        $sql  = 'INSERT INTO `'.TABLE_PREFIX.'mod_jsadmin` SET '
              . '`id`=\''.$jsadminDefault[$x]['id'].'\', '
              .'`name`=\''.$jsadminDefault[$x]['name'].'\', '
             . '`value`=\''.$jsadminDefault[$x]['value'].'\' ';
        if(!$database->query($sql) ) { $msg[] = $database->get_error();}
    }
} else {
    $msg[] = $database->get_error();
}


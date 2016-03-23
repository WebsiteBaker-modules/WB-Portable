<?php
/**
 *
 * @category        modules
 * @package         JsAdmin
 * @author          WebsiteBaker Project, modified by Swen Uth for Website Baker 2.7
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http:/websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: jsadmin.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/jsadmin.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
*/

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
function get_setting($name, $default = '') {
    global $database, $sql;
    $sql  = 'SELECT `value` FROM `'.TABLE_PREFIX.'mod_jsadmin` '
          . 'WHERE `name` = \''.$database->escapeString($name).'\'';
    if( $rs = $database->get_one($sql) ) {
        return $rs;
    } else {
      return $default;
      
    }
}

function save_setting($name, $value) {
    global $database;
    $prev_value = get_setting ( $name, '' );
    if($prev_value === false) {
        $sql   = 'INSERT INTO `'.TABLE_PREFIX.'mod_jsadmin` SET ';
    } else {
        $sql   = 'UPDATE `'.TABLE_PREFIX.'mod_jsadmin` SET ';
    }
        $sql  .= '`name`  = \''.$database->escapeString($name).'\', '
              . '`value`  = '.(int)$value.' '
              . 'WHERE `name` = \''.$database->escapeString($name).'\'';
        $database->query($sql);
        return $sql;
}

// the follwing variables to use and check existing the YUI
$WB_MAIN_RELATIVE_PATH="../..";
$YUI_PATH = '/include/yui';
$js_yui_min = "-min";  // option for smaller code so faster
$js_yui_scripts = Array();
$js_yui_scripts[] = $YUI_PATH.'/yahoo/yahoo'.$js_yui_min.'.js';
$js_yui_scripts[] = $YUI_PATH.'/event/event'.$js_yui_min.'.js';
$js_yui_scripts[] = $YUI_PATH.'/dom/dom'.$js_yui_min.'.js';
$js_yui_scripts[] = $YUI_PATH.'/connection/connection'.$js_yui_min.'.js';
$js_yui_scripts[] = $YUI_PATH.'/dragdrop/dragdrop'.$js_yui_min.'.js';

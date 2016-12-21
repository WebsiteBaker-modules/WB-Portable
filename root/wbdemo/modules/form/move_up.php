<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: move_up.php 1457 2011-06-25 17:18:50Z Luisehahne $
 * @filesource        $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/move_up.php $
 * @lastmodified    $Date: 2011-06-25 19:18:50 +0200 (Sa, 25. Jun 2011) $
 * @description
 */

// Include the configuration file
if (!defined('WB_PATH')) {
    $sStartupFile = dirname(dirname(__DIR__)).'/config.php';
    if (is_readable($sStartupFile)) {
        require($sStartupFile);
    } else {
        die(
            'tried to read a nonexisting or not readable startup file ['
          . basename(dirname($sStartupFile)).'/'.basename($sStartupFile).']!!'
        );
    }
}

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Get id
$field_id = $admin->checkIDKEY('field_id', false, 'GET');
if (!$field_id) {
 $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $field_id ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
// Include the ordering class
if (!class_exists('order', false)){require(WB_PATH.'/framework/class.order.php');}

// Create new order object an reorder
$order = new order(TABLE_PREFIX.'mod_form_fields', 'position', 'field_id', 'section_id');
if($order->move_up($field_id)) {
    $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
    $admin->print_error($TEXT['ERROR'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();

<?php
/**
 *
 * @category        modules
 * @package         JsAdmin
 * @author          WebsiteBaker Project, modified by Swen Uth for Website Baker 2.7
 * @copyright       (C) 2006, Stepan Riha
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: move_to.php 1441 2011-04-09 23:04:22Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/move_to.php $
 * @lastmodified    $Date: 2011-04-10 01:04:22 +0200 (So, 10. Apr 2011) $
 *
*/

require( dirname(dirname((__DIR__))).'/config.php' );

    // Include WB admin wrapper script
    $update_when_modified = false;
    // Tells script to update when this page was last updated
    require(WB_PATH.'/modules/admin.php');

if(isset($_GET['page_id']) AND is_numeric($_GET['page_id']) AND is_numeric(@$_GET['position'])) {
    $old_position = (int)$_GET['position'];
    if(isset($_GET['newposition']) AND is_numeric($_GET['newposition'])) {
      $position = (int)$_GET['newposition'];
    }

$field_id = $admin->checkIDKEY('field_id', false, 'GET');
if (!$field_id) {
 $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
    $id = (int)$field_id;
    $id_field = 'field_id';
    $table = TABLE_PREFIX.'mod_form_fields';
    $common_field = 'section_id';
}

//$order = new order($table, 'position', $id_field, 'section_id');

/**
 * 
    if($old_position == $position) {
        return;
    }
    if($old_position < $position) {
    if($order->move_up($field_id)) { return; }
    }

    if($old_position > $position) {
    if($order->move_down($field_id)) { return; }
    }
 */

/**
 * 
 */
    // Get current index
    $sql = <<<EOT
SELECT `$common_field`, `position` FROM `$table` WHERE `$id_field` = $id
EOT;
    echo "$sql";
    $rs = $database->query($sql);
    if($row = $rs->fetchRow( MYSQLI_ASSOC )) {
        $common_id = $row[$common_field];
        $old_position = $row['position'];
    }
//    echo "$old_position";
    if($old_position == $position)
        return;

    // Build query to update affected rows
    if($old_position < $position)
        $sql = <<<EOT
UPDATE `$table` SET `position` = `position` - 1
    WHERE `position` > $old_position AND `position` <= $position
        AND `$common_field` = $common_id
EOT;
    else
        $sql = <<<EOT
UPDATE `$table` SET `position` = `position` + 1
    WHERE `position` >= $position AND `position` < $old_position
        AND `$common_field` = $common_id
EOT;
//    echo "$sql";
    $database->query($sql);

    // Build query to update specified row
    $sql = <<<EOT
UPDATE `$table` SET `position` = $position
    WHERE `$id_field` = $id
EOT;
//    echo "$sql";
    $database->query($sql);
} else {
    die("Missing parameters");
    header("Location: index.php");
    exit(0);
}

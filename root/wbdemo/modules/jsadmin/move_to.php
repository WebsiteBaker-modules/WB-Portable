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

    $aJsonRespond = array();
    $aJsonRespond['jsadmin'] = array();
    $aJsonRespond['modules'] = '';
    $aJsonRespond['modules_dir'] = '';
    $aJsonRespond['message'] = 'ajax operation failed';
    $aJsonRespond['success'] = false;
    // Include WB admin wrapper script
    $update_when_modified = false;
// Tells script to update when this page was last updated
    $admin_header = false;
    require(WB_PATH.'/modules/admin.php');

if (isset($aRequestVars['page_id']) && is_numeric($aRequestVars['page_id']) && is_numeric(@$aRequestVars['newposition']))
{

// Include the ordering class
    if (!class_exists('order', false)){require(WB_PATH.'/framework/class.order.php');}

  $cleanOrder = (function($common_id) use ($database){
        global $table,$sFieldOrderName,$common_field;
// Loop through all records and give new order
        $sql  = 'SET @c:=0';
        $database->query($sql);
        $sql  = 'UPDATE `'.$table.'` SET `'.$sFieldOrderName.'`=(SELECT @c:=@c+1) '
              . 'WHERE `'.$common_field.'`=\''.$common_id.'\' '
              . 'ORDER BY `'.$sFieldOrderName.'` ASC;';
        if ($database->query($sql)){
            echo "$sql".PHP_EOL;
        } else {
          $aJsonRespond['message'] = $sFieldOrderName.PHP-EOL.$database->get_error();
          $aJsonRespond['success'] = false;
          exit (json_encode($aJsonRespond));
        }
  });

    $position = (int)$aRequestVars['newposition'];
    // Interface move_to.php from modules
    if (isset($aRequestVars['module'])) {
        $aJsonRespond['jsadmin'] = $aRequestVars;
        $sParameterFileName = WB_PATH.'/modules/'.$aRequestVars['module'].'/move_to.php';
        if (is_readable($sParameterFileName)){require $sParameterFileName;}
//        exit(json_encode($aJsonRespond));
    } else {
    // default Interface move_to.php from core
        if( isset($aRequestVars['page_id']) || (isset($aRequestVars['section_id'])) ) {
            // Get common fields
            if(isset($aRequestVars['section_id']) && is_numeric($aRequestVars['section_id'])) {
//                $page_id = (int)$aRequestVars['page_id'];
                $id = (int)$aRequestVars['section_id'];
                $id_field = 'section_id';
//                $group = (int)$aRequestVars['section_id'];
                $sFieldOrderName = 'position';
                $common_field = 'page_id';
                $table = TABLE_PREFIX.'sections';
                $aJsonRespond['modules'] = '/'.ADMIN_DIRECTORY.'(pages/sections.php';
            } else {
                $id = (int)$aRequestVars['page_id'];
                $id_field = 'page_id';
//                $group = (int)$aRequestVars['page_id'];
                $sFieldOrderName = 'position';
                $common_field = 'parent';
                $table = TABLE_PREFIX.'pages';
                $aJsonRespond['modules'] = '/'.ADMIN_DIRECTORY.'(pages/index.php';
            }
        }
    }

    // Get current index
    $sql = <<<EOT
SELECT `$common_field`, `$sFieldOrderName` FROM `$table` WHERE `$id_field` = $id
EOT;
    echo "$sql".PHP_EOL;
    if ($oRes = $database->query($sql)){
        if( $row = $oRes->fetchRow(MYSQLI_ASSOC)) {
            $common_id = $row[$common_field];
            $old_position = $row['position'];
        }
    } else {
      $aJsonRespond['message'] = $sFieldOrderName.PHP-EOL.$database->get_error();
      $aJsonRespond['success'] = false;
      exit (json_encode($aJsonRespond));
    }
    echo "Old Position: $old_position".PHP_EOL;
    echo "New Position: $position".PHP_EOL;
    if($old_position == $position){
      $cleanOrder($common_id);
      return;
    }

    // Build query to update affected rows
    if($old_position < $position)
        $sql = <<<EOT
UPDATE `$table` SET `$sFieldOrderName` = `$sFieldOrderName` - 1
    WHERE `$sFieldOrderName` > $old_position AND `$sFieldOrderName` <= $position
        AND `$common_field` = $common_id
EOT;
    else
        $sql = <<<EOT
UPDATE `$table` SET `position` = `position` + 1
    WHERE `$sFieldOrderName` >= $position AND `$sFieldOrderName` < $old_position
        AND `$common_field` = $common_id
EOT;
    if ($database->query($sql)){
        echo "$sql".PHP_EOL;
    }
    // Build query to update specified row
    $sql = <<<EOT
UPDATE `$table` SET `$sFieldOrderName` = $position
    WHERE `$id_field` = $id
EOT;
    if ($database->query($sql))
    {
        echo "$sql".PHP_EOL;
        $cleanOrder($common_id);
        $aJsonRespond['success'] = true;
        echo (json_encode($aJsonRespond));
    }
} else {
    $aJsonRespond['message'] = "Missing parameters";
    $aJsonRespond['success'] = false;
    exit (json_encode($aJsonRespond));
}

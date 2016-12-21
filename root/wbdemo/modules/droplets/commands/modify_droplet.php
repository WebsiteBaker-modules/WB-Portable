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
 * @version         $Id: modify_droplet.php 16 2016-09-13 20:52:49Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/commands/modify_droplet.php $
 * @lastmodified    $Date: 2016-09-13 22:52:49 +0200 (Di, 13. Sep 2016) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Get id
if(!isset($dropletAddId)) {
    $droplet_id = (@$droplet_id?:'droplet_id');
    $droplet_id = ($oApp->checkIDKEY($droplet_id, false, ''));
}
if ($droplet_id === false) {
    $oApp->print_error('MODIFY_DROPLET_IDKEY::'.$MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl);
    exit();
}

$sOverviewDroplets = $DR_TEXT['DROPLETS'];
$sTimeStamp = (@$sTimeStamp?:'');
$modified_by = $oApp->get_user_id();
if (($droplet_id > 0)) {
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` = '.$droplet_id;
    $oDroplet = $oDb->query($sql);
    $aDroplet = $oDroplet->fetchRow(MYSQLI_ASSOC);
    $content  = (htmlspecialchars($aDroplet['code']));
    $sSubmitButton = $TEXT['SAVE'];
    $iDropletIdKey = $oApp->getIDKEY($droplet_id);
    $dropletAddId = $droplet_id;
} else {
    $aDroplet = array();
    // check if it is a normal add or a copy
    if (sizeof($aDroplet)==0) {
        $aDroplet = array(
            'id' => $dropletAddId,
            'name' => 'Dropletname',
            'code' => 'return true;',
            'description' => '',
            'modified_when' => 0,
            'modified_by' => 0,
            'active' => 0,
            'admin_edit' => 0,
            'admin_view' => 0,
            'show_wysiwyg' => 0,
            'comments' => ''
            );
        $content = '';
    }
    $dropletAddId = 0;
    $sSubmitButton = $TEXT['ADD'];
    $iDropletIdKey = $oApp->getIDKEY($droplet_id);
}
require_once($oReg->AppPath . '/include/editarea/wb_wrapper_edit_area.php');
echo registerEditArea ('contentedit','php');
//'contentedit','php',true,'both',true,true,600,450,'search,fullscreen, |, undo, redo, |, select_font,|,highlight, reset_highlight, |, help');

?><br /><div class="block-outer droplets">
<form name="modify" action="<?php echo $ToolUrl; ?>" method="post" style="margin: 0;">
    <input type="hidden" name="command" value="save_droplet" />
    <input type="hidden" name="data_codepress" value="" />
    <input type="hidden" name="droplet_id" value="<?php echo $iDropletIdKey; ?>" />
    <input type="hidden" name="id" value="<?php echo $dropletAddId; ?>" />
    <input type="hidden" name="show_wysiwyg" value="<?php echo $aDroplet['show_wysiwyg']; ?>" />
    <?php echo $oApp->getFTAN(); ?>
    <table class="droplets droplets-modify" style="width: 100%;">
        <tbody>
        <tr>
            <td class="setting_name">
                <?php echo $TEXT['NAME']; ?>:
            </td>
            <td >
                <div class="block-outer" style="width: 98%;">
<?php if ($droplet_id ==0 ){ ?>
                     <input type="text" class="rename-input" name="title" value="<?php echo stripslashes($aDroplet['name']).$sTimeStamp; ?>" style="width: 100%;" maxlength="32" />
<?php } else { ?>
                     <div class="noInput"><?php echo stripslashes($aDroplet['name']).$sTimeStamp; ?></div>
<?php }?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="setting_name" ><?php echo $TEXT['DESCRIPTION']; ?>:</td>
            <td>
                <input type="text" name="description" value="<?php echo stripslashes($aDroplet['description']); ?>" style="width: 98%;" />
            </td>
        </tr>
        <tr>
            <td class="setting_name" >
                <?php echo $TEXT['ACTIVE']; ?>:
            </td>
            <td>
                <input type="radio" name="active" id="active_true" value="1" <?php if($aDroplet['active'] == 1) { echo ' checked="checked"'; } ?> />
                <a href="#" onclick="javascript: document.getElementById('active_true').checked = true;">
                <label><?php echo $TEXT['YES']; ?></label>
                </a>
                <input type="radio" name="active" id="active_false" value="0" <?php if($aDroplet['active'] == 0) { echo ' checked="checked"'; } ?> />
                <a href="#" onclick="javascript: document.getElementById('active_false').checked = true;">
                <label><?php echo $TEXT['NO']; ?></label>
                </a>
            </td>
        </tr>
<?php
// Next show only if admin is logged in, user_id = 1
if ($modified_by == 1) {
    ?>
        <tr>
            <td class="setting_name">
                <?php echo $TEXT['ADMIN']; ?>:
            </td>
            <td>
                <?php echo $DR_TEXT['ADMIN_EDIT']; ?>&nbsp;
                <input type="radio" name="admin_edit" id="admin_edit_true" value="1" <?php if($aDroplet['admin_edit'] == 1) { echo ' checked="checked"'; } ?> />
                <a href="#" onclick="document.getElementById('admin_edit_true').checked = true;">
                <label><?php echo $TEXT['YES']; ?></label>
                </a>
                <input type="radio" name="admin_edit" id="admin_edit_false" value="0" <?php if($aDroplet['admin_edit'] == 0) { echo ' checked="checked"'; } ?> />
                <a href="#" onclick="document.getElementById('admin_edit_false').checked = true;">
                <label><?php echo $TEXT['NO']; ?></label>
                </a>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <?php echo $DR_TEXT['ADMIN_VIEW']; ?>:
                <input type="radio" name="admin_view" id="admin_view_true" value="1" <?php if($aDroplet['admin_view'] == 1) { echo ' checked="checked"'; } ?> />
                <a href="#" onclick="document.getElementById('admin_view_true').checked = true;">
                <label><?php echo $TEXT['YES']; ?></label>
                </a>
                <input type="radio" name="admin_view" id="admin_view_false" value="0" <?php if($aDroplet['admin_view'] == 0) { echo ' checked="checked"'; } ?> />
                <a href="#" onclick="document.getElementById('admin_view_false').checked = true;">
                <label><?php echo $TEXT['NO']; ?></label>
                </a>
            </td>
        </tr>
    <?php
}
?>
        <tr>
            <td class="setting_name"><?php echo $TEXT['CODE']; ?>:</td>
            <td >

            <textarea name="savecontent" id ="contentedit" style="width: 98%; height: 450px;" rows="50" cols="120"><?php echo $content; ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="setting_name" ><?php echo $TEXT['COMMENTS']; ?>:</td>
            <td>
                <textarea name="comments" style="width: 98%; height: 100px;" rows="50" cols="120"><?php echo ($aDroplet['comments']); ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        </tbody>
    </table>
<br />
<table>
    <tr>
        <td>
<?php
// Show only save button if allowed....
if ($modified_by == 1 || $aDroplet['admin_edit'] == 0 ) {
?>
            <button  class="btn" name="command" value="save_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" type="submit"><?php echo $sSubmitButton; ?></button>
<?php
}
?>
            <button class="btn" type="button" onclick="window.location = '<?php echo $ToolUrl; ?>';"><?php echo $TEXT['CANCEL']; ?></button>
        </td>
    </tr>
</table>
</form>
<br />
</div>


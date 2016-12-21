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
 * @version         $Id: rename_droplet.php 16 2016-09-13 20:52:49Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/commands/rename_droplet.php $
 * @lastmodified    $Date: 2016-09-13 22:52:49 +0200 (Di, 13. Sep 2016) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Get id
if(!isset($iDropletAddId)) {
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
$sHeaderDroplet = $DR_TEXT['ADD_DROPLET'];
$sDropletHelp = $Droplet_Help['DROPLET_RENAME_ADD'];
if (($droplet_id > 0)) {
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` = '.$droplet_id;
    $oDroplet = $oDb->query($sql);
    $aDroplet = $oDroplet->fetchRow(MYSQLI_ASSOC);
    $content  = (htmlspecialchars($aDroplet['code']));
    $DropletName    = $aDroplet['name'];
    $sSubmitButton  = $TEXT['SAVE'];
    $iDropletIdKey  = $oApp->getIDKEY($droplet_id);
    $iDropletAddId  = $droplet_id;
    $sHeaderDroplet = $Droplet_Header['RENAME_DROPLET'];
    $sDropletHelp   = $Droplet_Help['DROPLET_RENAME'];
} else if (isset($aCopyDroplet)){
    $aDroplet = $aCopyDroplet;
    $DropletName   = $aDroplet['name'];
    $sSubmitButton = $TEXT['ADD'];
    $iDropletIdKey = $droplet_id;
} else {
    $aDroplet = array();
    // check if it is a normal add or a copy
    if (sizeof($aDroplet)==0) {
        $aDroplet = array(
            'id' => 0,
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
        $DropletName   = $aDroplet['name'];
        $content = '';
    }
    $sDropletHelp = $Droplet_Help['DROPLET_RENAME_ADD'];
    $sSubmitButton = $TEXT['ADD'];
    $iDropletIdKey = $oApp->getIDKEY($aDroplet['id']);
}
    $aFtan = $admin->getFTAN('');
    // prepare default data for phplib and twig
    $aTplData = array (
        'action' => $action,
        'FTAN_NAME' => $aFtan['name'],
        'FTAN_VALUE' => $aFtan['value'],
        'DropletName' => $aDroplet['name'],
        'iDropletAddId' => $iDropletAddId,
        'iDropletIdKey' => $iDropletIdKey,
        'show_wysiwyg' => $aDroplet['show_wysiwyg'],
        'sSubmitButton' => $sSubmitButton,
        'HEADER_DROPLET' => $sHeaderDroplet,
        'sDropletHelp' => $sDropletHelp,
        );
// Create new template object with phplib
    $oTpl = new Template($sAddonThemePath, 'keep' );
    $oTpl->set_file('page', 'rename_droplet.htt');
    $oTpl->set_block('page', 'main_block', 'main');
    $oTpl->set_var($aLang);
    $oTpl->set_var($aTplDefaults);
    $oTpl->set_var($aTplData);
    $oTpl->set_block('main_block', 'show_admin_edit_block', 'show_admin_edit');
    if ($admin->ami_group_member('1') || $aDroplet['admin_edit'] == 0 ) {
        $oTpl->parse('show_admin_edit', 'show_admin_edit_block', true);
    } else {
        $oTpl->set_block('show_admin_edit', '');
    }
/*-- finalize the page -----------------------------------------------------------------*/
    $oTpl->parse('main', 'main_block', false);
    $oTpl->pparse('output', 'page');

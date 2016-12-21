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
 * @version         $Id: overview.php 124 2016-11-26 09:27:02Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/commands/overview.php $
 * @lastmodified    $Date: 2016-11-26 10:27:02 +0100 (Sa, 26. Nov 2016) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
$msg = array();
if( !$oApp->get_permission($sAddonName,'module' ) ) {
    $oApp->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
    exit();
}
// Get userid for showing admin only droplets or not
$loggedin_user = ($oApp->ami_group_member('1') ? 1 : $oApp->get_user_id() );
$loggedin_group = $oApp->get_groups_id();
$oApp_user = ( ($oApp->get_home_folder() == '') && ($oApp->ami_group_member('1') ) || ($loggedin_user == '1'));
//removes empty entries from the table so they will not be displayed
$sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_droplets` '
     . 'WHERE name = \'\' ';
if( !$oDb->query($sql) ) {
    $msg[] = $oDb->get_error();
}
// if import failed after installation, should be only 1 time
$sql = 'SELECT COUNT(`id`) FROM `'.TABLE_PREFIX.'mod_droplets` ';
if( !$oDb->get_one($sql) ) {
    include($sAddonPath.'/install.php');
}

function check_syntax($code) {
    if (class_exists('ParseError')) {
        $bRetval = true;
        try{
            $bRetval = (@eval('return true;' . $code));
        } catch(ParseError $e) {
            $bRetval = false;
        }
    } else {
        $bRetval = false;
        $bRetval = (@eval('return true;' . $code));
    }
    return $bRetval;
}
$sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` ';
if (!$oApp_user) {
    $sql .= 'WHERE `admin_view` <> 1 ';
}
$sql .= 'ORDER BY `modified_when` DESC';
$oDroplets = $oDb->query($sql);
$num_droplets = $oDroplets->numRows();
$aFtan = $admin->getFTAN('');
// prepare default data for phplib and twig
$aTplData = array (
        'action' => $action,
        'FTAN_NAME' => $aFtan['name'],
        'FTAN_VALUE' => $aFtan['value'],
        'IDKEY0' => $oApp->getIDKEY(0),
        );
// Create new template object with phplib  IDKEY0
    $oTpl = new Template($sAddonThemePath, 'keep' );
    $oTpl->set_file('page', 'overview.htt');
    $oTpl->set_block('page', 'main_block', 'main');
    $oTpl->set_var($aLang);
    $oTpl->set_var($aTplDefaults);
    $oTpl->set_var($aTplData);
    $oTpl->set_block('main_block', 'list_droplet_block', 'list_droplet');
/*----------------------------------------------------------------------------------------------------------------------*/
    while($aDroplets = $oDroplets->fetchRow(MYSQLI_ASSOC))
    {
        $aComment =  array();
        $modified_user = $TEXT['UNKNOWN'];
        $modified_userid = 0;
        $sql = 'SELECT `display_name`,`username`, `user_id` FROM `'.TABLE_PREFIX.'users` '
        .'WHERE `user_id` = '.$aDroplets['modified_by'];
        $get_modified_user = $oDb->query($sql);
        if($get_modified_user->numRows() > 0) {
            $fetch_modified_user = $get_modified_user->fetchRow(MYSQLI_ASSOC);
            $modified_user = $fetch_modified_user['username'];
            $modified_userid = $fetch_modified_user['user_id'];
        }
        $sDropletName  =  mb_strlen($aDroplets['name']) > 20 ? mb_substr($aDroplets['name'], 0, 19).'…' : $aDroplets['name'];

        $sDropletDescription  =  mb_strlen($aDroplets['description']) > 60 ? mb_substr($aDroplets['description'], 0, 59).'…' : $aDroplets['description'];
//        $iDropletIdKey = $aDroplets['id'];
        $iDropletIdKey = $oApp->getIDKEY($aDroplets['id']);
        $comments = '';
//        $comments = str_replace(array("\r\n", "\n", "\r"), '<br >', $aDroplets['comments']);
        if (!strpos($comments,"[[")) $comments = "Use: [[".$aDroplets['name']."]]<br />".$comments;
        $comments = str_replace(array("[[", "]]"), array('<b>[[',']]</b>'), $comments);
        $valid_code = true;
        $valid_code = check_syntax($aDroplets['code']);
        if (!$valid_code === true) $comments = '<span color=\'red\'><strong>'.$DR_TEXT['INVALIDCODE'].'</strong></span><br />'.$comments;
        $unique_droplet = true;
        if ($unique_droplet === false ) {$comments = '<span color=\'red\'><strong>'.$DR_TEXT['NOTUNIQUE'].'</strong></span><br />'.$comments;}

        $aTplData = array(
            'iDropletIdKey'         => $iDropletIdKey,
            'sDropletName'          => $sDropletName,
            'sDropletTitle'         => $aDroplets['name'],
            'comments'              => '',
            'icon'                  => ($valid_code && $unique_droplet ? 'droplet' : 'invalid'),
            'sDropletDescription'   => $sDropletDescription,
            'DropletId'             => $aDroplets['id'],
            'modified_when'         => date('d.m.Y'.' '.'H:i', $aDroplets['modified_when']+TIMEZONE),
            'active'                => $aDroplets['id'],
            'ActiveIcon'            => ($aDroplets['active']?'1':'0'),
        );
/*----------------------------------------------------------------------------------------------------------------------*/
        $oTpl->set_var($aTplData);
        $oTpl->parse('list_droplet', 'list_droplet_block', true);
    }
/*-- finalize the page -----------------------------------------------------------------*/
    $oTpl->parse('main', 'main_block', false);
    $oTpl->pparse('output', 'page');

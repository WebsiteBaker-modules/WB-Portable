<?php
/**
 *
 * @category        admin
 * @package         groups
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: get_permissions.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/groups/get_permissions.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */
/*---------------------------------------------------------------------------------------------------------------*/
if(defined('WB_PATH') == false)
{
    die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly');
} else {
/*---------------------------------------------------------------------------------------------------------------*/
// merge extended system_permission  
    $system_permissions = array_flip($system_permissions);
// Get system permissions
    $system_permissions = (@$bResetSystem?array():$system_permissions);
    function getSystemDefaultPermission(){
        global $database;
        $sqlAdmin = 'SELECT `system_permissions` FROM `'.TABLE_PREFIX.'groups` '
                  . 'WHERE `group_id`=\'1\' ';
        $sPermissions = $database->get_one($sqlAdmin);
        return (@$database->get_error()?:$sPermissions);
    }
/*---------------------------------------------------------------------------------------------------------------*/
    function getSystemFromRequest($aRequestVars=null)
    {
        global $bResetSystem;
        if ($bResetSystem){return null;}
        $aPermissions = array_flip(explode(',', getSystemDefaultPermission()));
        // define Lambda-Callback for sanitize POST arguments   secunia 2010-92-2
        $cbSanitize = (function($sValue) { $sValue = preg_replace('/[^a-z0-9_-]/i', '', $sValue); return $sValue;});
        $aPermissions = (is_array($aPermissions) ? $aPermissions : array());
        $aPermissions = array_map($cbSanitize, $aPermissions);
        $aPermissions = array_intersect_key($aRequestVars, $aPermissions);
        return $aPermissions;
    }
/*---------------------------------------------------------------------------------------------------------------*/
    function getSystemPermissions($aRequestVars=null)
    {
        $aPermissions = array();
        if (!$aRequestVars){return $aPermissions;}
        $aValidType = $aValidView = $aValidAddons = $aValidAccess = $aValidSettings = array();
        $aTmpPermissions  = getSystemFromRequest($aRequestVars);
        if (($aTmpPermissions)){
            $aValidType     = preg_replace('/^(.*?)_.*$/', '\1', array_keys($aTmpPermissions));
            $aValidView     = preg_replace('/^(.*)/', '\1_view', $aValidType);
            $aValidAddons   = preg_replace('/^(modules.*|templates.*|languages.*)$/', 'addons', $aValidView);
            $aValidAccess   = preg_replace('/^(groups.*|users.*)$/', 'access', $aValidView);
            $aValidSettings = preg_replace('/^(settings.*)$/', 'settings_basic', $aValidView);
            $aPermissions   = array_merge(
                              $aTmpPermissions, 
                              array_flip($aValidType), 
                              array_flip($aValidView), 
                              array_flip($aValidAccess),
                              array_flip($aValidAddons),
                              array_flip($aValidSettings) 
                              );
            ksort ($aPermissions,  SORT_NATURAL|SORT_FLAG_CASE);
        }
        return $aPermissions;
    }
    $aRequestSystemPermissions = getSystemPermissions($aRequestVars);
/* WB283 SP4 Fixes ***************************************************/
    // clean up system_permission
    $system_permissions = ($bAdvancedSave ? array_intersect_key($aRequestSystemPermissions, $system_permissions):$system_permissions);
    $aSystemPermissions = array_merge($aRequestSystemPermissions, $system_permissions);
    $aSystemPermissions = (@$bResetSystem?array():$aSystemPermissions);
    ksort ($aSystemPermissions,  SORT_NATURAL|SORT_FLAG_CASE);
    // Implode system permissions
    $aAllowedSystemPermissions = array();
/*------------------------------------------------------------------------------------------------------------*/
    foreach ($aSystemPermissions as $sName => $sValue) {
        $aAllowedSystemPermissions[] = $sName;
    }
    $system_permissions = implode(',', $aAllowedSystemPermissions);
/*------------------------------------------------------------------------------------------------------------*/
    function getPermissionsFromPost($sType, $bReset=false)
    {
        // define Lambda-Callback for sanitize POST arguments   secunia 2010-92-2
        $cbSanitize = function($sValue) { $sValue = preg_replace('/[^a-z0-9_-]/i', '', $sValue); return $sValue; };
        $aPermissions = $GLOBALS['admin']->get_post($sType.'_permissions');
        $aPermissions = is_array($aPermissions) ? $aPermissions : array();
        $aPermissions = array_map($cbSanitize, $aPermissions);
        $sOldWorkingDir = getcwd();
        chdir(WB_PATH.'/'.$sType.'s/');
        $aAvailableItemsList = glob('*', GLOB_ONLYDIR|GLOB_NOSORT);
        chdir($sOldWorkingDir);
        $aPermissions = (@$bReset?array():$aPermissions);
        $aUncheckedItems = array_diff($aAvailableItemsList, $aPermissions);
        return implode(',', $aUncheckedItems);
    }
    // Get module permissions
    $module_permissions   = getPermissionsFromPost('module', $bResetModules);
    // Get template permissions
    $template_permissions = getPermissionsFromPost('template', $bResetTemplates);
}

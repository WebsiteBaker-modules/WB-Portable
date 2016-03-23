<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @copyright       WebsiteBaker Org. e.V.
 * @author          Christian Sommer
 * @author          Dietmar Wöllbrink
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: tool.php 1520 2011-11-09 00:12:37Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/output_filter/tool.php $
 * @lastmodified    $Date: 2011-11-09 01:12:37 +0100 (Mi, 09. Nov 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

        $debugMessage = '';
        $js_back = ADMIN_URL.'/admintools/tool.php';
        $ToolUrl = ADMIN_URL.'/admintools/tool.php?tool='.$sAddonName;
        if( !$admin->get_permission($sAddonName,'module' ) ) {
            $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
        }
        $SettingsDenied = array( 'at_replacement', 'dot_replacement', 'email_filter', 'mailto_filter', 'OutputFilterMode' );
        if ($database->field_exists( TABLE_PREFIX.'mod_output_filter', 'sys_rel')) {
            include('cmdUpgrade.inc' );
        } else {
        }

        if (is_readable(dirname(__DIR__).'/OutputFilterApi.php')) {
            if (!function_exists('getOutputFilterSettings')) {
                require(dirname(__DIR__).'/OutputFilterApi.php');
            }
// read settings from the database to show
        $aFilterSettings = getOutputFilterSettings();

// extended defaultSettings for email filter
        $aEmailDefaults = array();
        if( $aFilterSettings['Email'] ) {
            $aEmailSettings = array(  );
            $aEmailDefaults = array(
                'at_replacement'  => '@',
                'dot_replacement' => '.',
                'email_filter'    => '0',
                'mailto_filter'   => '0'
            );
        } else {
            $aEmailSettings = array(
                'at_replacement'  => '@',
                'dot_replacement' => '.',
                'email_filter'    => '0',
                'mailto_filter'   => '0'
            );
        }
        $aDefaultSettings = array_diff_key( $aFilterSettings, $aEmailSettings );
        // preset settings as default
        $aFiles = glob(WB_PATH.'/modules/'.$sAddonName.'/filters/*');
        $getDefaultSettings = (function() use ( $sAddonName, $aDefaultSettings, $aFiles ){
            foreach ( $aFiles  as $sFilterFile) {
                $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', $sFilterFile);
                $aDefaultSettings[$key] = '0';
            }
          return $aDefaultSettings;
        });
        $aDefaultSettings = array_merge( $getDefaultSettings(), $aEmailDefaults );
        $aAllowedFilters  = array_keys ( $aDefaultSettings );
        $aFilterExists    = array_diff ( $aAllowedFilters, $SettingsDenied );
        // remove not existing filters from table
        foreach ( $aFilterExists  as $sFilterName) {
            $sFilterFile = WB_PATH.'/modules/'.$sAddonName.'/filters/filter'.$sFilterName.'.php';
            if( !file_exists( $sFilterFile ) ) {
                $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_output_filter`'
                      . 'WHERE `name` = \''.$sFilterName.'\'';
                if( $database->query( $sql ) ){
                    unset($aDefaultSettings[$sFilterName]);
                }
            }
        }
//        $aAllowedFilters  = array_keys ( $aDefaultSettings );
// Create new template object   
        $oTpl = new Template( $sAddonThemePath );
//        $oTpl->debug = true;
        $oTpl->set_file('page', 'tool.htt');
        $oTpl->set_block('page', 'main_block', 'main');
        $oTpl->set_block('main_block', 'headline_block', 'headline');
        $oTpl->set_var('FTAN', $admin->getFTAN());
        $oTpl->set_var($TEXT);
        $oTpl->set_var('ADMIN_URL', $oReg->AcpUrl);
        $oTpl->set_var('MODULE_NAME', $sAddonName); 
        $msgTxt = '';
        $msgCls = 'msg-box';

        $msgTxt = $module_description;
        $msgCls = 'info-box';

        $oTpl->set_var('TOOL_NAME', $toolName); 
        $oTpl->set_var('REQUEST_URI', $_SERVER['REQUEST_URI']);
        $oTpl->set_var('CANCEL_URL', $oReg->AcpUrl.'/admintools/index.php');
        $oTpl->set_var('TOOL_URL', $oReg->AcpUrl.'/admintools/tool.php?tool='.$sAddonName);
//        $oTpl->set_var($MESSAGE);
        $oTpl->set_var($MOD_MAIL_FILTER);
        $oTpl->set_var($HEADING);
// check if data was submitted
        if($doSave) {
    // save changes 
            $oTpl->parse('headline', 'headline_block', true);
            $oTpl->set_var('TOOL_URL', $oReg->AcpUrl.'/admintools/tool.php?tool='.$sAddonName);
            $oTpl->set_var('CANCEL_URL', $oReg->AcpUrl.'/admintools/tool.php?tool='.$sAddonName);
            include(__DIR__.'/cmdSave.inc');
            $aFilterSettings = getOutputFilterSettings();
        } else {
            $oTpl->set_block('main_block', 'headline_block', '');
            $oTpl->set_var('CANCEL_URL', $oReg->AcpUrl.'/admintools/index.php');
        }
        $oTpl->set_block('main_block', 'core_info_block', 'core_info');
        if( $debugMessage != '') {
        // write message box if needed
//            echo '<div class="'.$msgCls.'">'.$msgTxt.'</div>';
            $oTpl->set_var('CORE_MSGCLS', $msgCls); 
            $oTpl->set_var('CORE_MSGTXT', $print_r); 
            $oTpl->parse('core_info', 'core_info_block', true);
        } else {
            $oTpl->set_block('core_info', 'core_info_block', '');
        }
        $oTpl->set_block('main_block', 'message_block', 'message');
        if( $msgTxt != '') {
        // write message box if needed
//            echo '<div class="'.$msgCls.'">'.$msgTxt.'</div>';
            $oTpl->set_var('MSGCLS', $msgCls); 
            $oTpl->set_var('MSGTXT', $msgTxt); 
        }
        $oTpl->parse('message', 'message_block', true);

        $oTpl->set_var($aFilterSettings);
        $oTpl->set_block('main_block', 'filter_block', 'filter_list');
        foreach($aFilterSettings as $sFilterName => $sFilterValue)
        {
            if ( in_array( $sFilterName, $SettingsDenied ) ) { continue; }
            $oTpl->set_var('FVALUE', $sFilterValue);
            $oTpl->set_var('FNAME', $sFilterName);
            $oTpl->set_var('FCHECKED', (($sFilterValue=='1') ? ' checked="checked"' : '') );
            $oTpl->parse('filter_list', 'filter_block', true);
        }
// enable/disable extended email filter settings
        $oTpl->set_block('main_block', 'email-filter_block', 'email-filter');
        if( $aFilterSettings['Email'] ) {
            $oTpl->set_var('EMAIL_FILTER_CHECK',  (($aFilterSettings['email_filter']) ? ' checked="checked"' : '') );
            $oTpl->set_var('MAILTO_FILTER_CHECK', (($aFilterSettings['mailto_filter']) ? ' checked="checked"' : '') );
            $oTpl->parse('email-filter', 'email-filter_block', true);
        } else {
            $oTpl->set_block('main_block', 'email-filter_block', '');
        }
        // write out header if needed
        if(!$admin_header) { $admin->print_header(); }
    // Parse template objects output
            $oTpl->parse('main', 'main_block', true);
            $oTpl->pparse('output', 'page');
    }

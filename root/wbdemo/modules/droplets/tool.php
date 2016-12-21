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
 * @version         $Id: tool.php 44 2016-09-22 08:43:36Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/tool.php $
 * @lastmodified    $Date: 2016-09-22 10:43:36 +0200 (Do, 22. Sep 2016) $
 *
 */
    function executeDropletTool()
    {
/* -------------------------------------------------------- */
        $sAddonName = basename(__DIR__);
/*
*/
        if (is_readable(dirname(__DIR__).'/SimpleRegister.php')) {
            require (dirname(__DIR__).'/SimpleRegister.php');
        }
        if (!isset($oReg) && class_exists('WbAdaptor', false)){
              $oReg = WbAdaptor::getInstance();
// backward compatibilty for upgrade, install, uninstall called from core
        }
/*******************************************************************************************/
//      SimpleCommandDispatcher
/*******************************************************************************************/
        if (is_readable(__DIR__.'/SimpleCommandDispatcher.inc')) {
            require (__DIR__.'/SimpleCommandDispatcher.inc');
        }

        $database    = $oDb;
        $wb = $admin = $oApp;
        if(!function_exists('getUniqueName')) { require($sAddonPath.'/droplets.functions.php'); }
        $ToolUrl  = $oReg->AcpUrl.'admintools/tool.php?tool=droplets';
        $ApptoolLink = $oReg->AcpUrl.'admintools/index.php';
        // create default placeholder array for templates htt or Twig use
        $aLang = array_merge($HEADING,$MENU,$TEXT,$DR_TEXT,$Droplet_Header,$Droplet_Message,$Droplet_Help,$Droplet_Import);
        $aTplDefaults = array (
              'ToolUrl' => $ToolUrl,
              'sAddonUrl' => $sAddonUrl,
              'ApptoolLink' => $ApptoolLink,
              'sAddonThemeUrl'  => $sAddonThemeUrl,
              );
        $output = '';
        if ( !class_exists('msgQueue', false) ) { require($oReg->AppPath.'/framework/class.msg_queue.php'); }
        msgQueue::clear();
        if( !$oApp->get_permission($sAddonName,'module' ) ) {
            $oApp->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
            exit();
        }
        $sOverviewDroplets = $TEXT['LIST_OPTIONS'].' '.$DR_TEXT['DROPLETS'];
        // prepare to get parameters (query)) from this URL string e.g. modify_droplet?droplet_id
        $aQuery = array('command'=>'overview');
        $sql = '';
        $aRequestVars = $_REQUEST;
        $aParseUrl  = ( isset($aRequestVars['command'])?  parse_url ($aRequestVars['command']): $aQuery );
        // sanitize command from compatibility file
        $action = preg_replace(
            '/[^a-z\/0-1_]/siu',
            '',
            (isset($aParseUrl['path']) ? $aParseUrl['path'] : 'overview')
        );
        $sCommand = $sAddonPath.'/commands/'.$action.'.php';
        $subCommand = (@$aRequestVars['subCommand']?:$action);
        if ( isset( $aParseUrl['query']) ) { parse_str($aParseUrl['query'], $aQuery); }
//        if( !function_exists( 'make_dir' ) ) { require($oReg->AppPath.'/framework/functions.php');  }
        ob_start();
        extract($aQuery, EXTR_PREFIX_SAME, "dr");
        switch ($action):
            case 'add_droplet':
            case 'copy_droplet':
                $iDropletAddId = ($oApp->checkIDKEY($droplet_id, false, ''));
                if ( is_readable($sCommand)) { include ( $sCommand ); }
                $sCommand = $sAddonPath.'/commands/'.'rename_droplet.php';
            case 'rename_droplet':
                if ( is_readable($sCommand)) { include ( $sCommand ); }
                $sCommand = $sAddonPath.'/commands/'.'overview.php';
            case 'modify_droplet':
            case 'backup_droplets':
            case 'import_droplets':
                if (is_readable($sCommand)) { include ( $sCommand ); }
                break;
            case 'save_rename':
                $droplet_id = $aRequestVars['CopyDropletId'];
//                $droplet_id = ($oApp->checkIDKEY($droplet_id, false, ''));
                if ( is_readable($sCommand)) { include ( $sCommand ); }
                $sCommand = $sAddonPath.'/commands/'.'overview.php';
                if (is_readable($sCommand)) { include ( $sCommand ); }
                break;
            case 'save_droplet':
                $droplet_id = $aRequestVars['droplet_id'];
            case 'ToggleStatus':
            case 'delete_droplet':
                $droplet_id = ($oApp->checkIDKEY($droplet_id, false, ''));
            case 'restore_droplets':
            case 'call_help':
            case 'call_import':
            case 'select_archiv':
            case 'delete_archiv':
                if ( is_readable($sCommand)) { include ( $sCommand ); }
            default:
                $sCommand = $sAddonPath.'/commands/'.'overview.php';
                if (is_readable($sCommand)) { include ( $sCommand ); }
                break;
        endswitch;
        $output = ob_get_clean();
        if( ($msg = msgQueue::getSuccess()) != '')
        {
            $output = $oApp->print_success($msg, $ToolUrl ).$output;
        }
        if( ($msg = msgQueue::getError()) != '')
        {
            $output = $oApp->print_error($msg, $ToolUrl).$output;
        }
        print $output;
        $oApp->print_footer();
    } // end executeDropletTool
/* -------------------------------------------------------------------------------------------- */
/*                                                                                              */
/* -------------------------------------------------------------------------------------------- */
    if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
    if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
    $oApp = new admin('admintools', 'admintools', false);
    $requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
    $aRequestVars  = (isset(${$requestMethod})) ? ${$requestMethod} : null;
    executeDropletTool();
    exit;
// end of file

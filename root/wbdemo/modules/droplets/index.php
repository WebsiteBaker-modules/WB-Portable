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
 * @version         $Id: index.php 1457 2011-06-25 17:18:50Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/index.php $
 * @lastmodified    $Date: 2011-06-25 19:18:50 +0200 (Sa, 25. Jun 2011) $
 *
 */
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }

$admin = new admin('admintools', 'admintools');

$ModuleRel = '/modules/'.basename(__DIR__).'/';
$ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';
$ToolUrl = ADMIN_URL.'/admintools/tool.php?tool=droplets';
$admintool_link = ADMIN_URL.'/admintools/index.php';
$sStyleFolder ='templates/default/';
$output = '';
if ( !class_exists('msgQueue', false) ) { require(WB_PATH.'/framework/class.msg_queue.php'); }
msgQueue::clear();
// Load Language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}
$sOverviewDroplets = $TEXT['LIST_OPTIONS'].' '.$DR_TEXT['DROPLETS'];

$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$aRequestVars  = (isset(${$requestMethod})) ? ${$requestMethod} : null;

// prepare to get parameters (query)) from this URL string e.g. modify_droplet?droplet_id
$aQuery = array();
$sql = '';
$action = 'show';
$aParseUrl  = ( isset($aRequestVars['command'])?  parse_url ($aRequestVars['command']): array() ); 
$sCommand   = ( isset( $aParseUrl['path'])   ? WB_PATH.$ModuleRel.'commands/'.$aParseUrl['path'].'.php' : '' );
$action     = ( isset($aParseUrl['path']) ? ($aParseUrl['path']) : $action );
if ( isset( $aParseUrl['query']) ) { parse_str($aParseUrl['query'], $aQuery); }
if( !function_exists( 'make_dir' ) )  {  require(WB_PATH.'/framework/functions.php');  }

    switch ($action):
        case 'add_droplet':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $sCommand = $ModulePath.'commands/'.'modify_droplet.php';
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'modify_droplet':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            $droplet_id = intval($admin->checkIDKEY($droplet_id, false, ''));
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'backup_droplets':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'import_droplets':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            if ( is_readable($sCommand)) {  require ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'restore_droplets':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            if ( is_readable($sCommand)) {  require ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'copy_droplets':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            $droplet_id = intval($admin->checkIDKEY($droplet_id, false, ''));
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $sCommand = $ModulePath.'commands/'.'modify_droplet.php';
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'delete_droplet':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $output = ob_get_clean();
            break;
        case 'delete_archiv':
            ob_start();
            extract($aQuery, EXTR_PREFIX_SAME, "dr");
            if ( is_readable($sCommand)) {  include ( $sCommand ); }
            $output = ob_get_clean();
            break;
        default:
            msgQueue::add($DR_TEXT['INVALID_BACK'] );
            break;
    endswitch;

        if( ($msg = msgQueue::getSuccess()) != '')
        {
            $output = $admin->print_success($msg, $ToolUrl ).$output;
        }
        if( ($msg = msgQueue::getError()) != '')
        {
            $output = $admin->print_error($msg, $ToolUrl).$output;
        }
      print $output;
      $admin->print_footer();
?><script type="text/javascript">
<!--
domReady(function() {
    LoadOnFly('head', WB_URL+"<?php echo $ModuleRel; ?>backend.css");
});
-->
</script>
<script src="<?php echo $ModuleUrl; ?>backend_body.js" ></script>

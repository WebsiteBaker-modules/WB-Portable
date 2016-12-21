<?php
/**
 *
 * @category        admin
 * @package         login
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 *
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
if ( !class_exists('SysInfo', false) ) { require(WB_PATH.'/framework/SysInfo.php'); }

$admin = new admin('##skip##','start', false, false);
if (!class_exists('Twig_Autoloader')) {
    $e = 'This modul requires TWIG. please download from <a href="http://wiki.websitebaker.org/doku.php/en/downloads" target="_blank" ><b>wiki.wb</a></b>...';
    throw new RuntimeException($e);
} else {
    $aWritablePaths = array(
        'languages',
        'media',
        'modules',
        'pages',
        'temp',
        'templates',
        'var',
        );
    $oInfo = new SysInfo();
    if (is_readable(WB_PATH.'/modules/SimpleRegister.php')){
      require WB_PATH.'/modules/SimpleRegister.php';
    }

    if(is_object($oReg->Db->DbHandle)) {
        $title = "MySQLi Info";
        $server_info          = mysqli_get_server_info($oReg->Db->DbHandle);
        $host_info            = mysqli_get_host_info($oReg->Db->DbHandle);
        $proto_info           = mysqli_get_proto_info($oReg->Db->DbHandle);
        $client_info          = mysqli_get_client_info($oReg->Db->DbHandle);
        $client_encoding      = mysqli_character_set_name($oReg->Db->DbHandle);
        $status = explode('  ', mysqli_stat($oReg->Db->DbHandle));
    }
// Create new template object with phplib
    $aTwigData = array(
        'ADMIN_URL' => $oReg->AcpUrl,
        'THEME_URL' => $oReg->ThemeUrl,
        'INFO_URL' =>  $oReg->AcpUrl.'start/wb_info.php',
        'sAddonThemeUrl' => THEME_URL.'',
        'getInterface' => $oInfo->getInterface(),
        'isCgi' => $oInfo->isCgi(),
        'WbVersion' => $oInfo->getWbVersion(true),
        'getOsVersion' => $oInfo->getOsVersion(true),
        'aWritablePaths' => $oInfo->checkFolders($aWritablePaths),
        'getSqlServer' => $oInfo->getSqlServer(),
        'client_encoding' => $client_encoding,
        'php_version' => PHP_VERSION,
        'oReg' => $oReg,
        'server' => $oReg->Db->db_handle,
        'client_info' => $client_info,
        'server_info' => $server_info,
    );
    $aTwigloader = array('header'=> 'header.twig',
                                    'content' => 'content.twig',
                                    'sysinfo' => 'sysInfo.twig',
                                    'footer' => 'footer.twig'
                           );
    if (is_readable($oReg->ThemePath.'templates/'.$aTwigloader['sysinfo'])){
        $loader = new Twig_Loader_Filesystem($oReg->ThemePath . 'templates');
        $Twig = new Twig_Environment(
            $loader, array(
            'autoescape'       => false,
            'cache'            => false,
            'strict_variables' => false,
            'debug'            => false,
            ));
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aTwigData['aWritablePaths'] ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
/*-- finalize the page -----------------------------------------------------------------*/
        echo $Output = $Twig->Render($aTwigloader['sysinfo'], $aTwigData);//
    } else {
    print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
    print_r( $aTwigloader['sysinfo'] ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    }
}
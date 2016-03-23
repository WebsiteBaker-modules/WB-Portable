<?php
/**
 *
 * @category        frontend
 * @package         account
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: logout.php 1599 2012-02-06 15:59:24Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/account/logout.php $
 * @lastmodified    $Date: 2012-02-06 16:59:24 +0100 (Mo, 06. Feb 2012) $
 *
 */

if( !defined( 'WB_PATH' ) ){ require(dirname(__DIR__).'/config.php'); }

if(isset($_COOKIE['REMEMBER_KEY'])) {
    setcookie('REMEMBER_KEY', '', time()-3600, '/');
}
$redirect_url = ((isset($_SESSION['HTTP_REFERER']) && $_SESSION['HTTP_REFERER'] != '') ? $_SESSION['HTTP_REFERER'] : WB_URL );
$redirect_url = ( isset($redirect) && ($redirect!='') ? $redirect : $redirect_url);
$page_id = @$_SESSION['PAGE_ID'] ?: 0;

$_SESSION['USER_ID'] = null;
$_SESSION['GROUP_ID'] = null;
$_SESSION['GROUPS_ID'] = null;
$_SESSION['USERNAME'] = null;
$_SESSION['PAGE_PERMISSIONS'] = null;
$_SESSION['SYSTEM_PERMISSIONS'] = null;
$_SESSION = array();

session_unset();
unset($_COOKIE[session_name()]);
session_destroy();

if( !FRONTEND_LOGIN && INTRO_PAGE) {
    header('Location: '.WB_URL.'/index.php');
    exit;
} else {
    $no_intro = true;
    require(WB_PATH.'/index.php');
}


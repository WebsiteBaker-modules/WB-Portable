<?php
/**
 *
 * @category        admin
 * @package         login
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: index.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/login/index.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
*/

if ( !defined('WB_PATH') ){ require(dirname(dirname(__DIR__))."/config.php"); }
if( !class_exists('Login', false) ){ require(WB_PATH."/framework/class.Login.php"); }
if( !class_exists('frontend', false) ){ require(WB_PATH."/framework/class.frontend.php"); }

$username_fieldname = 'username';
$password_fieldname = 'password';
if(defined('SMART_LOGIN') && SMART_LOGIN == 'true') {
    $sTmp = '_'.substr(md5(microtime()), -8);
    $username_fieldname .= $sTmp;
    $password_fieldname .= $sTmp;
}

$admin = new frontend();

// Setup template object, parse vars to it, then parse it
$WarnUrl = str_replace(WB_PATH,WB_URL,$admin->correct_theme_source('warning.html'));
$LoginTpl = 'login.htt';
$ThemePath = dirname($admin->correct_theme_source($LoginTpl));
$thisApp = new Login( array(
        'MAX_ATTEMPS'           => 3,
        'WARNING_URL'           => $WarnUrl,
        'USERNAME_FIELDNAME'    => $username_fieldname,
        'PASSWORD_FIELDNAME'    => $password_fieldname,
        'REMEMBER_ME_OPTION'    => SMART_LOGIN,
        'MIN_USERNAME_LEN'      => 2,
        'MIN_PASSWORD_LEN'      => 3,
        'MAX_USERNAME_LEN'      => 100,
        'MAX_PASSWORD_LEN'      => 100,
        'LOGIN_URL'             => ADMIN_URL."/login/index.php",
        'DEFAULT_URL'           => ADMIN_URL."/start/index.php",
        'TEMPLATE_DIR'          => $ThemePath,
        'TEMPLATE_FILE'         => $LoginTpl,
        'FRONTEND'              => false,
        'FORGOTTEN_DETAILS_APP' => ADMIN_URL."/login/forgot/index.php",
        'USERS_TABLE'           => TABLE_PREFIX."users",
        'GROUPS_TABLE'          => TABLE_PREFIX."groups",
    )
);


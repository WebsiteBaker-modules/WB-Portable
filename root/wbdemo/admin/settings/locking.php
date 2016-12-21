<?php
/**
 *
 * @category        admin
 * @package         login
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.4
 * @requirements    PHP 5.4 and higher
 * @version         $Id: locking.php 2109 2014-11-25 13:15:56Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/admin/settings/locking.php $
 * @lastmodified    $Date: 2014-11-25 14:15:56 +0100 (Di, 25. Nov 2014) $
 *
 */
if (!defined('WB_PATH')) {
    $sStartupFile = dirname(dirname(__DIR__)).'/config.php';
    if (is_readable($sStartupFile)) {
        require($sStartupFile);
    } else {
        throw new Exception(
                            'tried to read a nonexisting or not readable startup file ['
                          . basename(dirname($sStartupFile)).'/'.basename($sStartupFile).']!!'
        );
    }
}
$oDb = $GLOBALS['database'];
$oTrans = Translate::getInstance();
$oTrans->enableAddon('admin\\settings');

if (!class_exists('admin', false)) {require (WB_PATH.'/framework/class.admin.php');}
$admin = new admin('Start', 'settings', false, false);

if ($admin->get_user_id() == 1) {
    $val = (((int)(defined('SYSTEM_LOCKED') ? SYSTEM_LOCKED : 0)) + 1) % 2;
    db_update_key_value('settings', 'system_locked', $val);
}
// redirect to backend
header('Location: ' . ADMIN_URL . '/index.php');
exit();

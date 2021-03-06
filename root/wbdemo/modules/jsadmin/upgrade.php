<?php
/**
 *
 * @category        modules
 * @package         jsadmin
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http:/websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/upgrade.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

$msg = '';
$sTable = TABLE_PREFIX.'mod_jsadmin';
if(($sOldType = $database->getTableEngine($sTable))) {
    if(('myisam' != strtolower($sOldType))) {
        if(!$database->query('ALTER TABLE `'.$sTable.'` Engine = \'MyISAM\' ')) {
            $msg = $database->get_error();
        }
    }
} else {
    $msg = $database->get_error();
}
// ------------------------------------
    $sInstallStruct = __DIR__.'/install-struct.sql';
    if (!is_readable($sInstallStruct)) {
        $msg[] = '<strong>\'missing or not readable file [install-struct.sql]\'</strong> '.$FAIL.'<br />';
        $iErr = true;
    } else {
        $database->SqlImport($sInstallStruct, TABLE_PREFIX, true );
    }

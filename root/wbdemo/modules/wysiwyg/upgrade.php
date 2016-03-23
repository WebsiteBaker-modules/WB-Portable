<?php
/**
 *
 * @category        modules
 * @package         wysiwyg
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade.php 1576 2012-01-16 17:29:11Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/wysiwyg/upgrade.php $
 * @lastmodified    $Date: 2012-01-16 18:29:11 +0100 (Mo, 16. Jan 2012) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
$msg = '';
$sTable = TABLE_PREFIX.'mod_wysiwyg';
if(($sOldType = $database->getTableEngine($sTable))) {
    if(('myisam' != strtolower($sOldType))) {
        if(!$database->query('ALTER TABLE `'.$sTable.'` Engine = \'MyISAM\' ')) {
            $msg = $database->get_error();
        }
    }
} else {
    $msg .= $database->get_error().'<br />';
}
// sanitize URLs inside mod_wysiwyg.content ----------------------------
    $msg = '';
    $sRelUrl = preg_replace('/^https?:\/\/[^\/]+(.*)/is', '\1', WB_URL);
    $sDocumentRootUrl = str_replace($sRelUrl, '', WB_URL);
    $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
    $sql = 'SELECT `content`, `section_id` FROM `'.TABLE_PREFIX.'mod_wysiwyg`';
    if (($oInstances = $database->query($sql))) {
        while (($aInstance = $oInstances->fetchRow(MYSQLI_ASSOC))) {
            // add $sDocumentRootUrl to relative URLs
            $aPatterns = array(
                '/(<[^>]*?=\s*\")(\/+)([^\"]*?\"[^>]*?)/is',
                '/(<[^>]*=\s*")('.preg_quote($sMediaUrl, '/').')([^">]*".*>)/siU'
            );
            $aReplacements = array(
                '\1'.$sDocumentRootUrl.'/\3',
                '$1{SYSVAR:MEDIA_REL}$3'
            );
            $aInstance['content'] = preg_replace($aPatterns, $aReplacements, $aInstance['content']);
            $sql = 'UPDATE `'.TABLE_PREFIX.'mod_wysiwyg` '
                 . 'SET `content`=\''.$database->escapeString($aInstance['content']).'\' '
                 . 'WHERE `section_id`='.(int)$aInstance['section_id'];
            if (!$database->query($sql)) {
                $msg = $database->get_error();
                break;
            }
        }
    } else { $msg = $database->get_error(); }

// ---------------------------------------------------------------------

$sql  = 'UPDATE `'.$sTable.'` SET '
      . '`content` = REPLACE(`content`, \'"'.WB_URL.MEDIA_DIRECTORY.'\', \'"{SYSVAR:MEDIA_REL}\')';
if (!$database->query($sql)) {
    $msg .= $database->get_error().'<br />';
}

// ------------------------------------
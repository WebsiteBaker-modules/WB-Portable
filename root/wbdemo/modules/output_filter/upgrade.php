<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @copyright       WebsiteBaker Org. e.V.
 * @author          Dietmar Wöllbrink
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/output_filter/upgrade.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */
// Must include code to stop this file being access directly
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) { throw new RuntimeException('Illegal access'); }
/* -------------------------------------------------------- */

    $sTable = TABLE_PREFIX.'mod_output_filter';
    $i = (!isset($i) ? 1 : $i);
    $OK   = "<span class=\"ok\">OK</span>";
    $FAIL = "<span class=\"error\">FAILED</span>";
    $iErr = false;
    $msg = array();
    $sSqlCreate = '`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\','
                . '`value` text COLLATE utf8_unicode_ci NOT NULL, '
                . 'PRIMARY KEY (`name`)'
                . ')ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
    $sql = 'CREATE TABLE IF NOT EXISTS `'.$sTable.'` ('.$sSqlCreate;
    $database->query($sql);
    $msg[] = '<div style="margin:1em auto;font-size:1.1em;">';
    $msg[] = '<h4>Step '.$i++.': Updating Output Filter</h4>';
    // check if table already upgraded
    if ($database->field_exists($sTable, 'email_filter')) {
        // read old settings first
        $sql = 'SELECT * FROM `'.$sTable.'`';
        if (($oSettings = $database->query($sql))) {
            if (!($aOldSettings = $oSettings->fetchRow(MYSQLI_ASSOC))) {
                $msg[] = '<strong>\'Output Filter backup old settings\'</strong> '.$FAIL.'<br />';
                $iErr = true;
            }
            $sql = 'DROP TABLE IF EXISTS `'.$sTable.'`';
            if (!($database->query($sql))) {
                $msg[] = '<strong>\'Output Filter drop old table\'</strong> '.$FAIL.'<br />';
                $iErr = true;
            }
            $sql = 'CREATE TABLE `'.$sTable.'` ('.$sSqlCreate;
            if ($database->query($sql)) {
                if ($aOldSettings) {
                // restore old settings if there any
                    $sNameValPairs = '';
                    foreach ($aOldSettings as $index => $val) {
                        $sNameValPairs .= ', (\''.$index.'\', \''.$database->escapeString($val).'\')';
                    }
                    $sValues = ltrim($sNameValPairs, ', ');
                    $sql = 'REPLACE INTO `'.$sTable.'` (`name`, `value`) '
                         . 'VALUES '.$sValues;
                    if (!$database->query($sql)) {
                        $msg[] = '<strong>\'Output Filter restore old settings\'</strong> '.$FAIL.'<br />';
                        $iErr = true;
                    }
                }
            } else {
                $msg[] = '<strong>\'Output Filter create new table\'</strong> '.$FAIL.'<br />';
                $iErr = true;
            }
        } else {
            $msg[] = '<strong>\'Output Filter read old settings\'</strong> '.$FAIL.'<br />';
            $iErr = true;
        }
        if (!$iErr) {
            $msg[] = '<strong>\'Output Filter successful updated\'</strong> '.$OK.'<br />';
        }
    } else {
        $msg[] = '<strong>\'Output Filter already updated\'</strong> '.$OK.'<br />';
    }
    $msg[] = '</div>';
    print implode("\n", $msg)."\n";

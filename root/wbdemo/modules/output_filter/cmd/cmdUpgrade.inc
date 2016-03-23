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
    $aOldSettings = array();
    $msg = array(
        '<div style="margin:1em auto;font-size:1.1em;">',
        '<h4>Step '.$i++.': Updating Output Filter Settings</h4>'
    );
    $sInstallStruct = $sAddonPath.'/install-struct.sql';
    if ( !is_readable($sInstallStruct)) {
        $msg[] = '<strong>\'missing or not readable file [install-struct.sql]\'</strong> '.$FAIL.'<br />';
        $iErr = true;
    } else {
        $aFiles = glob(WB_PATH.'/modules/'.$sAddonName.'/filters/*');
        $getDefaultSettings = (function() use ( $database, $sAddonName, $aFiles ){
            $aDefaults = array(
                'at_replacement'  => '@',
                'dot_replacement' => '.',
                'email_filter'    => '1',
                'mailto_filter'   => '1'
            );
            foreach ( $aFiles  as $sFilterFile) {
                $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', $sFilterFile);
                $aDefaults[$key] = '1';
            }
          return $aDefaults;
        });
        $aDefaults =  $getDefaultSettings();
//        $aAllowedFilters = array_keys ( $aDefaults );
        // try to create table if not exists
        $database->SqlImport($sInstallStruct, TABLE_PREFIX, true );
        // read settings first
        $sql = 'SELECT * FROM `'.$sTable.'`';
        // check if table already upgraded
        if ( $bOldStructure = $database->field_exists($sTable, 'sys_rel') ) 
        {
            if (($oSettings = $database->query($sql))) 
            {
              // 
                if (!($aOldSettings = $oSettings->fetchRow(MYSQLI_ASSOC))) {
                    $msg[] = '<strong>\'Output Filter backup old settings\'</strong> '.$FAIL.'<br />';
                    $iErr = true;
                } else {
                    // add new defaults to old settings without invalide values 
                    $aNewSettings = array_intersect_key( $aOldSettings, $aDefaults );
                    $aOldSettings = array_replace_recursive( $aDefaults, $aNewSettings );
                }
            }
        } elseif ( $database->field_exists($sTable, 'name') ) {
              $aOldSettings = $aDefaults;
              // overwrite standardsettings ($aOldSettings)
              $sql = 'SELECT * FROM `'.$sTable.'`';
              if (($oSettings = $database->query($sql))) {
                while( $aSettings = $oSettings->fetchRow( MYSQLI_ASSOC ) ) 
                {
                    $key = $aSettings['name'];
                    $aOldSettings[$key] = $aSettings['value'];
                }
              }
          }
        // delete not existing filter in table
        $SettingsDenied = array( 'at_replacement', 'dot_replacement', 'email_filter', 'mailto_filter', 'OutputFilterMode' );
        $aAllowedFilters  = array_keys ( $aOldSettings );
        $aFilterExists    = array_diff ( $aAllowedFilters, $SettingsDenied );
        foreach ( $aFilterExists  as $sFilterName) {
            $sFilterFile = WB_PATH.'/modules/'.$sAddonName.'/filters/filter'.$sFilterName.'.php';
            if( !file_exists( $sFilterFile ) ) {
                $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_output_filter`'
                      . 'WHERE `name` = \''.$sFilterName.'\'';
                if( $database->query( $sql ) ){
                    unset($aOldSettings[$sFilterName]);
                }
            }
        }
          // drop old table and create new one
          if ($database->SqlImport($sInstallStruct, TABLE_PREFIX, false)) 
          {
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
              $msg[] = '<strong>\'Output Filter recreate table\'</strong> '.$FAIL.'<br />';
              $iErr = true;
          }
//            } else {
//                $msg[] = '<strong>\'Output Filter read old settings\'</strong> '.$FAIL.'<br />';
//                $iErr = true;
//            }
            if (!$iErr) {
                $msg[] = '<strong>\'Output Filter successful updated\'</strong> '.$OK.'<br />';
            }
//        } else {
//            $msg[] = '<strong>\'Output Filter already updated\'</strong> '.$OK.'<br />';
//        }
        unset($getDefaultSettings);
    }
    $msg[] = '</div>';
    print implode("\n", $msg)."\n";


<?php
/**
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * upgrade.php
 * 
 * @category     Module
 * @package      Module_form
 * @subpackage   upgrade
 * @author       Dietmar Wöllbrink <dietmar.woellbrink@websitebaker.org>
 * @author       Werner v.d.Decken <wkl@isteam.de>
 * @copyright    Werner v.d.Decken <wkl@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.0.1
 * @revision     $Revision: 1890 $
 * @link         $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/upgrade.php $
 * @lastmodified $Date: 2013-03-19 11:45:14 +0100 (Di, 19. Mrz 2013) $
 * @since        File available since 17.01.2013
 * @description  xyz
 * 
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_URL')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
}
/* -------------------------------------------------------- */

//if(!function_exists('mod_form_upgrade')){
    function mod_form_upgrade($bDebug=false) {
        global $OK ,$FAIL;
        $oDb = ( @$GLOBALS['database'] ?: null );
        $msg = array();
        $callingScript = $_SERVER["SCRIPT_NAME"];
        // check if upgrade startet by upgrade-script to echo a message
        $tmp = 'upgrade-script.php';
        $globalStarted = substr_compare($callingScript, $tmp,(0-strlen($tmp)),strlen($tmp)) === 0;

        $getMissingTables = (function (array $aTablesList) use ( $oDb )
        {
            $aTablesList = array_flip($aTablesList);
            $sPattern =  addcslashes( TABLE_PREFIX, '%_' );
            $sql = 'SHOW TABLES LIKE \''.$sPattern.'%\'';
            if (($oTables = $oDb->query( $sql ))) {
                while ($aTable = $oTables->fetchRow(MYSQLI_NUM)) {
                    $sTable =  preg_replace('/^'.preg_quote(TABLE_PREFIX, '/').'/s', '', $aTable[0]);
                    if (isset($aTablesList[$sTable])) {
                        unset($aTablesList[$sTable]);
                    }
                }
            }
            return array_flip($aTablesList);
        });

// check for missing tables, if true stop the upgrade
        $aTable = array('mod_form_fields','mod_form_settings','mod_form_submissions');
        $aPackage = $getMissingTables($aTable);

        if( sizeof($aPackage) > 0){
            $msg[] =  'TABLE '.implode(' missing! '.$FAIL.'<br />TABLE ',$aPackage).' missing! '.$FAIL;
            $msg[] = 'Form upgrade failed'." $FAIL";
            if($globalStarted) {
                echo '<strong>'.implode('<br />',$msg).'</strong><br />';
            }
            return ( ($globalStarted==true ) ? $globalStarted : $msg);
        } else {
            for($x=0; $x<sizeof($aTable);$x++) {
                if(($sOldType = $oDb->getTableEngine(TABLE_PREFIX.$aTable[$x]))) {
                    if(('myisam' != strtolower($sOldType))) {
                        if(!$oDb->query('ALTER TABLE `'.TABLE_PREFIX.$aTable[$x].'` Engine = \'MyISAM\' ')) {
                            $msg[] = $oDb->get_error();
                        } else{
                            $msg[] = 'TABLE `'.TABLE_PREFIX.$aTable[$x].'` changed to Engine = \'MyISAM\''." $OK";
                        }
                    } else {
                        $msg[] = 'TABLE `'.TABLE_PREFIX.$aTable[$x].'` has Engine = \'MyISAM\''." $OK";
                    }
                } else {
                    $msg[] = $oDb->get_error();
                }
            }

            $table_name = TABLE_PREFIX.'mod_form_settings';
            $field_name = 'perpage_submissions';
            $description = "INT NOT NULL DEFAULT '10' AFTER `max_submissions`";
            if(!$oDb->field_exists($table_name,$field_name)) {
                $oDb->field_add($table_name, $field_name, $description);
                $msg[] = 'Add field `perpage_submissions` AFTER `max_submissions`';
            } else {
                $msg[] = 'Field `perpage_submissions` already exists'." $OK";
            }
// only for upgrade-script
            if($globalStarted) {
                if($bDebug) {
                    echo '<strong>'.implode('<br />',$msg).'</strong><br />';
                }
            }
        }
        $msg[] = 'Form upgrade successfull finished ';
        if($globalStarted) {
            echo "<strong>Form upgrade successfull finished $OK</strong><br />";
        }
        return ( ($globalStarted==true ) ? $globalStarted : $msg);
    }
//}
// ------------------------------------

$bDebugModus = ((isset($bDebugModus)) ? $bDebugModus : false);
if( is_array($msg = mod_form_upgrade($bDebugModus))) {
    echo '<strong>'.implode('<br />',$msg).'</strong><br />';
}

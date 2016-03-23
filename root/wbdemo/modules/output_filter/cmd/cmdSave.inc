<?php
/*
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * cmdSave.php
 *
 * @category     Addons
 * @package      
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      3.0.1
 * @lastmodified $Date: $
 * @since        File available since 2015-12-17
 * @description  xyz
 */

        if ($admin->checkFTAN()) {

    // take over post - arguments
            $aDatas = array();
            foreach ( $aDefaultSettings as $key => $value ) {
                if( in_array( $key, $aAllowedFilters ) ) {
                    $aDatas[$key]   = (bool)(isset($_POST[$key])
                                                ? intval($_POST[$key])
                                                : $aDefaultSettings[$key]);
                }
           }

            if( $aFilterSettings['Email'] ) {
                $aDatas['email_filter']    = (bool)(isset($_POST['email_filter'])
                                               ? intval($_POST['email_filter'])
                                               : $aDefaultSettings['email_filter']);
                $aDatas['mailto_filter']   = (bool)(isset($_POST['mailto_filter'])
                                               ? intval($_POST['mailto_filter'])
                                               : $aDefaultSettings['mailto_filter']);
                $aDatas['at_replacement']  = ( (isset($_POST['at_replacement']) && $_POST['at_replacement'] != '')
                                               ? trim(strip_tags($_POST['at_replacement']))
                                               : $aDefaultSettings['at_replacement']);
                $aDatas['dot_replacement'] = ( (isset($_POST['dot_replacement']) && $_POST['dot_replacement'] != '')
                                               ? trim(strip_tags($_POST['dot_replacement']))
                                               : $aDefaultSettings['dot_replacement']);
            }

            $sNameValPairs = '';
            foreach ($aDatas as $index => $val) {
                $sNameValPairs .= ', (\''.$index.'\', \''.$database->escapeString($val).'\')';
            }
            $sValues = ltrim($sNameValPairs, ', ');
            $sql = 'REPLACE INTO `'.TABLE_PREFIX.'mod_output_filter` (`name`, `value`) '
                 . 'VALUES '.$sValues;

            if ($database->query($sql)) {
            //anything ok
                $msgTxt = $MESSAGE['RECORD_MODIFIED_SAVED'];
                $msgCls = 'msg-box';
            }else {
            // database error
                $msgTxt = $MESSAGE['RECORD_MODIFIED_FAILED'];
                $msgCls = 'error-box';
            }
        } else {
        // FTAN error
            $msgTxt = $MESSAGE['GENERIC_SECURITY_ACCESS'];
            $msgCls = 'error-box';
        }
// end of file

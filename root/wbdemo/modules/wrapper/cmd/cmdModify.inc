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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * cmdModify.php
 *
 * @category     Addons
 * @package      Addons_wrapper
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      3.0.1
 * @lastmodified $Date: $
 * @since        File available since 17.12.2015
 * @description  xyz
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if (!defined('WB_PATH')) { throw new Exception('Cannot access the addon \"'.basename(__DIR__).'\" directly'); }
/* -------------------------------------------------------- */

    // set default values for a new one or if no matching record found
    $aInstance = array(
        'section_id' => $section_id,
        'page_id'    => $page_id,
        'url'        => '',
        'height'     => 400
    );
    // Get page content
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_'.$sAddonName.'` '
         . 'WHERE `section_id` = '.$section_id;
    if (($oInstances = $database->query($sql))) {
    // try to load an existing record
        if (($aRecord = $oInstances->fetchRow(MYSQLI_ASSOC))) {
            $aRecord['url'] = str_replace(
                                  '{SYSVAR:AppUrl}',
                                  str_replace('\\', '/', WB_URL).'/',
                                  $aRecord['url']
                              );
            $aInstance = $aRecord;
        }
    }
    $aInstance = array_change_key_case($aInstance, CASE_UPPER);
    // complete array with replacements
    $aInstance['WB_URL']      = WB_URL;
    $aInstance['THEME_URL']   = $sAddonThemeUrl;
    $aInstance['THEME_PATH']  = $sAddonThemePath;
    $aInstance['SAVE_URL']    = ADMIN_URL.'/pages/save.php';
//    $aInstance['CANCEL_URL']  = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#wb_'.$section_id;
    $aInstance['CANCEL_URL']  = ADMIN_URL.'/pages/index.php';
    $aInstance['TEXT_URL']    = $TEXT['URL'];
    $aInstance['TEXT_HEIGHT'] = $TEXT['HEIGHT'];
    $aInstance['TEXT_SAVE']   = $TEXT['SAVE'];
    $aInstance['TEXT_BACK']   = $TEXT['BACK'];
    $aInstance['TEXT_CANCEL'] = $TEXT['CANCEL'];
    $aInstance['FTAN']        = $admin->getFTAN();
    // create template object
    $oTpl = new Template($sAddonThemePath);
    $oTpl->set_file('page', 'modify.htt');
    $oTpl->set_block('page', 'main_block', 'main');
    // add array of replacements
    $oTpl->set_var($aInstance);
    // Parse template object
    $oTpl->parse('main', 'main_block', false);
    $oTpl->pparse('output', 'page');

// end of file


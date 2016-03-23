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
 * cmdInstall.php
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

    // get content
    $sql = 'SELECT `url`, `height` FROM `'.TABLE_PREFIX.'mod_wrapper` '
         . 'WHERE `section_id` = '.$section_id;
    if (($oInstances = $database->query($sql))) {
        if (($aInstance = $oInstances->fetchRow(MYSQLI_ASSOC))) {
            $aTplData = array_change_key_case($aInstance, CASE_UPPER);
            $aTplData['NOTICE'] = $MOD_WRAPPER['NOTICE'];
            if (class_exists('Twig_Environment') && is_readable($sAddonTemplatePath.'/view.twig')) {
                // create twig template object
                $oTwig = new Twig_Environment(new Twig_Loader_Filesystem($sAddonTemplatePath));
                echo $oTwig->render('view.twig', $aTplData);
            } else {
                // create phplib template object
                $oTpl = new Template($sAddonTemplatePath);
                $oTpl->set_file('page', 'view.htt');
                $oTpl->set_block('page', 'main_block', 'main');
                $oTpl->set_var($aInstance);
                // Parse template object
                $oTpl->parse('main', 'main_block', false);
                $oTpl->pparse('output', 'page');
            }
        }
    }
// end of file


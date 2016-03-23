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
 *
 * cmdSave.php
 *
 * @category     Addons
 * @package      Addons_wrapper
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      3.0.1
 * @lastmodified $Date: $
 * @since        File available since 2015-12-17
 * @description  xyz
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if (!defined('WB_PATH')) { throw new Exception('Cannot access the addon \"'.basename(__DIR__).'\" directly'); }
/* -------------------------------------------------------- */
    $bBackLink = isset($_POST['pagetree']);

    // Update the mod_wrapper table with the contents
    if (isset($_POST['url'])) {
    // sanitize/validate url
        // first add the local URL if there is no one
        $sNewUrl = ltrim(str_replace('\\', '/', $_POST['url']), '/');
        if (!preg_match('/^https?:\/\/.*$/si', $sNewUrl)) {
            $sNewUrl = WB_URL.'/'.$sNewUrl;
        }
        // validate the URL
        include_once WB_PATH.'/include/idna_convert/idna_convert.class.php';
        $oIdn = new idna_convert();
        $url = $oIdn->encode($sNewUrl);
        $url = (filter_var($url, FILTER_VALIDATE_URL) === false ? '' : $url);
        $url = $oIdn->decode($url);
        unset($oIdn);
        // replace local host by SYSVAR-Tag
        $url = preg_replace(
            '/^'.preg_quote(str_replace('\\', '/', WB_URL).'/', '/').'/si',
            '{SYSVAR:AppUrl}',
            ltrim(str_replace('\\', '/', $url), '/')
        );

    } else { $url = ''; }
    // sanitize/validate height
    $height = (isset($_POST['height']) ? ((intval($_POST['height'])) ?: 400) : 400);
    // prepare SET part of the SQL-statement
    $sqlSet = '`'.TABLE_PREFIX.'mod_wrapper` SET '
            . '`section_id`='.$section_id.', '
            . '`page_id`='.$page_id.', '
            . '`url` = \''.$database->escapeString($url).'\', '
            . '`height` = \''.$database->escapeString($height).'\' ';
    // search for instance of this module in section
    $sql = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_wrapper` '
         . 'WHERE `section_id`='.$section_id;
    if ($database->get_one($sql)) {
    // if matching record already exists run UPDATE
        $sql = 'UPDATE '.$sqlSet.'WHERE `section_id`='.$section_id;
    } else {
    // if no matching record exists INSERT new record
        $sql = 'INSERT INTO '.$sqlSet;
    }
    $database->query($sql);
    // Tells script to update when this page was last updated
    $update_when_modified = true;

// end of file

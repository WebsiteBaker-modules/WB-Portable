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
 * cmdUninstall.php
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
    $sSqlDumpFile = $sAddonPath.'/install-struct.sql';
    if (is_readable($sSqlDumpFile)) {
        // create needet database tables and set default records
        $database->SqlImport($sSqlDumpFile, TABLE_PREFIX, false);
    }
// end of file


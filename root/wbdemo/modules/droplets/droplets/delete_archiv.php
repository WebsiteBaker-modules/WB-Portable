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
 * @category        modules
 * @package         test
 * @subpackage      test
 * @author          Dietmar Wöllbrink
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.4 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 *
 */
 /* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

if( !$admin->checkFTAN() ){
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
    exit();
}
if ( @$aRequestVars['zipFiles'] == '' ) {
    msgQueue::add( $Droplet_Message['GENERIC_MISSING_ARCHIVE_FILE'] );
} else { 

    $sArchFile = WB_PATH.$aRequestVars['zipFiles'];
    $unlink = @unlink($sArchFile);

    if( $unlink==false ) {
        msgQueue::add( $Droplet_Message['ARCHIVE_NOT_DELETED'] );
    } else {
        msgQueue::add( $Droplet_Message['ARCHIVE_DELETED'], true );
    }

}


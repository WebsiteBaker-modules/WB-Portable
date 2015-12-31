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
 * @package         droplets
 * @subpackage      restore_droplets
 * @author          Dietmar Wöllbrink
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.4
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

    $aUnzipDroplets = array();
/**
 * 
*/
     if(!$admin->checkFTAN() ) {
        msgQueue::add($MESSAGE['GENERIC_SECURITY_ACCESS']);
    } elseif(!isset($aRequestVars['restore_id']) || !is_array($aRequestVars['restore_id'])) {
        msgQueue::add($Droplet_Message['MISSING_UNMARKED_ARCHIVE_FILES'] );
    } else {
        $aDroplet = array();
        if( !class_exists('PclZip',false) ) { require( WB_PATH.'/include/pclzip/pclzip.lib.php'); }
        if(!function_exists('insertDropletFile')) { require('droplets.functions.php'); }
      // unzip to buffer and store in DB / fetch ach entry as single process, to surpress buffer overflow 
        foreach($aRequestVars['restore_id'] as $index => $iArchiveIndex ) {
            $oArchive = new PclZip( $aRequestVars['ArchiveFile'] );
            $sDroplet = $oArchive->extract(PCLZIP_OPT_BY_INDEX, $iArchiveIndex,
                                           PCLZIP_OPT_EXTRACT_AS_STRING);
            if ($sDroplet == 0) {
                  msgQueue::add( 'UNABLE TO UNZIP FILE'.'::'.$oArchive->errorInfo(true) );
            } else {
//                $sSearchFor = 'php';
//                $file_types  = preg_replace( '/\s*[,;\|#]\s*/','|',$sSearchFor );
//        if (!preg_match('/^(to|cc|bcc|Reply-To)$/', $kind)) {
                $aDroplet['name'] = $sDroplet[0]['filename'];
                $aDroplet['content'] = explode("\n",$sDroplet[0]['content']);

//                if ( !preg_match('/'.$file_types.'/si', $aDroplet['name'], $aMatch) ) { 
//                  continue; }
                if( $sTmp = insertDroplet($aDroplet, false)) {
                    $aUnzipDroplets[] = $sTmp; 
                }
            }
        }

        if( $oArchive->error_code != 0 )
        {
            msgQueue::add( sizeof( $aUnzipDroplets ).' '. $Droplet_Import['ARCHIV_IMPORTED']);
        } else {
            if ( sizeof( $aUnzipDroplets ) > 0 ) {
                msgQueue::add( implode(', ',$aUnzipDroplets).'<br />'.sizeof( $aUnzipDroplets ).' '. $Droplet_Import['ARCHIV_IMPORTED'], true);
            } else {
                msgQueue::add( sizeof( $aUnzipDroplets ).' '. $Droplet_Import['ARCHIV_IMPORTED'], true);
            }
        }
    }



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
 * @category        core
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

 // Create new admin object and print admin header
if (!defined('WB_PATH')){require( (dirname(dirname(dirname(__DIR__)))).'/config.php' );}

if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
//$admin = new admin('##skip##', false, false);
$admin = new admin('Pages', 'pages_settings',false);
//if ( !class_exists( 'order', false ) ) { require(WB_PATH.'/framework/class.order.php'); }
$aJsonRespond = array();
$aJsonRespond['message'] = 'Ajax operation failed';
$aJsonRespond['success'] = FALSE;

//if (!$admin->is_authenticated()){exit(json_encode($aJsonRespond));}
if (!$admin->is_authenticated() || !$admin->ami_group_member('1')){exit(json_encode($aJsonRespond));}
// suppress to print the header, so no new FTAN will be set

if ( !function_exists( 'create_access_file' ) ) { require(WB_PATH.'/framework/functions.php'); }
require (WB_PATH.'/modules/SimpleRegister.php');

//$target_url = ADMIN_URL.'/pages/settings.php?page_id='.$page_id;
//$pagetree_url = ADMIN_URL.'/pages/index.php';

    function _makeSql($iParentKey = 0)
    {
        global $oDb, $oReg;
        $iParentKey = intval($iParentKey);
        $sql  = 'SELECT ( SELECT COUNT(*) '
              .          'FROM `'.TABLE_PREFIX.'pages` `x` '
              .          'WHERE x.`parent`=p.`page_id`'
              .        ') `children`, '
              .        's.`module`, MAX(s.`publ_start` + s.`publ_end`) published, p.`link`, '
              .        '(SELECT MAX(`position`) FROM `'.TABLE_PREFIX.'pages` '
              .        'WHERE `parent`='.$iParentKey.') max_position, '
              .        '0 min_position, '
              .        'p.`position`, '
              .        'p.`page_id`, p.`parent`, p.`level`, p.`language`, p.`admin_groups`, '
              .        'p.`admin_users`, p.`viewing_groups`, p.`viewing_users`, p.`visibility`, '
              .        'p.`menu_title`, p.`page_title`, p.`page_trail`, p.`modified_when`, '
              .        'GROUP_CONCAT(CAST(CONCAT(s.`section_id`, \' - \', s.`module`) AS CHAR) ORDER BY s.`position` SEPARATOR \'\n\') `section_list` '
              . 'FROM `'.TABLE_PREFIX.'pages` p '
              .    'INNER JOIN `'.TABLE_PREFIX.'sections` s '
              .    'ON p.`page_id`=s.`page_id` '
              . 'WHERE `parent`='.$iParentKey.' '
              .    (($oReg->PageTrash != 'inline') ? 'AND `visibility`!=\'deleted\' ' : '')
              . 'GROUP BY p.`page_id` '
              . 'ORDER BY p.`position` ASC';
        return $sql;
    }

    function _IterateTree($iParent = 0)
    {
        global $oDb, $oReg, $_queries, $index,$aOutput;
      // Get page list from database
        if(($oPages = $oDb->query(_makeSql($iParent))))
        {
            $_queries++;
            $iMinPosition = 1;
            while($aPage = $oPages->fetchRow(MYSQLI_ASSOC))
            { // iterate through the current branch
                if($oReg->PageLevelLimit && ($aPage['level'] > $oReg->PageLevelLimit)) {
                    break;
                }

                // array for sitemap
                $aOutput[$aPage['page_id']] = array(
                'loc' => $oReg->AppUrl.$oReg->PagesDir.trim($aPage['link'],'/').$oReg->PageExtension,
                'lastmod' => date(DATE_W3C, (int)$aPage['modified_when']),
                'changefreq' => 'monthly',
                'priority' => '0.5'
                );

                // array to create accessfiles
                $aOutput[$aPage['page_id']] = $aPage;
                // could not use oReg, we are needing backslashes to create access files
                $sPageFile = WB_PATH.PAGES_DIRECTORY.'/'.trim($aPage['link'],'/').$oReg->PageExtension;
                if( is_writeable( $sPageFile ) || !file_exists( $sPageFile ) ) {
                    create_access_file( $sPageFile, $aPage['page_id'], $aPage['level']);
                    $index++; //
                }
                if((int)$aPage['children'] > 0 ) {
                    _IterateTree($aPage['page_id']);
                }
          }
        } else {
           $aOutput = $oDb->get_error();
        }
        return $aOutput;
    }
/*-----------------------------------------------------------------------------------*/
      if (!isset($oDb)) {$oDb = $GLOBALS['database'];}
      $_queries  = $index = 0;
      $iTreeRoot = 0;
      $aOutput   = array();
      $aPageTree = array();

      $aPageTree = _IterateTree($iTreeRoot);
#      echo '<h3>Rebuild '.$index.' pages access files</h3>';
$aJsonRespond['message'] = 'Rebuild '.$index.' pages access files';
//unset($aJsonRespond['message']);
// If the script is still running, set success to true
$aJsonRespond['success'] = true;
// and echo the json_respond to the ajax function
exit(json_encode($aJsonRespond));


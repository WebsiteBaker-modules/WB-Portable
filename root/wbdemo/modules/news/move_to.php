<?php
/**
 *
 * @category        modules
 * @package         JsAdmin
 * @author          WebsiteBaker Project, modified by Swen Uth for Website Baker 2.7
 * @copyright       (C) 2006, Stepan Riha
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: move_to.php 1441 2011-04-09 23:04:22Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/move_to.php $
 * @lastmodified    $Date: 2011-04-10 01:04:22 +0200 (So, 10. Apr 2011) $
 *
*/

/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if(defined('WB_PATH') == false) { exit('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

    $aJsonRespond['modules'] = $aRequestVars['module'];
    $aJsonRespond['success'] = true;
    $aJsonRespond['modules_dir'] = '/modules/'.$aRequestVars['module'];

// Get id
    if (isset($aRequestVars['post_id'])){
        $table = TABLE_PREFIX.'mod_news_posts';
        $id = (int)$aRequestVars['move_id'];
        $id_field = 'post_id';
        $common_field = 'section_id';
        $sFieldOrderName = 'position';
        $aJsonRespond['message'] = 'Activity position '.$id.' successfully changed';
    } else
    if (isset($aRequestVars['group_id'])){
        $table = TABLE_PREFIX.'mod_news_groups';
        $id = (int)$aRequestVars['move_id'];
        $id_field = 'group_id';
        $common_field = 'section_id';
        $sFieldOrderName = 'position';
        $aJsonRespond['message'] = 'Activity position '.$id.' successfully changed';
    } else {
      $aJsonRespond['message'] = 'ajax operation failed';
      $aJsonRespond['success'] = false;
      exit (json_encode($aJsonRespond));
    }



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
    $table = TABLE_PREFIX.'mod_form_fields';
    $id = (int)$aRequestVars['move_id'];
    $id_field = 'field_id';
    $common_field = 'section_id';
    $sFieldOrderName = 'position';
    $aJsonRespond['message'] = 'Activity position '.$id.' successfully changed';
//    $group = (int)$aRequestVars['section_id'];


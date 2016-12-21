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
// initialize json_respond array  (will be sent back)
$aJsonRespond = array();
$aJsonRespond['message'] = 'Ajax operation failed';
$aJsonRespond['success'] = FALSE;
//if (!$admin->is_authenticated()){exit(json_encode($aJsonRespond));}
if (!$admin->is_authenticated() || !$admin->ami_group_member('1')){exit(json_encode($aJsonRespond));}

if (is_writable(WB_PATH.'/temp/cache')) {
    Translate::getInstance()->clearCache();
}
/*
unset($aJsonRespond['message']);
*/
$aJsonRespond['message'] = 'Translate Cache was cleared';
// If the script is still running, set success to true
$aJsonRespond['success'] = true;
// and echo the json_respond to the ajax function
exit(json_encode($aJsonRespond));

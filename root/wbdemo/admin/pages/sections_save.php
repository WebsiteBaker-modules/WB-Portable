<?php
/**
 *
 * @category        admin
 * @package         pages
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: sections_save.php 1473 2011-07-09 00:40:50Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/admin/pages/sections_save.php $
 * @lastmodified    $Date: 2011-07-09 02:40:50 +0200 (Sa, 09. Jul 2011) $
 *
 */

// Include config file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }

$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$aRequestVars = (isset(${$requestMethod})) ? ${$requestMethod} : null;
// Make sure people are allowed to access this page
if(MANAGE_SECTIONS != 'enabled') {
    header('Location: '.ADMIN_URL.'/pages/index.php');
    exit(0);
}

require_once(WB_PATH."/include/jscalendar/jscalendar-functions.php");
/**/
// Create new admin object
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// suppress to print the header, so no new FTAN will be set
$admin = new admin('Pages', 'pages_modify',false);

// Get page id
if(!isset($aRequestVars['page_id']) || !is_numeric($aRequestVars['page_id'])) {
    $sInfo = __LINE__.') '.strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION)).'::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$MESSAGE['PAGES_INSUFFICIENT_PERMISSIONS']);
    exit(0);
} else {
    $iPageId = $page_id = (int)$aRequestVars['page_id'];
}

$callingScript = $_SERVER['HTTP_REFERER'];
$sBackLink = $callingScript.'?page_id='.$iPageId;
//$sBackLink = ADMIN_URL.'/pages/sections.php?page_id='.$iPageId;

if (!$admin->checkFTAN())
{
    $admin->print_header();
    $sInfo = __LINE__.') '.strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION)).'::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL);
}
/*
if( (!($page_id = $admin->checkIDKEY('page_id', 0, $_SERVER['REQUEST_METHOD']))) )
{
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS']);
    exit();
}
*/
// Get perms
$sql  = 'SELECT `admin_groups`,`admin_users` FROM `'.TABLE_PREFIX.'pages` '
      . ' WHERE `page_id` = '.(int)$page_id. '';
$results = $database->query($sql);
$results_array = $results->fetchRow(MYSQLI_ASSOC);
$old_admin_groups = explode(',', $results_array['admin_groups']);
$old_admin_users = explode(',', $results_array['admin_users']);
$in_old_group = FALSE;
foreach($admin->get_groups_id() as $cur_gid){
    if (in_array($cur_gid, $old_admin_groups)) {
        $in_old_group = TRUE;
    }
}
if((!$in_old_group) && !is_numeric(array_search($admin->get_user_id(), $old_admin_users))) {
    $sInfo = __LINE__.') '.(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION)).'::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$MESSAGE['PAGES_INSUFFICIENT_PERMISSIONS']);
}

// Get page details
$query = 'SELECT COUNT(`page_id`) `numRows` FROM `'.TABLE_PREFIX.'pages` WHERE `page_id` = '.(int)$page_id.'';
$numRows = $database->get_one($query);
if($database->is_error()) {
    $admin->print_header();
    $sInfo = __LINE__.') '.strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION)).'_DATABASE_ERROR::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$database->get_error());
}
if($numRows == 0) {
    $admin->print_header();
    $sInfo = __LINE__.') '.strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION)).'::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$MESSAGE['PAGES_NOT_FOUND']);
}
// After check print the header
$admin->print_header();

$results_array = $results->fetchRow(MYSQLI_ASSOC);
// Set module permissions
$module_permissions = $_SESSION['MODULE_PERMISSIONS'];

$aSql = array();
$section_id = intval($admin->get_post('section_id') );
$sTitle  = $admin->StripCodeFromText( $admin->get_post('title_'.$section_id ) );
$bSaveTitle = isset( $aRequestVars['inputSection'] );
if( $bSaveTitle ) {
  $aSql[]  = 'UPDATE `'.TABLE_PREFIX.'sections` SET '
         . '`title`=\''.$database->escapeString($sTitle).'\' '
         . 'WHERE `section_id`='.(int)$section_id;
  foreach( $aSql as $sSql ) {
      if(!$database->query($sSql)) {
      }
 }

} else {
// Loop through sections
    $sql  = 'SELECT `section_id`,`module`,`position` FROM `'.TABLE_PREFIX.'sections` '
          . 'WHERE `page_id` = '.(int)$page_id.' '
          . 'ORDER BY `position` ';
    if($query_sections = $database->query($sql))
    {
        $num_sections = $query_sections->numRows();
        while($section = $query_sections->fetchRow(MYSQLI_ASSOC)) {
            if(!is_numeric(array_search($section['module'], $module_permissions))) {
                // Update the section record with properties
                $section_id = $section['section_id'];
                $sql = ''; $publ_start = 0; $publ_end = 0;
                $dst = date("I")?" DST":""; // daylight saving time?
                if(isset($_POST['block'.$section_id]) && $_POST['block'.$section_id] != '') {
                    $sql = "block = '".$admin->add_slashes($_POST['block'.$section_id])."'";
                }
                // update publ_start and publ_end, trying to make use of the strtotime()-features like "next week", "+1 month", ...
                if(isset($_POST['start_date'.$section_id]) && isset($_POST['end_date'.$section_id])) {
                    if(trim($_POST['start_date'.$section_id]) == '0' || trim($_POST['start_date'.$section_id]) == '') {
                        $publ_start = 0;
                    } else {
                        $publ_start = jscalendar_to_timestamp($_POST['start_date'.$section_id]);
                    }
                    if(trim($_POST['end_date'.$section_id]) == '0' || trim($_POST['end_date'.$section_id]) == '') {
                        $publ_end = 0;
                    } else {
                        $publ_end = jscalendar_to_timestamp($_POST['end_date'.$section_id], $publ_start);
                    }
                    if($sql != ''){$sql .= ",";}
                    $sql .= " publ_start = '".$database->escapeString($publ_start)."'";
                    $sql .= ", publ_end = '".$database->escapeString($publ_end)."'";
                }

                $query = "UPDATE ".TABLE_PREFIX."sections SET $sql WHERE section_id = '$section_id'";
                if($sql != '') {
                    $database->query($query);
                }
            }
        }
    }
  }
// Check for error or print success message
if($database->is_error()) {
    $sInfo = __LINE__.') '.strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION)).'::';
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$database->get_error(), ADMIN_URL.'/pages/sections.php?page_id='.$page_id );
} else {
    $admin->print_success($MESSAGE['PAGES_SECTIONS_PROPERTIES_SAVED'], $sBackLink );
}

// Print admin footer
$admin->print_footer();

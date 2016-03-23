<?php
/**
 *
 * @category        backend
 * @package         wysiwyg
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save.php 1615 2012-02-18 02:14:31Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/wysiwyg/save.php $
 * @lastmodified    $Date: 2012-02-18 03:14:31 +0100 (Sa, 18. Feb 2012) $
 *
*/

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }

// suppress to print the header, so no new FTAN will be set
$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

if (!$admin->checkFTAN()) {
    $admin->print_header();
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}
// After check print the header
$admin->print_header();

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

$bBackLink = isset($_POST['pagetree']);
// Update the mod_wysiwygs table with the contents
if(isset($_POST['content'.$section_id])) {
    $content = $_POST['content'.$section_id];
    if(ini_get('magic_quotes_gpc')==true)
    {
        $content = $admin->strip_slashes($_POST['content'.$section_id]);
    }
/*
    $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
    $searchfor = '@(<[^>]*=\s*")('.preg_quote($sMediaUrl).')([^">]*".*>)@siU';
    $content = preg_replace($searchfor, '$1{SYSVAR:MEDIA_REL}$3', $content);
*/
    $sRelUrl = preg_replace('/^https?:\/\/[^\/]+(.*)/is', '\1', WB_URL);
    $sDocumentRootUrl = str_replace($sRelUrl, '', WB_URL);
    $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
    $aPatterns = array(
        '/(<[^>]*?=\s*\")(\/+)([^\"]*?\"[^>]*?)/is',
        '/(<[^>]*=\s*")('.preg_quote($sMediaUrl, '/').')([^">]*".*>)/siU'
    );
    $aReplacements = array(
        '\1'.$sDocumentRootUrl.'/\3',
        '$1{SYSVAR:MEDIA_REL}$3'
    );
    $content = preg_replace($aPatterns, $aReplacements, $content);

    $text = strip_tags($content);
    $sql = 'UPDATE `'.TABLE_PREFIX.'mod_wysiwyg` SET '
         . '`content`=\''.$database->escapeString($content).'\', '
         . '`text`=\''.$database->escapeString($text).'\' '
         . 'WHERE `section_id`='.(int)$section_id;
    $database->query($sql);
}
$sec_anchor = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? '#'.SEC_ANCHOR.$section['section_id'] : '' );
if(defined('EDIT_ONE_SECTION') && EDIT_ONE_SECTION){
    $edit_page = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&wysiwyg='.$section_id;
} elseif ( $bBackLink ) {
  $edit_page = ADMIN_URL.'/pages/index.php';
} else {
    $edit_page = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.$sec_anchor;
}

// Check if there is a database error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(), $js_back);
} else {
    $admin->print_success($MESSAGE['PAGES_SAVED'], $edit_page );
}

// Print admin footer
$admin->print_footer();

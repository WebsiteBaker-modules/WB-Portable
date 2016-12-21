<?php
/**
 *
 * @category        modules
 * @package         code
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save.php 1425 2011-02-03 23:16:12Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/code/save.php $
 * @lastmodified    $Date: 2011-02-04 00:16:12 +0100 (Fr, 04. Feb 2011) $
 *
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
$aErrorMessage = array();
// suppress to print the header, so no new FTAN will be set
$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$bBackAfterSave = intval(isset($aRequestVars['pagetree']));
$OverviewUrl = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;
if ( !$admin->checkFTAN())
{
    $admin->print_header();
    $sInfo = strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION).'::');
    $sDEBUG=(@DEBUG?$sInfo:'');
    $admin->print_error($sDEBUG.$MESSAGE['GENERIC_SECURITY_ACCESS'], $OverviewUrl);
}
$admin->print_header();
// Update the mod_wysiwygs table with the contents
if(isset($_POST['content'])) {
    $notAllowedTags = array('<?php', '?>' , '<?', '<?=');
    $content = (str_replace($notAllowedTags, '', $_POST['content']));

    $sqlSet = '`'.TABLE_PREFIX.'mod_code` SET '
            . '`section_id`='.$section_id.', '
            . '`page_id`='.$page_id.', '
            . '`content` = \''.$database->escapeString($content).'\' ';
    // search for instance of this module in section
    $sql = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_code` '
         . 'WHERE `section_id`='.$section_id;
    if ($database->get_one($sql)) {
    // if matching record already exists run UPDATE
        $sql  = 'UPDATE '.$sqlSet
              .'WHERE `section_id`='.$section_id;
    } else {
    // if no matching record exists INSERT new record
        $sql = 'INSERT INTO '.$sqlSet;
    }
    if (!$database->query($sql)){
        $aErrorMessage[] = ($database->is_error()?$database->get_error():'');
    }
}
// Check if there is a database error, otherwise say successful
if (sizeof($aErrorMessage)) {
    $admin->print_error(implode('<br />', $aErrorMessage), $OverviewUrl);
} else {
    $sIndexUrl = ADMIN_URL.'/pages/index.php';
    $OverviewUrl = (@$bBackAfterSave ? $sIndexUrl : $OverviewUrl);
    $admin->print_success($MESSAGE['PAGES_SAVED'], $OverviewUrl);
}

// Print admin footer
$admin->print_footer();

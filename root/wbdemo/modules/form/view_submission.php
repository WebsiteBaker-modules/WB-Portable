<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: view_submission.php 1919 2013-06-07 04:21:49Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/view_submission.php $
 * @lastmodified    $Date: 2013-06-07 06:21:49 +0200 (Fr, 07. Jun 2013) $
 * @description
 */

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
//if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
// load module language file
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}
/* */

include_once (WB_PATH.'/framework/functions.php');

// Get page
$requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
$page = intval(isset(${$requestMethod}['page'])) ? ${$requestMethod}['page'] : 1;

// Get id
$submission_id = intval($admin->checkIDKEY('submission_id', false, 'GET'));
if (!$submission_id) {
 $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page='.$page.'&amp;page_id='.$page_id.'#submissions');
}

// Get submission details
$sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_form_submissions` '
      . 'WHERE `submission_id` = '.$submission_id.' ';
if($query_content = $database->query($sql)) {

    $submission = $query_content->fetchRow(MYSQLI_ASSOC);
}

//print '<pre style="text-align: left;"><strong>function '.__FUNCTION__.'( '.''.' );</strong>  basename: '.basename(__FILE__).'  line: '.__LINE__.' -> <br />';
//print_r( $page ); print '</pre>';

// Get the user details of whoever did this submission
$sql  = 'SELECT `username`,`display_name` FROM `'.TABLE_PREFIX.'users` '
      . 'WHERE `user_id` = '.$submission['submitted_by'];
if($get_user = $database->query($sql)) {
    if($get_user->numRows() != 0) {
        $user = $get_user->fetchRow(MYSQLI_ASSOC);
    } else {
        $user['display_name'] = $TEXT['GUEST'];
        $user['username'] = $TEXT['UNKNOWN'];
    }
}
//$sSectionIdPrefix = ( defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? '#'.SEC_ANCHOR : 'Sec' );
$sSectionIdPrefix = 'submissions';
?>
<table class="frm-submission" summary="" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td><?php echo $TEXT['SUBMISSION_ID']; ?>:</td>
    <td><?php echo $submission['submission_id']; ?></td>
</tr>
<tr>
    <td><?php echo $TEXT['SUBMITTED']; ?>:</td>
    <td><?php echo gmdate(DATE_FORMAT .', '.TIME_FORMAT, $submission['submitted_when']+TIMEZONE); ?></td>
</tr>
<tr>
    <td><?php echo $TEXT['USER'].' '; ?>:</td>
    <td><?php echo $user['display_name'].' '; ?></td>
</tr>
<tr>
    <td colspan="2">
        <hr />
    </td>
</tr>
<tr>
    <td colspan="2">
        <?php echo nl2br($submission['body']); ?>
    </td>
</tr>
</table>

<br />

<input type="button" value="<?php echo $TEXT['CLOSE']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page=<?php echo $page?>&amp;page_id=<?php echo $page_id.'#'.$sSectionIdPrefix; ?>';" style="width: 150px; margin-top: 5px;" />
<input type="button" value="<?php echo $TEXT['DELETE']; ?>" onclick="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/form/delete_submission.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&submission_id=<?php echo $admin->getIDKEY($submission_id).'#'.$sSectionIdPrefix; ?>');" style="width: 150px; margin-top: 5px;" />
<?php

// Print admin footer
$admin->print_footer();

<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: add.php 1573 2012-01-16 02:01:52Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/form/add.php $
 * @lastmodified    $Date: 2012-01-16 03:01:52 +0100 (Mo, 16. Jan 2012) $
 * @description
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die("Cannot access this file directly"); }
/* -------------------------------------------------------- */

// load module language file
$lang = (dirname(__FILE__)) . '/languages/' . LANGUAGE . '.php';
require_once(!file_exists($lang) ? (dirname(__FILE__)) . '/languages/EN.php' : $lang );

// Insert an extra rows into the database
$header = '<table class="frm-field_table" >';
$field_loop = '<tr>'.PHP_EOL.'<td class=\"frm-field_title\">{TITLE}{REQUIRED}:</td>'.PHP_EOL.'<td>{FIELD}</td>'.PHP_EOL.'</tr>';
$footer = '<tr>'.PHP_EOL.'<td>&nbsp;</td>'.PHP_EOL.'
<td>'.PHP_EOL.'
<input type=\"submit\" name=\"submit\" value=\"{SUBMIT_FORM}\" />'.PHP_EOL.'
</td>'.PHP_EOL.'
</tr>'.PHP_EOL.'
</table>'.PHP_EOL;
$email_to = '';
$email_from = '';
$email_fromname = '';
$email_subject = '';
$success_page = 'none';
$success_email_to = '';
$success_email_from = '';
$success_email_fromname = '';
$success_email_text = '';
// $success_email_text = addslashes($success_email_text);
$success_email_subject = '';
$max_submissions = 50;
$stored_submissions = 50;
$use_captcha = true;

// $database->query("INSERT INTO ".TABLE_PREFIX."mod_form_settings (page_id,section_id,header,field_loop,footer,email_to,email_from,email_fromname,email_subject,success_page,success_email_to,success_email_from,success_email_fromname,success_email_text,success_email_subject,max_submissions,stored_submissions,use_captcha) VALUES ('$page_id','$section_id','$header','$field_loop','$footer','$email_to','$email_from','$email_fromname','$email_subject','$success_page','$success_email_to','$success_email_from','$success_email_fromname','$success_email_text','$success_email_subject','$max_submissions','$stored_submissions','$use_captcha')");

// Insert settings
$sql  = 'INSERT INTO  `'.TABLE_PREFIX.'mod_form_settings` SET '
      . '`section_id` = \''.$section_id.'\', '
      . '`page_id` = \''.$page_id.'\', '
      . '`header` = \''.$header.'\', '
      . '`field_loop` = \''.$field_loop.'\', '
      . '`footer` = \''.$footer.'\', '
      . '`email_to` = \''.$email_to.'\', '
      . '`email_from` = \''.$email_from.'\', '
      . '`email_fromname` = \''.$email_fromname.'\', '
      . '`email_subject` = \''.$email_subject.'\', '
      . '`success_page` = \''.$success_page.'\', '
      . '`success_email_to` = \''.$success_email_to.'\', '
      . '`success_email_from` = \''.$success_email_from.'\', '
      . '`success_email_fromname` = \''.$success_email_fromname.'\', '
      . '`success_email_text` = \''.$success_email_text.'\', '
      . '`success_email_subject` = \''.$success_email_subject.'\', '
      . '`max_submissions` = \''.$max_submissions.'\', '
      . '`stored_submissions` = \''.$stored_submissions.'\', '
      . '`use_captcha` = \''.$use_captcha.'\' '
      . '';
if($database->query($sql)) {
    // $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id.$sec_anchor);
}

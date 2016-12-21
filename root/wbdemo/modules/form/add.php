<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://wwebsitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: add.php 1919 2013-06-07 04:21:49Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/add.php $
 * @lastmodified    $Date: 2013-06-07 06:21:49 +0200 (Fr, 07. Jun 2013) $
 * @description
 */
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
    $table_name = TABLE_PREFIX.'mod_form_settings';
    $field_name = 'perpage_submissions';
    $description = "INT NOT NULL DEFAULT '10' AFTER `max_submissions`";
    if(!$database->field_exists($table_name,$field_name)) {
        $database->field_add($table_name, $field_name, $description);
    }

// Insert an extra rows into the database
    $header     = '<table class="frm-field_table">'.PHP_EOL
                . '    <tbody>'.PHP_EOL;
    $field_loop = '        <tr>'.PHP_EOL
                . '            <td class="frm-field_title">{TITLE}{REQUIRED}:</td>'.PHP_EOL
                . '            <td>{FIELD}</td>'.PHP_EOL
                . '        </tr>';
    $footer     = '        <tr>'.PHP_EOL
                . '            <td>&#32;</td>'.PHP_EOL
                . '            <td>'.PHP_EOL
                . '                <input type="submit" name="submit" value="{SUBMIT_FORM}" />'.PHP_EOL
                . '            </td>'.PHP_EOL
                . '        </tr>'.PHP_EOL
                . '    </tbody>'.PHP_EOL
                . '</table>'.PHP_EOL;

    $email_to = '';
    $email_from = '';
    $email_fromname = '';
    $email_subject = '';
    $success_page = 0;
    $success_email_to = '';
    $success_email_from = '';
    $success_email_fromname = '';
    $success_email_text = '';
    // $success_email_text = addslashes($success_email_text);
    $success_email_subject = '';
    $max_submissions = 50;
    $stored_submissions = 50;
    $perpage_submissions = 10;
    $use_captcha = true;

    // Insert settings
    $sql  = 'INSERT INTO  `'.TABLE_PREFIX.'mod_form_settings` SET '
          . '`section_id` = \''.$database->escapeString($section_id).'\', '
          . '`page_id` = \''.$database->escapeString($page_id).'\', '
          . '`header` = \''.$database->escapeString($header).'\', '
          . '`field_loop` = \''.$database->escapeString($field_loop).'\', '
          . '`footer` = \''.$database->escapeString($footer).'\', '
          . '`email_to` = \''.$database->escapeString($email_to).'\', '
          . '`email_from` = \''.$database->escapeString($email_from).'\', '
          . '`email_fromname` = \''.$database->escapeString($email_fromname).'\', '
          . '`email_subject` = \''.$database->escapeString($email_subject).'\', '
          . '`success_page` = \''.$database->escapeString($success_page).'\', '
          . '`success_email_to` = \''.$database->escapeString($success_email_to).'\', '
          . '`success_email_from` = \''.$database->escapeString($success_email_from).'\', '
          . '`success_email_fromname` = \''.$database->escapeString($success_email_fromname).'\', '
          . '`success_email_text` = \''.$database->escapeString($success_email_text).'\', '
          . '`success_email_subject` = \''.$database->escapeString($success_email_subject).'\', '
          . '`max_submissions` = \''.$database->escapeString($max_submissions).'\', '
          . '`stored_submissions` = \''.$database->escapeString($stored_submissions).'\', '
          . '`perpage_submissions` = \''.$database->escapeString($perpage_submissions).'\', '
          . '`use_captcha` = \''.$database->escapeString($use_captcha).'\' ';
   if($database->query($sql)) {

    }
}

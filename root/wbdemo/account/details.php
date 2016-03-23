<?php
/**
 *
 * @category        frontend
 * @package         account
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: details.php 1599 2012-02-06 15:59:24Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/account/details.php $
 * @lastmodified    $Date: 2012-02-06 16:59:24 +0100 (Mo, 06. Feb 2012) $
 *
 */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { die("Cannot access this file directly"); }
// sanitize entered values
    $display_name = strip_tags( $wb->StripCodeFromText($wb->get_post('display_name')));
    $display_name = ( $display_name == '' ? $wb->get_display_name() : $display_name );
    $language = preg_match('/^[a-z]{2}$/si', $wb->get_post('language'))
                ? $wb->get_post('language') : 'EN';
    $timezone = is_numeric($wb->get_post('timezone')) ? $wb->get_post('timezone')*3600 : 0;
    $date_format = $wb->get_post('date_format');
    $time_format = $wb->get_post('time_format');
    // check that display_name is unique in whoole system (prevents from User-faking)
    $sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'users` ';
    $sql .= 'WHERE `user_id` <> '.(int)$wb->get_user_id().' AND `display_name` LIKE \''.$display_name.'\'';
    if ($database->get_one($sql) > 0) {
        $error[] = $MESSAGE['MEDIA_BLANK_NAME'].' ('.$TEXT['DISPLAY_NAME'].')';
    } else {
// Update the database
    $sql  = 'UPDATE `'.TABLE_PREFIX.'users` SET '
          .     '`display_name` = \''.$database->escapeString($display_name).'\', '
          .     '`language` = \''.$database->escapeString($language).'\', '
          .     '`timezone` = \''.$database->escapeString($timezone).'\', '
          .     '`date_format` = \''.$database->escapeString($date_format).'\', '
          .     '`time_format` = \''.$database->escapeString($time_format).'\' '
          . 'WHERE `user_id` = \''.$wb->get_user_id().'\'';
          $database->query($sql);
          if($database->is_error()) {
              $error[] = $database->get_error();
          } else {
              $success[] = $MOD_PREFERENCE['DETAILS_SAVED'];
              $_SESSION['DISPLAY_NAME'] = $display_name;
              $_SESSION['LANGUAGE'] = $language;
              $_SESSION['TIMEZONE'] = $timezone;
          // Update date format
              if($date_format != '') {
                  $_SESSION['DATE_FORMAT'] = $date_format;
                  if(isset($_SESSION['USE_DEFAULT_DATE_FORMAT'])) { unset($_SESSION['USE_DEFAULT_DATE_FORMAT']); }
              } else {
                  $_SESSION['USE_DEFAULT_DATE_FORMAT'] = true;
                  if(isset($_SESSION['DATE_FORMAT'])) { unset($_SESSION['DATE_FORMAT']); }
              }

        // Update time format
            if($time_format != '') {
                $_SESSION['TIME_FORMAT'] = $time_format;
                if(isset($_SESSION['USE_DEFAULT_TIME_FORMAT'])) { unset($_SESSION['USE_DEFAULT_TIME_FORMAT']); }
            } else {
                $_SESSION['USE_DEFAULT_TIME_FORMAT'] = true;
                if(isset($_SESSION['TIME_FORMAT'])) { unset($_SESSION['TIME_FORMAT']); }
            }
        }
    }

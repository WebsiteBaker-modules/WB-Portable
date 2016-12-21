<?php
/**
 *
 * @category        admin
 * @package         users
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: users.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/users/users.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

 // Include config file and admin class file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }

$action = 'cancel';
// Set parameter 'action' as alternative to javascript mechanism
$action = (isset($_POST['modify']) ? 'modify' : $action );
$action = (isset($_POST['delete']) ? 'delete' : $action );

$oTrans = Translate::getInstance();
$oTrans->enableAddon('admin\\users');
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $oTrans ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/

switch ($action):
    case 'modify' :
            // Print header
            $admin = new admin('Access', 'users_modify');
            $user_id = intval($admin->checkIDKEY('user_id', 0, $_SERVER['REQUEST_METHOD']));
            // Check if user id is a valid number and doesnt equal 1
            if($user_id == 0){
            $admin->print_error($oTrans->MESSAGE_GENERIC_FORGOT_OPTIONS );
            }
            if( ($user_id < 2 ) )
            {
                // if($admin_header) { $admin->print_header(); }
                $admin->print_error($oTrans->MESSAGE_GENERIC_SECURITY_ACCESS, ADMIN_URL );
            }
            // Get existing values

            $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'users` ' ;
            $sql .= 'WHERE user_id != 1 ';
            $sql .=   'AND user_id = '.$user_id.' ';

            $results = $database->query($sql);

            $user = $results->fetchRow(MYSQLI_ASSOC);
            // Setup template object, parse vars to it, then parse it
            // Create new template object
            $template = new Template(dirname($admin->correct_theme_source('users_form.htt')), 'remove');
            // $template->debug = true;
            $template->set_file('page', 'users_form.htt');
            $template->set_block('page', 'main_block', 'main');
            $template->set_block('main_block', 'user_add_block', 'user_add');

            $template->set_var(array(
                                'ACTION_URL' => ADMIN_URL.'/users/save.php',
                                'SUBMIT_TITLE' => $oTrans->TEXT_SAVE,
                                'USER_ID' => $user['user_id'],
                                'USERNAME' => $user['username'],
                                'DISPLAY_NAME' => $user['display_name'],
                                'EMAIL' => $user['email'],
                                'ADMIN_URL' => ADMIN_URL,
                                'WB_URL' => WB_URL,
                                'THEME_URL' => THEME_URL
                                )
                        );

            $template->set_var('FTAN', $admin->getFTAN());
            $template->set_var('READONLY', 'readonly="readonly"' );
            if($user['active'] == 1) {
                $template->set_var('ACTIVE_CHECKED', ' checked="checked"');
            } else {
                $template->set_var('DISABLED_CHECKED', ' checked="checked"');
            }
            // Add groups to list
            $template->set_block('main_block', 'group_list_block', 'group_list');
            $sql  = 'SELECT `group_id`, `name` FROM `'.TABLE_PREFIX.'groups` '
                  . 'WHERE `group_id` != 1 '
                  . 'ORDER BY `name`';
            $results = $database->query($sql);
            if ($database->query($sql))
            {
                $template->set_var('ID', '');
                $template->set_var('NAME', $oTrans->TEXT_PLEASE_SELECT.'...');
                $template->set_var('SELECTED', '');
                $template->parse('group_list', 'group_list_block', true);
                while($group = $results->fetchRow(MYSQLI_ASSOC))
                {
                    $template->set_var('ID', $group['group_id']);
                    $template->set_var('NAME', $group['name']);
                    $template->set_var('SELECTED', '');
                    if ($admin->is_group_match($group['group_id'], $user['groups_id']))
                    {
                        $template->set_var('SELECTED', ' selected="selected"');
                    }
                    $template->parse('group_list', 'group_list_block', true);
                }
            }

            // Only allow the user to add a user to the Administrators group if they belong to it
            if ($admin->ami_group_member('1'))
            {
                $template->set_var('ID', '1');
                $users_groups = $admin->get_groups_name();
                $template->set_var('NAME', $users_groups[1]);
                if ($admin->is_group_match('1', $user['groups_id']))
                {
                    $template->set_var('SELECTED', ' selected="selected"');
                } else {
                    $template->set_var('SELECTED', '');
                }
                $template->parse('group_list', 'group_list_block', true);
            } else {
                if($results->numRows() == 0)
                {
                    $template->set_var('ID', '');
                    $template->set_var('NAME', $oTrans->TEXT_NONE_FOUND);
                    $template->set_var('SELECTED', ' selected="selected"');
                    $template->parse('group_list', 'group_list_block', true);
                }
            }

            // Generate username field name
            $username_fieldname = 'username_';
            $salt = "abchefghjkmnpqrstuvwxyz0123456789";
            srand((double)microtime()*1000000);
            $i = 0;
            while ($i <= 7) {
                $num = rand() % 33;
                $tmp = substr($salt, $num, 1);
                $username_fieldname = $username_fieldname . $tmp;
                $i++;
            }

            // Work-out if home folder should be shown
            if(!HOME_FOLDERS) {
                $template->set_var('DISPLAY_HOME_FOLDERS', 'display:none;');
            }

            // Include the WB functions file
            require_once(WB_PATH.'/framework/functions.php');

            // Add media folders to home folder list
            $template->set_block('main_block', 'folder_list_block', 'folder_list');
            foreach(directory_list(WB_PATH.MEDIA_DIRECTORY) AS $name)
            {
                $template->set_var('NAME', str_replace(WB_PATH, '', $name));
                $template->set_var('FOLDER', str_replace(WB_PATH.MEDIA_DIRECTORY, '', $name));
                if($user['home_folder'] == str_replace(WB_PATH.MEDIA_DIRECTORY, '', $name)) {
                    $template->set_var('SELECTED', ' selected="selected"');
                } else {
                    $template->set_var('SELECTED', ' ');
                }
                $template->parse('folder_list', 'folder_list_block', true);
            }

            // Insert language text and messages
            $template->set_var(array(
                                'TEXT_RESET' => $oTrans->TEXT_RESET,
                                'TEXT_CANCEL' => $oTrans->TEXT_CANCEL,
                                'TEXT_ACTIVE' => $oTrans->TEXT_ACTIVE,
                                'TEXT_DISABLED' => $oTrans->TEXT_DISABLED,
                                'TEXT_PLEASE_SELECT' => $oTrans->TEXT_PLEASE_SELECT,
                                'TEXT_USERNAME' => $oTrans->TEXT_USERNAME,
                                'TEXT_PASSWORD' => $oTrans->TEXT_PASSWORD,
                                'TEXT_RETYPE_PASSWORD' => $oTrans->TEXT_RETYPE_PASSWORD,
                                'TEXT_DISPLAY_NAME' => $oTrans->TEXT_DISPLAY_NAME,
                                'TEXT_EMAIL' => $oTrans->TEXT_EMAIL,
                                'TEXT_GROUP' => $oTrans->TEXT_GROUP,
                                'TEXT_NONE' => $oTrans->TEXT_NONE,
                                'TEXT_HOME_FOLDER' => $oTrans->TEXT_HOME_FOLDER,
                                'USERNAME_FIELDNAME' => $username_fieldname,
                                'CHANGING_PASSWORD' => $oTrans->MESSAGE_USERS_CHANGING_PASSWORD,
                                'HEADING_MODIFY_USER' => $oTrans->HEADING_MODIFY_USER,
                                'CANCEL_LINK' => ADMIN_URL.'/users/index.php',
                                )
                        );

            $template->set_block( 'user_add_block', '');
            // Parse template object
            $template->parse('main', 'main_block', false);
            $template->pparse('output', 'page');
            // Print admin footer
            $admin->print_footer();
            break;
        case 'delete' :
            // Print header
            $admin = new admin('Access', 'users_delete');
            $user_id = intval($admin->checkIDKEY('user_id', 0, $_SERVER['REQUEST_METHOD']));
            // Check if user id is a valid number and doesnt equal 1
            if($user_id == 0){
            $admin->print_error($oTrans->MESSAGE_GENERIC_FORGOT_OPTIONS );
            }
            if( ($user_id < 2 ) )
            {
                // if($admin_header) { $admin->print_header(); }
                $admin->print_error($oTrans->MESSAGE_GENERIC_SECURITY_ACCESS, ADMIN_URL  );
            }
            $sql  = 'SELECT `active` FROM `'.TABLE_PREFIX.'users` ';
            $sql .= 'WHERE `user_id` = '.$user_id.'';
            if( ($iDeleteUser = $database->get_one($sql)) == 1 ) {
                $sMessage = $oTrans->MESSAGE_USERS_DEACTIVATED;
                // Delete the user
                $database->query("UPDATE `".TABLE_PREFIX."users` SET `active` = 0 WHERE `user_id` = '".$user_id."' ");
            } else {
                $sMessage = $oTrans->MESSAGE_USERS_DELETED;
                $database->query("DELETE FROM `".TABLE_PREFIX."users` WHERE `user_id` = ".$user_id);
            }

            if($database->is_error()) {
                $admin->print_error($database->get_error());
            } else {
                $admin->print_success($sMessage);
            }
            // Print admin footer
            $admin->print_footer();
            break;
    default:
            break;
endswitch;

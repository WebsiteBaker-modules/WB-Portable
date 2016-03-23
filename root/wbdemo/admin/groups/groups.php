<?php
/**
 *
 * @category        admin
 * @package         groups
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: groups.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/groups/groups.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

// Include config file and admin class file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// Set parameter 'action' as alternative to javascript mechanism
$action = 'cancel';
// Set parameter 'action' as alternative to javascript mechanism
$action = (isset($_POST['action']) && ($_POST['action'] ='modify') ? 'modify' : $action );
$action = (isset($_POST['modify']) ? 'modify' : $action );
$action = (isset($_POST['delete']) ? 'delete' : $action );

switch ($action):

    case 'modify' :
            // Create new admin object
            $admin = new admin('Access', 'groups_modify' );
            // Check if group group_id is a valid number and doesnt equal 1
            $group_id = intval($admin->checkIDKEY('group_id', 0, $_SERVER['REQUEST_METHOD']));
            if($group_id == 0){
                $admin->print_error($MESSAGE['USERS_NO_GROUP'] );
            }
            if( ($group_id < 2 ) )
            {
                // if($admin_header) { $admin->print_header(); }
                $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
            }

            // Get existing values
            $sql = 'SELECT * FROM  `'.TABLE_PREFIX.'groups` WHERE `group_id` =  '.$group_id;
            $results = $database->query($sql);
            $group = $results->fetchRow(MYSQLI_ASSOC);
            // Setup template object, parse vars to it, then parse it
            // Create new template object
            $template = new Template(dirname($admin->correct_theme_source('groups_form.htt')));
            // $template->debug = true;
            $template->set_file('page', 'groups_form.htt');
            $template->set_block('page', 'main_block', 'main');
            $template->set_var(    array(
                                'ADMIN_URL' => ADMIN_URL,
                                'WB_URL' => WB_URL,
                                'THEME_URL' => THEME_URL,
                                'ACTION_URL' => ADMIN_URL.'/groups/save.php',
                                'SUBMIT_TITLE' => $TEXT['SAVE'],
                                'GROUP_ID' => $admin->getIDKEY($group['group_id']),
                                'GROUP_NAME' => $group['name'],
                                'ADVANCED_LINK' => ADMIN_URL.'/groups/groups.php',
                                'CANCEL_LINK' => ADMIN_URL.'/groups/index.php',
                                'FTAN' => $admin->getFTAN(),
                                ));
            // Tell the browser whether or not to show advanced options
            if( true == (isset( $_POST['advanced']) AND ( strpos( $_POST['advanced'], ">>") > 0 ) ) ) {
                $template->set_var('DISPLAY_ADVANCED', '');
                $template->set_var('DISPLAY_BASIC', 'display:none;');
                $template->set_var('ADVANCED', 'yes');
                $template->set_var('ADVANCED_BUTTON', '&lt;&lt; '.$TEXT['HIDE_ADVANCED']);
            } else {
                $template->set_var('DISPLAY_ADVANCED', 'display:none;');
                $template->set_var('DISPLAY_BASIC', '');
                $template->set_var('ADVANCED', 'no');
                $template->set_var('ADVANCED_BUTTON', $TEXT['SHOW_ADVANCED'].'  &gt;&gt;');
            }

            // Explode system permissions
            $system_permissions = explode(',', $group['system_permissions']);
            // Check system permissions boxes
            foreach($system_permissions AS $name) {
                    $template->set_var($name.'_checked', ' checked="checked"');
            }
            // Explode module permissions
            $module_permissions = explode(',', $group['module_permissions']);

            // Explode template permissions
            $template_permissions = explode(',', $group['template_permissions']);

            // Insert values into module list
            $template->set_block('main_block', 'module_list_block', 'module_list');

            $sql = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
                  .'WHERE `type` = \'module\' '
                  .'AND (`function` = \'page\' OR `function` = \'tool\' ) ORDER BY `function`, `name`';

            $result = $database->query($sql);
            $aCheckedList = array();

            $ChangeFunction = 'page';
            if($result->numRows() > 0) {
                $i=0;
                while($addon = $result->fetchRow(MYSQLI_ASSOC)) {
                    if( $addon['function']!=$ChangeFunction ){
                        $ChangeFunction = $addon['function'];
                    }
                    $template->set_var('VALUE', $addon['directory']);
                    $template->set_var('NAME', (($addon['function'] == 'page') ? $addon['name'].'':' (A) '.$addon['name'])   );
                    if(!is_numeric(array_search($addon['directory'], $module_permissions))) {
                        $template->set_var('CHECKED', ' checked="checked"');
                        $aCheckedList[$i]['directory'] = $addon['directory'];
                        $aCheckedList[$i]['name'] = $addon['name'];
                        ++$i;
                    } else {
                        $template->set_var('CHECKED', '');
                    }
                    $template->parse('module_list', 'module_list_block', true);
                }
            }

            // Insert values into template list
            $template->set_block('main_block', 'template_list_block', 'template_list');
            $result = $database->query('SELECT * FROM `'.TABLE_PREFIX.'addons` WHERE `type` = "template" ORDER BY `name`');
            if($result->numRows() > 0) {
                while($addon = $result->fetchRow(MYSQLI_ASSOC)) {
                    $template->set_var('VALUE', $addon['directory']);
                    $template->set_var('NAME', $addon['name']);
                    if(!is_numeric(array_search($addon['directory'], $template_permissions))) {
                        $template->set_var('CHECKED', ' checked="checked"');
                    } else {
                        $template->set_var('CHECKED', '');
                    }
                    $template->parse('template_list', 'template_list_block', true);
                }
            }

            // Insert language text and messages
            $template->set_var(array(
                        'TEXT_CANCEL' => $TEXT['CANCEL'],
                        'TEXT_RESET' => $TEXT['RESET'],
                        'TEXT_ACTIVE' => $TEXT['ACTIVE'],
                        'TEXT_DISABLED' => $TEXT['DISABLED'],
                        'TEXT_PLEASE_SELECT' => $TEXT['PLEASE_SELECT'],
                        'TEXT_USERNAME' => $TEXT['USERNAME'],
                        'TEXT_PASSWORD' => $TEXT['PASSWORD'],
                        'TEXT_RETYPE_PASSWORD' => $TEXT['RETYPE_PASSWORD'],
                        'TEXT_DISPLAY_NAME' => $TEXT['DISPLAY_NAME'],
                        'TEXT_EMAIL' => $TEXT['EMAIL'],
                        'TEXT_GROUP' => $TEXT['GROUP'],
                        'TEXT_GROUPS' => $MENU['GROUPS'],
                        'TEXT_SYSTEM_PERMISSIONS' => $TEXT['SYSTEM_PERMISSIONS'],
                        'TEXT_MODULE_PERMISSIONS' => $TEXT['MODULE_PERMISSIONS'],
                        'TEXT_TEMPLATE_PERMISSIONS' => $TEXT['TEMPLATE_PERMISSIONS'],
                        'TEXT_NAME' => $TEXT['NAME'],
                        'SECTION_PAGES' => $MENU['PAGES'],
                        'SECTION_MEDIA' => $MENU['MEDIA'],
                        'SECTION_MODULES' => $MENU['MODULES'],
                        'SECTION_TEMPLATES' => $MENU['TEMPLATES'],
                        'SECTION_LANGUAGES' => $MENU['LANGUAGES'],
                        'SECTION_SETTINGS' => $MENU['SETTINGS'],
                        'SECTION_USERS' => $MENU['USERS'],
                        'SECTION_GROUPS' => $MENU['GROUPS'],
                        'SECTION_ADMINTOOLS' => $MENU['ADMINTOOLS'],
                        'TEXT_VIEW' => $TEXT['VIEW'],
                        'TEXT_ADD' => $TEXT['ADD'],
                        'TEXT_LEVEL' => $TEXT['LEVEL'],
                        'TEXT_MODIFY' => $TEXT['MODIFY'],
                        'TEXT_DELETE' => $TEXT['DELETE'],
                        'TEXT_MODIFY_CONTENT' => $TEXT['MODIFY_CONTENT'],
                        'TEXT_MODIFY_SETTINGS' => $TEXT['MODIFY_SETTINGS'],
                        'HEADING_MODIFY_INTRO_PAGE' => $HEADING['MODIFY_INTRO_PAGE'],
                        'TEXT_CREATE_FOLDER' => $TEXT['CREATE_FOLDER'],
                        'TEXT_RENAME' => $TEXT['RENAME'],
                        'TEXT_UPLOAD_FILES' => $TEXT['UPLOAD_FILES'],
                        'TEXT_BASIC' => $TEXT['BASIC'],
                        'TEXT_ADVANCED' => $TEXT['ADVANCED'],
                        'CHANGING_PASSWORD' => $MESSAGE['USERS']['CHANGING_PASSWORD'],
                        'DISPLAY_EXTRA' => 'display:block;',
                        'HEADING_MODIFY_GROUP' => $HEADING['MODIFY_GROUP'],
                    ));

            // Parse template object
            $template->parse('main', 'main_block', false);
            $template->pparse('output', 'page');
            // Print admin footer
            $admin->print_footer();
            break;
        case 'delete' :
            // Create new admin object
            $admin = new admin('Access', 'groups_delete', false);
            $group_id = intval($admin->checkIDKEY('group_id', 0, $_SERVER['REQUEST_METHOD']));
            if($group_id == 0){
                $admin->print_header();
                $admin->print_error($MESSAGE['USERS_NO_GROUP'] );  //  GENERIC_CANNOT_UNINSTALL
            }
            // Check if user id is a valid number and doesnt equal 1
            if( ($group_id < 2 ) )
            {
                $admin->print_header();
                $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
            }
            // Print header
            $admin->print_header();
            $query = "SELECT `name` FROM `".TABLE_PREFIX."groups` WHERE `group_id` = ".$group_id;
            if ( ($group_name = $database->get_one($query)) ) { }
            $query = "SELECT COUNT(*) FROM `".TABLE_PREFIX."users` WHERE `groups_id` like '%".$group_id."%'";
            if ( $database->get_one($query) == 0 ) {
                // Delete the group
                $database->query("DELETE FROM `".TABLE_PREFIX."groups` WHERE `group_id` = '".$group_id."' LIMIT 1");
                if($database->is_error()) {
                    $admin->print_error($database->get_error());
                } else {
                        $admin->print_success($MESSAGE['GROUPS_DELETED']);
                }
            } else {
              $admin->print_error('('.$TEXT['GROUP'].' '.$group_name.') '.$MESSAGE['GENERIC_CANNOT_UNINSTALL'] );  //  
            }
            $admin->print_footer();
            break;
    default:
            break;
endswitch;

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
/*---------------------------------------------------------------------------------------------------------*/
// Include config file and admin class file
if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// Set parameter 'action' as alternative to javascript mechanism
$js_back = ADMIN_URL.'/groups/index.php';
$requestMethod = '_'.($GLOBALS['_SERVER']['REQUEST_METHOD']);
$aRequestVars  = (@(${$requestMethod}) ? : null);

$bAdvanced = intval (@$aRequestVars['advanced'] ?: 0);
$bAdvancedSave   = intval(@$aRequestVars['advanced_exented'] ?: 0);
$sDefaultModules   = array();
$sDefaultTemplates = array();

$action = 'cancel';
// Set parameter 'action' as alternative to javascript mechanism
$action = (isset($aRequestVars['action']) && ($aRequestVars['action'] ='modify') ? 'modify' : $action );
$action = (isset($aRequestVars['modify']) ? 'modify' : $action );
$action = (isset($aRequestVars['delete']) ? 'delete' : $action );
/*-------------------------------------------------------------------------------------------------------*/
switch ($action):
    case 'cancel' :
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$js_back);
            exit;
    case 'modify' :
            // Create new admin object
            $admin = new admin('Access', 'groups_modify' );
            // Check if group group_id is a valid number and doesnt equal 1
            $group_id = intval($admin->checkIDKEY('group_id', false, $_SERVER['REQUEST_METHOD']));
            if($group_id === false){
                $admin->print_error($MESSAGE['USERS_NO_GROUP'], $js_back);
            }
            if( ($group_id < 2 ) )
            {
                // if($admin_header) { $admin->print_header(); }
                $sInfo = strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION).'_'.$group_id.'_::');
                $sDEBUG=(@DEBUG?$sInfo:'');
                $admin->print_error($sDEBUG.$MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back );
            }
            // Get existing values
            $sql  = 'SELECT * FROM  `'.TABLE_PREFIX.'groups` '
                  . 'WHERE `group_id` = '.$group_id;
            $results = $database->query($sql);
            $group = $results->fetchRow(MYSQLI_ASSOC);
            // Setup template object, parse vars to it, then parse it
            // Create new template object
            $template = new Template(dirname($admin->correct_theme_source('groups_form.htt')), 'remove');
            // $template->debug = true;
            //$template->set_unknowns('keep');
            $template->set_file('page', 'groups_form.htt');
            $template->set_block('page', 'main_block', 'main');
            $template->set_var(array(
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
            $template->set_block('main_block', 'groups_basic_block', 'groups_basic');
            $template->set_block('main_block', 'groups_extended_block', 'groups_extended');
            // Explode system permissions
            if ($group['system_permissions']) {
                $system_permissions = explode(',', $group['system_permissions']);
                // Check system permissions boxes
                foreach($system_permissions as $name) {
//                    echo (@DEBUG?$name.'_checked':'');
//                    $template->set_var($name.'_checked', '');
                    $template->set_var($name.'_checked', (!$admin->get_permission($name) ?' checked="checked"':''));
                }
          }
          if($bAdvanced)
          {
              $template->set_var('DISPLAY_ADVANCED', '');
              $template->set_var('DISPLAY_BASIC', 'display:none;');
              $template->set_var('ADVANCED_VALUE', 0);
              $template->set_var('ADVANCED_BUTTON', '&laquo; '.$TEXT['HIDE_ADVANCED']);
              $template->parse('groups_extended', 'groups_extended_block', true);
              $template->set_block('groups_basic', '', '');
          } else {
              $template->set_var('DISPLAY_ADVANCED', 'display:none;');
              $template->set_var('DISPLAY_BASIC', '');
              $template->set_var('ADVANCED_VALUE', 1);
              $template->set_var('ADVANCED_BUTTON', $TEXT['SHOW_ADVANCED'].' &raquo;');
              $template->parse('groups_basic', 'groups_basic_block', true);
              $template->set_block('groups_extended', '');
          }
          // Explode module permissions
          $module_permissions = explode(',', $group['module_permissions']);
          $module_permissions = array_diff($module_permissions, $sDefaultModules);
          // Explode template permissions
          $template_permissions = explode(',', $group['template_permissions']);
          $template_permissions = array_diff($template_permissions, $sDefaultTemplates);
/*-------------------------------------------------------------------------------------------------------*/
// Insert values into module list
    $template->set_block('main_block', 'module_list_block', 'module_list');
    $template->set_block('main_block', 'module_group_block', 'module_group');
    $aCheckedList = array();
    $GroupsFunction = '';
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
          . 'WHERE `type` = \'module\' '
          .   'AND `function` IN (\'page\', \'tool\') '
          . 'ORDER BY `function`, `name`';
    if($result = $database->query($sql))
    {
        $i=0;
        while($addon = $result->fetchRow(MYSQLI_ASSOC)) {
            $template->set_var('OPTGROUP', '');
            $template->set_block('module_function', '');
            if (strcasecmp($addon['function'], $GroupsFunction)!== 0){
                $template->set_var('OPTGROUP', ucwords($addon['function']));
                $template->parse('module_group', 'module_group_block', true);
            }
            $template->set_var('VALUE', $addon['directory']);
            $template->set_var('NAME', (($addon['function'] == 'page') ? $addon['name'] :''.$addon['name']));
            if (!is_numeric(array_search($addon['directory'], $module_permissions)) )
            {
                $template->set_var('CHECKED', ' checked="checked"');
                $aCheckedList[$i]['directory'] = $addon['directory'];
                $aCheckedList[$i]['name'] = $addon['name'];
                ++$i;
            } else {
                $template->set_var('CHECKED', '');
            }
            $GroupsFunction = $addon['function'];
            $template->parse('module_list', 'module_list_block', true);
        }
    }
/*-------------------------------------------------------------------------------------------------------*/
// Insert values into template list
    $template->set_block('main_block', 'template_list_block', 'template_list');
    $template->set_block('main_block', 'template_group_block', 'template_group');
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'addons` '
          . 'WHERE `type` = \'template\' '
          . 'ORDER BY `function`, `name`';
    if($result = $database->query($sql))
    {
        $i=0;
        while( $addon = $result->fetchRow(MYSQLI_ASSOC)) {
            $template->set_var('OPTGROUP', '');
            $template->set_block('template_function', '');
            if (strcasecmp($addon['function'], $GroupsFunction)!== 0){
                $template->set_var('OPTGROUP', ucwords($addon['function']));
                $template->parse('template_group', 'template_group_block', true);
            }
            $template->set_var('VALUE', $addon['directory']);
            $template->set_var('NAME', $addon['name']   );
            if(!is_numeric(array_search($addon['directory'], $template_permissions)))
            {
                $template->set_var('CHECKED', ' checked="checked"');
                $aCheckedList[$i]['directory'] = $addon['directory'];
                $aCheckedList[$i]['name'] = $addon['name'];
                ++$i;
            } else {
                $template->set_var('CHECKED', '');
            }
            $GroupsFunction = $addon['function'];
            $template->parse('template_list', 'template_list_block', true);
        }
    }
/*-------------------------------------------------------------------------------------------------------*/
            // Insert language text and messages
            $template->set_var(array(
                        'TEXT_CANCEL' => $TEXT['CANCEL'],
                        'TEXT_RESET' => $TEXT['RESET'],
                        'TEXT_FILESYSTEM_PERMISSIONS' => $TEXT['FILESYSTEM_PERMISSIONS'],
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
                        'HEADING_MODIFY_INTRO_PAGE' => $TEXT['INTRO_PAGE'],
                        'TEXT_CREATE_FOLDER' => $TEXT['CREATE_FOLDER'],
                        'TEXT_RENAME' => $TEXT['RENAME'],
                        'TEXT_UPLOAD_FILES' => $TEXT['UPLOAD_FILES'],
                        'TEXT_BASIC' => $TEXT['BASIC'],
                        'TEXT_ADVANCED' => $TEXT['ADVANCED'],
                        'CHANGING_PASSWORD' => $MESSAGE['USERS_CHANGING_PASSWORD'],
                        'DISPLAY_EXTRA' => 'display:block;',
                        'HEADING_MODIFY_GROUP' => $HEADING['MODIFY_GROUP'],
                        'DEBUG_MSG'=>(@$DebugOLutput?:'')
                    ));
/*-------------------------------------------------------------------------------------------------------*/
            // Parse template object
            $template->parse('main', 'main_block', false);
            $template->pparse('output', 'page');
            // Print admin footer
            $admin->print_footer();
            break;
        case 'delete' :
            // Create new admin object
            $admin = new admin('Access', 'groups_delete', false);
            $group_id = intval($admin->checkIDKEY('group_id', false, $_SERVER['REQUEST_METHOD']));
            if($group_id === false){
                $admin->print_header();
                $admin->print_error($MESSAGE['USERS_NO_GROUP'], $js_back);  //  GENERIC_CANNOT_UNINSTALL
            }
            // Check if user id is a valid number and doesnt equal 1
            if( ($group_id < 2 ) )
            {
                $admin->print_header();
                $sInfo = strtoupper(basename(__DIR__).'_'.basename(__FILE__, ''.PAGE_EXTENSION).'_idkey::');
                $sDEBUG=(@DEBUG?$sInfo:'');
                $admin->print_error($sDEBUG.$MESSAGE['GENERIC_SECURITY_ACCESS'], $js_back );
            }
            // Print header
            $admin->print_header();
            $sql  = 'SELECT `name` FROM `'.TABLE_PREFIX.'groups` '
                  .'WHERE `group_id`='.(int)$group_id.''
                  .'';
            if ( ($group_name = $database->get_one($sql)) ) { }
            $query = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'users` '
                   . 'WHERE `groups_id` like \'%'.$group_id.'%\'';
            if ( $database->get_one($query) == 0 ) {
                // Delete the group
            $sql  = 'DELETE FROM `'.TABLE_PREFIX.'groups` '
                  .'WHERE `group_id`='.(int)$group_id.''
                  .'';
                $database->query($sql);
                if($database->is_error()) {
                    $admin->print_error($database->get_error());
                } else {
                        $admin->print_success($MESSAGE['GROUPS_DELETED'], $js_back);
                }
            } else {
              $admin->print_error('('.$TEXT['GROUP'].' '.$group_name.') '.$MESSAGE['GENERIC_CANNOT_UNINSTALL'], $js_back);
            }
            $admin->print_footer();
            break;
    default:
            break;
endswitch;

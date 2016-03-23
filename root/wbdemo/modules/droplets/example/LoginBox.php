//:Puts a Login / Logout box on your page.
//:Use: [[LoginBox?redirect=url]]
//:Absolute or relative url possible
//:Remember to enable frontend login in your website settings!!
    global $database,$wb, $TEXT, $MENU, $HEADING;
    $return_value = '<div class="login-box">'."\n";
    $return_admin = ' ';
    
    // Return a system permission
    $get_permission = function ($name, $type = 'system') use ( $wb )
    {
    // Append to permission type
        $type .= '_permissions';
        // Check if we have a section to check for
        if($name == 'start') {
            return true;
        } else {
            // Set system permissions var
            $system_permissions = $wb->get_session('SYSTEM_PERMISSIONS');
            // Set module permissions var
            $module_permissions = $wb->get_session('MODULE_PERMISSIONS');
            // Set template permissions var
            $template_permissions = $wb->get_session('TEMPLATE_PERMISSIONS');
            // Return true if system perm = 1
            if (isset($$type) && is_array($$type) && is_numeric(array_search($name, $$type))) {
                if($type == 'system_permissions') {
                    return true;
                } else {
                    return false;
                }
            } else {
                if($type == 'system_permissions') {
                    return false;
                } else {
                    return true;
                }
            }
        }
    };
    $get_page_permission = function ($page, $action='admin') use ( $database, $wb )
    {
        if ($action!='viewing'){ $action='admin';}
        $action_groups = $action.'_groups';
        $action_users  = $action.'_users';
        if (is_array($page)) {
            $groups = $page[$action_groups];
            $users  = $page[$action_users];
        } else {
            $sql  = 'SELECT '.$action_groups.','.$action_users.' FROM '.TABLE_PREFIX.'pages '
                  . 'WHERE page_id = \''.$page.'\'';
            if($oResults = $database->query( $sql )){
                $aResult  = $oResults->fetchRow( MYSQLI_ASSOC );
                $groups  = explode(',', str_replace('_', '', $aResult[$action_groups]));
                $users   = explode(',', str_replace('_', '', $aResult[$action_users]));
            }
        }
        $in_group = false;
        foreach($wb->get_groups_id() as $cur_gid){
            if (in_array( $cur_gid, $groups )) {
                $in_group = true;
            }
        }
        if( !$in_group && !is_numeric(array_search( $wb->get_user_id(), $users )) ) {
            return false;
        }
        return true;
    };
    // Get redirect
    $redirect_url = ((isset($_SESSION['HTTP_REFERER']) && $_SESSION['HTTP_REFERER'] != '') ? $_SESSION['HTTP_REFERER'] : WB_URL );
    $redirect_url = ( isset($redirect) && ($redirect!='') ? $redirect : $redirect_url);
    if ( ( FRONTEND_LOGIN == 'enabled') && ( VISIBILITY != 'private') && ( $wb->get_session('USER_ID') == '')  )
    {
        $return_value .= '<form action="'.LOGIN_URL.'" method="post">'."\n";
        $return_value .= '<input type="hidden" name="redirect" value="'.$redirect_url.'" />'."\n";
        $return_value .= '<fieldset>'."\n";
        $return_value .= '<h1>'.$TEXT['LOGIN'].'</h1>'."\n";
        $return_value .= '<label for="username">'.$TEXT['USERNAME'].':</label>'."\n";
        $return_value .= '<p><input type="text" name="username" id="username"  /></p>'."\n";
        $return_value .= '<label for="password">'.$TEXT['PASSWORD'].':</label>'."\n";
        $return_value .= '<p><input type="password" name="password" id="password"/></p>'."\n";
        $return_value .= '<p><input type="submit" id="submit" value="'.$TEXT['LOGIN'].'" class="dbutton" /></p>'."\n";
        $return_value .= '<ul class="login-advance">'."\n";
        $return_value .= '<li class="forgot"><a href="'.FORGOT_URL.'"><span>'.$TEXT['FORGOT_DETAILS'].'</span></a></li>'."\n";
        if (intval(FRONTEND_SIGNUP) > 0)
        {
        $return_value .= '<li class="sign"><a href="'.SIGNUP_URL.'">'.$TEXT['SIGNUP'].'</a></li>'."\n";
        }
        $return_value .= '</ul>'."\n";
        $return_value .= '</fieldset>'."\n";
        $return_value .= '</form>'."\n";
    } elseif( (FRONTEND_LOGIN == 'enabled') && (is_numeric($wb->get_session('USER_ID'))) )
    {
        $return_value .= '<form action="'.LOGOUT_URL.'" method="post" class="login-table">'."\n";
        $return_value .= '<input type="hidden" name="redirect" value="'.$redirect_url.'" />'."\n";
        $return_value .= '<fieldset>'."\n";
        $return_value .= '<h1>'.$TEXT["LOGGED_IN"].'</h1>'."\n";
        $return_value .= '<label>'.$TEXT['WELCOME_BACK'].', '.$wb->get_display_name().'</label>'."\n";
        $return_value .= '<p><input type="submit" name="submit" value="'.$MENU['LOGOUT'].'" class="dbutton" /></p>'."\n";
        $return_value .= '<ul class="logout-advance">'."\n";
        $return_value .= '<li class="preference"><a href="'.PREFERENCES_URL.'" title="'.$MENU['PREFERENCES'].'">'.$MENU['PREFERENCES'].'</a></li>'."\n";
        if ($wb->ami_group_member('1'))  //change ot the group that should get special links
        {
            $return_admin .= '<li class="admin"><a target="_blank" href="'.ADMIN_URL.'/index.php" title="'.$TEXT['ADMINISTRATION'].'" class="blank_target">'.$TEXT["ADMINISTRATION"].'</a></li>'."\n";
            //you can add more links for your users like userpage, lastchangedpages or something
            $return_value .= $return_admin;
        }
        //change ot the group that should get special links
        if( $get_permission('pages_modify') && $get_page_permission( PAGE_ID ) )
        {
            $return_value .= '<li class="modify"><a target="_blank" href="'.ADMIN_URL.'/pages/modify.php?page_id='.PAGE_ID.'" title="'.$HEADING['MODIFY_PAGE'].'" class="blank_target">'.$HEADING['MODIFY_PAGE'].'</a></li>'."\n";
        }
        $return_value .= '</ul>'."\n";
        $return_value .= '</fieldset>'."\n";
        $return_value .= '</form>'."\n";
    }
    $return_value .= '</div>'."\n";
    return $return_value;
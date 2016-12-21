<?php
/**
 *
 * @category        backend
 * @package         installation
 * @author          WebsiteBaker Project
 * @copyright       Website Baker Org. e.V.
 * @link            http://wwebsitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade-script.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/upgrade-script.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
 */
// PHP less then 5.3.2 is prohibited ---
if (version_compare(PHP_VERSION, '5.3.6', '<')) {
    $sMsg = '<p style="color: #ff0000;">WebsiteBaker 2.8.3 and above is not able to run with PHP-Version less then 5.3.6!!<br />'
          . 'Please change your PHP-Version to any kind from '.PHP_VERSION.' and up!<br />'
          . 'If you have problems to solve that, ask your hosting provider for it.<br  />'
          . 'The very best solution is the use of PHP-5.6 and up</p>';
    die($sMsg);
}

@require_once('config.php');

require_once(WB_PATH.'/framework/functions.php');
require_once(WB_PATH.'/framework/class.admin.php');
$admin = new admin('Addons', 'modules', false, false);

/* display a status message on the screen **************************************
 * @param string $message: the message to show
 * @param string $class:   kind of message as a css-class
 * @param string $element: witch HTML-tag use to cover the message
 * @return void
 */
function status_msg($message, $class='check', $element='p')
{
    // returns a status message
    $msg  = '<'.$element.' class="'.$class.'" style="padding: 0 0 2.00em 0.825em; ">';
#    $msg .= '<h4>'.strtoupper(strtok($class, ' ')).'</h4>';
    $msg .= $message.'</'.$element.'>';
    echo '<div class="message">'.$msg.'</div>';
}

    // pls add all addons to the blacklist array, which don't have a valid upgrade.php'
    $aModuleBlackList = array ( 'guestbook', 'lib_jquery', 'bookings_v2.36', 'oneforall' );

    if (is_readable(WB_PATH.'/install/ModuleWhiteList')){
        $aModuleWhiteList = file(WB_PATH.'/install/ModuleWhiteList', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    } else {
        $aModuleWhiteList =
              array (
                    'captcha_control',
                    'ckeditor',
                    'code',
                    'droplets',
                    'form',
                    'jsadmin',
                    'menu_link',
                    'mod_multilingual',
                    'news',
                    'output_filter',
                    'show_menu2',
                    'wrapper',
                    'wysiwyg'
            );
    }


// database tables including in WB package
$table_list = array ('settings','groups','addons','pages','sections','search','users');
/*
$table_list = array (
    'settings','groups','addons','pages','sections','search','users',
    'mod_captcha_control','mod_code','mod_droplets','mod_form_fields',
    'mod_form_settings','mod_form_submissions','mod_jsadmin','mod_menu_link',
    'mod_news_comments','mod_news_groups','mod_news_posts','mod_news_settings',
    'mod_output_filter','mod_wrapper','mod_wysiwyg'
);
*/

$OK               = ' <span class="ok">OK</span> ';
$FAIL             = ' <span class="error">FAILED</span> ';
$DEFAULT_THEME    = 'DefaultTheme';
$DEFAULT_TEMPLATE = (@DEFAULT_TEMPLATE?:'DefaultTemplate');
$sThemeUrl = WB_URL.'/templates/'.(is_readable(WB_URL.'/templates/'.$DEFAULT_THEME) ? $DEFAULT_THEME:'DefaultTheme');
$stepID = 0;
$dirRemove = array(
            '[INCLUDE]lightbox/',
            '[MODULES]SecureFormSwitcher/',
            '[MODULES]fckeditor/'
/*
            '[TEMPLATE]allcss/',
            '[TEMPLATE]blank/',
            '[TEMPLATE]round/',
            '[TEMPLATE]simple/',
*/
         );

$filesRemove = array(
            '[ROOT]SP5_UPGRADE_DE',
            '[ROOT]SP5_UPGRADE_EN',
            '[ROOT]SP6_UPGRADE_EN',

            '[ACCOUNT]template.html',

            '[ADMIN]preferences/details.php',
            '[ADMIN]preferences/email.php',
            '[ADMIN]preferences/password.php',
            '[ADMIN]settings/setting.js',
            '[ADMIN]settings/array.php',

            '[FRAMEWORK]class.login.php',
            '[FRAMEWORK]SecureForm.mtab.php',
            '[FRAMEWORK]SecureForm.php',

            '[INSTALL]install_struct.sql',
            '[INSTALL]install_data.sql',
/*  */
            '[MODULES]ckeditor/ckeditor/plugins/plugin.js',

            '[MODULES]captcha_control/uninstall.php',
            '[MODULES]jsadmin/uninstall.php',
            '[MODULES]menu_link/uninstall.php',
            '[MODULES]output_filter/uninstall.php',
            '[MODULES]output_filter/filters/canonical.php',
            '[MODULES]output_filter/filters/filterScript.php',
            '[MODULES]output_filter/filters/filterSysvarMedia.php',
            '[MODULES]show_menu2/uninstall.php',
            '[MODULES]wysiwyg/uninstall.php',

            '[MODULES]droplets/add_droplet.php',
            '[MODULES]droplets/backup_droplets.php',
            '[MODULES]droplets/delete_droplet.php',
            '[MODULES]droplets/modify_droplet.php',
            '[MODULES]droplets/save_droplet.php',
            '[MODULES]droplets/languages/DA.php',

            '[MODULES]form/save_field.php',

            '[TEMPLATE]argos_theme/templates/access.htt',
            '[TEMPLATE]argos_theme/templates/addons.htt',
            '[TEMPLATE]argos_theme/templates/admintools.htt',
            '[TEMPLATE]argos_theme/templates/error.htt',
            '[TEMPLATE]argos_theme/templates/groups.htt',
            '[TEMPLATE]argos_theme/templates/groups_form.htt',
            '[TEMPLATE]argos_theme/templates/languages.htt',
            '[TEMPLATE]argos_theme/templates/languages_details.htt',
            '[TEMPLATE]argos_theme/templates/media.htt',
            '[TEMPLATE]argos_theme/templates/media_browse.htt',
            '[TEMPLATE]argos_theme/templates/media_rename.htt',
            '[TEMPLATE]argos_theme/templates/modules.htt',
            '[TEMPLATE]argos_theme/templates/modules_details.htt',
            '[TEMPLATE]argos_theme/templates/pages.htt',
            '[TEMPLATE]argos_theme/templates/pages_modify.htt',
            '[TEMPLATE]argos_theme/templates/pages_sections.htt',
            '[TEMPLATE]argos_theme/templates/pages_settings.htt',
            '[TEMPLATE]argos_theme/templates/preferences.htt',
            '[TEMPLATE]argos_theme/templates/setparameter.htt',
            '[TEMPLATE]argos_theme/templates/settings.htt',
            '[TEMPLATE]argos_theme/templates/start.htt',
            '[TEMPLATE]argos_theme/templates/success.htt',
            '[TEMPLATE]argos_theme/templates/templates.htt',
            '[TEMPLATE]argos_theme/templates/templates_details.htt',
            '[TEMPLATE]argos_theme/templates/users.htt',
            '[TEMPLATE]argos_theme/templates/users_form.htt',

            '[TEMPLATE]wb_theme/uninstall.php',
            '[TEMPLATE]wb_theme/templates/access.htt',
            '[TEMPLATE]wb_theme/templates/addons.htt',
            '[TEMPLATE]wb_theme/templates/admintools.htt',
            '[TEMPLATE]wb_theme/templates/error.htt',
            '[TEMPLATE]wb_theme/templates/groups.htt',
            '[TEMPLATE]wb_theme/templates/groups_form.htt',
            '[TEMPLATE]wb_theme/templates/languages.htt',
            '[TEMPLATE]wb_theme/templates/languages_details.htt',
            '[TEMPLATE]wb_theme/templates/media.htt',
            '[TEMPLATE]wb_theme/templates/media_browse.htt',
            '[TEMPLATE]wb_theme/templates/media_rename.htt',
            '[TEMPLATE]wb_theme/templates/modules.htt',
            '[TEMPLATE]wb_theme/templates/modules_details.htt',
            '[TEMPLATE]wb_theme/templates/pages.htt',
            '[TEMPLATE]wb_theme/templates/pages_modify.htt',
            '[TEMPLATE]wb_theme/templates/pages_sections.htt',
            '[TEMPLATE]wb_theme/templates/pages_settings.htt',
            '[TEMPLATE]wb_theme/templates/preferences.htt',
            '[TEMPLATE]wb_theme/templates/setparameter.htt',
//            '[TEMPLATE]wb_theme/templates/settings.htt', SP7 replace this
            '[TEMPLATE]wb_theme/templates/start.htt',
            '[TEMPLATE]wb_theme/templates/success.htt',
            '[TEMPLATE]wb_theme/templates/templates.htt',
            '[TEMPLATE]wb_theme/templates/templates_details.htt',
            '[TEMPLATE]wb_theme/templates/users.htt',
            '[TEMPLATE]wb_theme/templates/users_form.htt',
         );

// analyze/check database tables
function mysqlCheckTables( $dbName )
{
    global $database, $table_list,$FAIL;
    $table_prefix = TABLE_PREFIX;

    $sql = 'SHOW TABLES FROM `'.$dbName.'`';
    $result = $database->query($sql);

    $data = array();
    $retVal = array();
    $x = 0;

//    while( ( $row = @mysqli_fetch_array( $result, MYSQLI_NUM ) ) == true )
    while (( $row = $result->fetchRow(MYSQLI_NUM)) == true)
    {
                $sql = "CHECK TABLE `" . $row[0].'`';
                $analyze = $database->query($sql);
                if( $analyze ) {
                    $rowFetch = $analyze->fetchRow(MYSQLI_ASSOC);
                    $data[$x]['Op'] = $rowFetch["Op"];
                    $data[$x]['Msg_type'] = $rowFetch["Msg_type"];
                    $msgColor = '<span class="error">';
                    $data[$x]['Table'] = $row[0];
                    $retVal[] = $row[0];
                   // print  " ";
                    $msgColor = ($rowFetch["Msg_text"] == 'OK') ? '<span class="ok">' : '<span class="error">';
                    $data[$x]['Msg_text'] = $msgColor.$rowFetch["Msg_text"].'</span>';
                   // print  "";
                    $x++;
                 } else {
                    echo '<br /><b>'.$sql.'</b>'.$FAIL.'<br />';
                }
   }
    return $retVal; //$data;
}

// check existings tables for upgrade or install
function check_wb_tables()
{
    global $database,$table_list;

 // if prefix inludes '_' or '%'
 $search_for = addcslashes ( TABLE_PREFIX, '%_' );
 $get_result = $database->query( 'SHOW TABLES LIKE "'.$search_for.'%"');

        // $get_result = $database->query( "SHOW TABLES FROM ".DB_NAME);
        $all_tables = array();
        if($get_result->numRows() > 0)
        {
            while ($data = $get_result->fetchRow())
            {
                $tmp = str_replace(TABLE_PREFIX, '', $data[0]);
                if(in_array($tmp,$table_list))
                {
                    $all_tables[] = $tmp;
                }
            }
        }
     return $all_tables;
}

// check existing tables
$all_tables = check_wb_tables();

?><!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Upgrade script</title>
<style type="text/css">
html { overflow: -moz-scrollbars-vertical; /* Force firefox to always show room for a vertical scrollbar */ }

body {
    margin:0;
    padding:0;
    border:0;
    background: #EBF7FC;
    color:#000;
    font-family: 'Trebuchet MS', Verdana, Arial, Helvetica, Sans-Serif;
    font-size: small;
    height:101%;
}

#container {
    width:85%;
    background: #A8BCCB url("<?php echo $sThemeUrl;?>/images/background.png") repeat-x;
    border:1px solid #000;
    color:#000;
    margin:2em auto;
    padding:0 15px;
    min-height: 500px;
    text-align:left;
}

p { line-height:1.5em; }

form {
    display: inline-block;
    line-height: 20px;
    vertical-align: baseline;
}
input[type="submit"].restart {
    background-color: #FFDBDB;
    font-weight: bold;
}

h1,h2,h3,h4,h5,h6 {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    color: #369;
    margin-top: 1.0em;
    margin-bottom: 0.1em;
}

h1 { font-size:150%; }
h2 { font-size: 130%; border-bottom: 1px #CCC solid; }
h3 { font-size: 120%; }

.ok, .error { font-weight:bold; }
.ok { color:green; }
.error { color: red; }
.check { color:#555; }
.content { margin-left: 1.925em; }
.warning {
    width: 98%;
    background:#FCDADA;
    padding:0.2em;
    margin-top:0.5em;
    border: 1px solid black;
}
.error p { color: #369; }

.info {
    width: 98%;
    background:#C3E3C3;
    padding:0.2em;
    margin-top:0.5em;
    border: 1px solid black;
}
.message { padding: 0; }

</style>
</head>
<body>
<div id="container">
<img src="<?php echo $sThemeUrl;?>/images/logo.png" alt="WebsiteBaker Project" />
<h1>WebsiteBaker Upgrade</h1>
<?php
    if( version_compare( WB_VERSION, '2.7', '<' )) {
        status_msg('It is not possible to upgrade from WebsiteBaker Versions before 2.7.<br />For upgrading to version '.VERSION.' you must upgrade first to v.2.8 at least!!!', 'warning', 'div');
        echo '<br />';
        echo "
        </body>
        </html>
        ";
        exit();
    }

$oldVersionOutput  = trim(''.WB_VERSION.'+'.( defined('WB_SP') ? WB_SP : ''), '+').' (r'.WB_REVISION.')';
$newVersionOutput  = trim(''.VERSION.'+'.( defined('SP') ? SP : ''), '+').' (r'.REVISION.')';
$oldVersion  = trim(''.WB_VERSION.'+'.WB_REVISION.'+'.( defined('WB_SP') ? WB_SP : ''), '+');
$newVersion  = trim(''.VERSION.'+'.REVISION.'+'.( defined('SP') ? SP : ''), '+');
if ( WB_VERSION != '2.8.4'){
    if (version_compare($oldVersion, $newVersion, '>') === true) {
        status_msg('It is not possible to upgrade from WebsiteBaker Versions '.WB_VERSION.'!<br />For upgrading to version '.$newVersionOutput.' you have to upgrade first to v.2.8.3 at least!!!', 'warning', 'div');
        echo '<br />';
        echo "
        </body>
        </html>
        ";
        exit();
    }
}
if($admin->get_user_id()!=1){
  status_msg('<br /><h3>WebsiteBaker upgrading is not possible!<br />Before upgrading '
            .'to Revision '.REVISION.' you have to login as System-Administrator!</h3>',
            'warning', 'div');
  echo '<br /><br />';
// delete remember key of current user from database
  //if (isset($_SESSION['USER_ID']) && isset($database)) {
  //     $table = TABLE_PREFIX . 'users';
  //     $sql = "UPDATE `$table` SET `remember_key` = '' WHERE `user_id` = '" . (int) $_SESSION['USER_ID'] . "'";
  //     $database->doQuery($sql);
  //}
// delete remember key cookie if set
  if (isset($_COOKIE['REMEMBER_KEY']) && !headers_sent() ) {
    setcookie('REMEMBER_KEY', '', time() - 3600, '/');
  }
  // delete most critical session variables manually
  $_SESSION['USER_ID'] = null;
  $_SESSION['GROUP_ID'] = null;
  $_SESSION['GROUPS_ID'] = null;
  $_SESSION['USERNAME'] = null;
  $_SESSION['PAGE_PERMISSIONS'] = null;
  $_SESSION['SYSTEM_PERMISSIONS'] = null;
  // overwrite session array
  $_SESSION = array();
  // delete session cookie if set
  if (isset($_COOKIE[session_name()]) && !headers_sent()) {
    setcookie(session_name(), '', time() - 42000, '/');
  }
  // delete the session itself
  session_destroy();
  status_msg('<br /><h3>You have to login as System-Adminstrator start '
            .'upgrade-script.php again!</h3>',
             'info', 'div');
  echo '<br /><br />';
  if(defined('ADMIN_URL')) {
    echo '<form action="'.ADMIN_URL.'/index.php" method="post">'
        .'&nbsp;<input name="backend_send" type="submit" value="Kick me to the Login" />'
        .'</form>';
  }
  echo '<br /><br /></div>'
      .'</div>'
      .'</div>'
      .'</body>'
      .'</html>';
  exit();
}

?>
<p>This script upgrades an existing WebsiteBaker <strong> <?php echo $oldVersionOutput; ?></strong> installation to the <strong> <?php echo $newVersionOutput ?> </strong>.<br />The upgrade script alters the existing WB database to reflect the changes introduced with WB 2.8.x</p>

<?php
/**
 * Check if disclaimer was accepted
 */
if (!(isset($_POST['backup_confirmed']) && $_POST['backup_confirmed'] == 'confirmed')) { ?>
<h2>Step 1: Backup your files</h2>
<p>It is highly recommended to <strong>create a manual backup</strong> of the entire <strong>/pages folder</strong> and the <strong>MySQL database</strong> before proceeding.<br /><strong class="error">Note: </strong>The upgrade script alters some settings of your existing database!!! You need to confirm the disclaimer before proceeding.</p>

<form name="send" action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="post">
    <textarea cols="80" rows="5">DISCLAIMER: The WebsiteBaker upgrade script is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. One needs to confirm that a manual backup of the /pages folder (including all files and subfolders contained in it) and backup of the entire WebsiteBaker MySQL database was created before you can proceed.</textarea>
    <br /><br /><input name="backup_confirmed" type="checkbox" value="confirmed" />&nbsp;I confirm that a manual backup of the /pages folder and the MySQL database was created.
    <br /><br /><input name="send" type="submit" value="Start upgrade script" />
    </form>
    <br />
<?php
    status_msg('<h4>You need to confirm that you have created a manual backup of the /pages directory and the MySQL database before you can proceed.</h4>', 'warning', 'div');
    echo '<br />';
    echo "</div>
    </body>
    </html>
    ";
    exit();
}

// function to add a var/value-pair into settings-table
function db_add_key_value($key, $value) {
    global $database, $OK, $FAIL;
    $table = TABLE_PREFIX.'settings';
    $query = $database->query("SELECT value FROM $table WHERE name = '$key' ");
    if($query->numRows() > 0) {
        echo "$key: already exists. $OK.<br />";
        return true;
    } else {
        $database->query("INSERT INTO $table (name,value) VALUES ('$key', '$value')");
        echo ($database->is_error() ? $database->get_error().'<br />' : '');
        $query = $database->query("SELECT value FROM $table WHERE name = '$key' ");
        if($query->numRows() > 0) {
            echo "$key: $OK.<br />";
            return true;
        } else {
            echo "$key: $FAIL!<br />";
            return false;
        }
    }
}

// function to add a new field into a table
function db_add_field($table, $field, $desc) {
    global $database, $OK, $FAIL;
    $table = TABLE_PREFIX.$table;
    $query = $database->query("DESCRIBE $table '$field'");
    if($query->numRows() == 0) { // add field
        $query = $database->query("ALTER TABLE $table ADD $field $desc");
        echo ($database->is_error() ? $database->get_error().'<br />' : '');
        $query = $database->query("DESCRIBE $table '$field'");
        echo ($database->is_error() ? $database->get_error().'<br />' : '');
        if($query->numRows() > 0) {
            echo "'$field' added. $OK.<br />";
        } else {
            echo "adding '$field' $FAIL!<br />";
        }
    } else {
        echo "'$field' already exists. $OK.<br />";
    }
}
/**
 *
 * @param object $oDb  current database object
 * @param string $sTablePrefix the valid TABLE_PREFIX
 * @return an error message or emty string on ok
 */
    function MigrateSettingsTable($oDb, $sTablePrefix)
    {
        $sRetval = '';
        $aSettings = array();
        $sql = 'SELECT * FROM `'.$sTablePrefix.'settings`';
        if (($oSettings = $oDb->query($sql))) {
            // backup all entries and remove duplicate entries
            while (($aEntry = $oSettings->fetchArray(MYSQLI_ASSOC))) {
                $aSettings[$aEntry ['name']] = $aEntry ['value'];
            }
            // drop the old table
            $sql = 'DROP TABLE IF EXISTS `'.$sTablePrefix.'settings`';
            if (!($oDb->query($sql))) { $sRetval = 'unable to delete old table `settings`'; goto end;}
            // recreate the table with correctet structure
            $sql = 'CREATE TABLE IF NOT EXISTS `'.$sTablePrefix.'settings` ('
                 .     '`name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\', '
                 .     '`value` text COLLATE utf8_unicode_ci NOT NULL, '
                 .     'PRIMARY KEY (`name`)'
                 . ')ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
            if (!($oDb->query($sql))) { $sRetval = 'unable to recreate table `settings`'; goto end; }
            // insert backed up entries into the new table
            foreach ($aSettings as $sName => $sValue) {
                $sql = 'INSERT INTO  `'.$sTablePrefix.'settings`'
                     . 'SET `name`=\''.$oDb->escapeString($sName).'\', '
                     .     '`value`=\''.$oDb->escapeString($sValue).'\'';
                if (!($oDb->query($sql))) { $sRetval = 'unable to insert values into new table `settings`'; goto end;}
            }
        } else {
            $sRetval = 'unable to read old table `settings`';
        }
end:
        return $sRetval;
    }

// check again all tables, to get a new array
 if(sizeof($all_tables) < sizeof($table_list)) { $all_tables = check_wb_tables(); }
/**********************************************************
 *  - check tables comin with WebsiteBaker
 */
    $check_text = 'total ';
    // $check_tables = mysqlCheckTables( DB_NAME ) ;
    if(sizeof($all_tables) == sizeof($table_list))
    {
        echo ('<h2>Step '.(++$stepID).' Your database '.DB_NAME.' has '.sizeof($all_tables).' '.$check_text.' tables from '.sizeof($table_list).' included in package '.$OK.'</h2>');
    }
    else
    {
        status_msg('can\'t run Upgrade, missing tables', 'warning', 'div');
        echo '<h4>Missing required tables. You can install them in backend->addons->modules->advanced. Then again run upgrade-script.php</h4>';
        $result = array_diff ( $table_list, $all_tables );
        echo '<h4 class="warning"><br />';
        while ( list ( $key, $val ) = each ( $result ) )
        {
            echo TABLE_PREFIX.$val.' '.$FAIL.'<br>';
        }
        echo '<br /></h4>';
        echo '<br /><form action="'. $_SERVER['SCRIPT_NAME'] .'">';
        echo '<input type="submit" value="kick me back" style="float:left;" />';
        echo '</form>';
        if(defined('ADMIN_URL'))
        {
            echo '<form action="'.ADMIN_URL.'" target="_self">';
            echo '&nbsp;<input type="submit" value="kick me to the Backend" />';
            echo '</form>';
        }
        echo "<br /><br /></div>
        </body>
        </html>
        ";
        exit();
    }
echo '<h2>Step '.(++$stepID).' : clear Translate cache if exists</h2>';
//**********************************************************
if (is_writable(WB_PATH.'/temp/cache')) {
    Translate::getInstance()->clearCache();
}

if (defined('DEBUG') && DEBUG){
    echo '<h2>Step '.(++$stepID).' : Adding/Updating settings table</h2>';
    echo "<br />Set DEBUG Modus to false in settings table<br />";
    db_update_key_value('settings', 'debug', 'false');
    $msg = '<p> The upgrade-script has be run properly, therefore the property Debug was set to the value false.</p><p>Please restart the upgrade-script!</p>';
    status_msg($msg, 'error warning', 'div');
    echo '<p style="font-size:120%;"><strong>WARNING: The upgrade script failed ...</strong></p>';
    echo '<form action="'.$_SERVER['SCRIPT_NAME'].'">';
    echo '&nbsp;<input name="send" type="submit" value="Restart upgrade script" />';
    echo '</form>';
    echo '<br /><br /></div></body></html>';
    exit;

}

/**********************************************************/

echo '<h2>Step '.(++$stepID).' : Adding/Updating database tables</h2>';
/**********************************************************
 *  - Upgrade Core Tables
echo "<br />Upgrade Core Tables <br />"; $mysqli->error_list
$sql = 'ALTER TABLE `'.TABLE_PREFIX.'addons` ADD UNIQUE `ident` ( `directory` )';
 */
// try to upgrade table if not exists
$sInstallStruct = WB_PATH.'/install/install-struct.sql';
if (is_readable($sInstallStruct))
{
    if (!$database->SqlImport($sInstallStruct, TABLE_PREFIX, true )){
        echo '<div class="content">';
        echo $database->get_error(). $FAIL.'(Index already exists)<br />';
        echo '</div>';
    } else {
        echo '<div class="content">';
        echo 'Upgrade Core Tables '. $OK.'<br />';
        echo '</div>';
        echo '<h2>Step '.(++$stepID).' Clear default title value in sections table</h2>';
        echo '<div class="content">';
        $sDescription = 'UPDATE `'.TABLE_PREFIX.'sections` SET `title` = REPLACE(`title`,\'Section-ID 0\',\'\') WHERE `title` LIKE \'%Section-ID%\'';
        if (!$database->query($sDescription)){
          echo 'Upgrading sections Table (empty title field) '. $FAIL.'<br />';
        } else {
          echo 'Upgrade sections Table '. $OK.'<br />';
        }
        echo '</div>';
    }
} else {

    if (!is_readable(WB_PATH.'/install')) {
    $msg = '<p>\'Missing or not readable install folder\' '.$FAIL.'</p>';
    } else {
    $msg = '<p>\'Missing or not readable file [install-struct.sql]\'</p> '.$FAIL.'';
    }
/*
            $sWbPath = str_replace('\\', '/', WB_PATH );
            array_walk($aMsg, function(&$sMsg) use ($sWbPath) { $sMsg = str_replace($sWbPath, '', $sMsg); });
            $msg = implode('<br />', $aMsg).'<br />';
*/
    $msg = $msg.'<p>Check if the install folder exist.<br />Please upload install folder
            using FTP and restart upgrade-script!</p>';
    status_msg($msg, 'error warning', 'div');
    echo '<p style="font-size:120%;">>WARNING: The upgrade script failed ...</p>';
    echo '<form action="'.$_SERVER['SCRIPT_NAME'].'">';
    echo '&nbsp;<input name="send" type="submit" value="Restart upgrade script" />';
    echo '</form>';
    echo '<br /><br /></div></body></html>';
    exit;
}

// --- modify table `settings` -----------------------------------------------------------
    echo '<h2>Step '.(++$stepID).' : Modify PRIMARY KEY in settings table</h2>';
    echo '<div class="content">';
    $msg = MigrateSettingsTable($database, TABLE_PREFIX);
    echo ($msg!=''?$msg.' '.$FAIL:'Add PRIMARY Index name '.$OK).'<br />';
    echo '</div>';

    echo '<h2>Step '.(++$stepID).' : Updating default_theme/default_template in settings table</h2>';
/**********************************************************
 *  - Adding field default_theme to settings table
 */
    echo '<div class="content">';
    echo "Adding default_theme to settings table<br />";
    db_update_key_value('settings', 'default_theme', $DEFAULT_THEME);
    echo "Adding default_template to settings table<br />";
    db_update_key_value('settings', 'default_template', $DEFAULT_TEMPLATE);
    echo '</div>';

#echo '<h2>Step '.(++$stepID).' : checking database entries</h2>';
    $check_tables = mysqlCheckTables( DB_NAME ) ;

/**********************************************************
 *  - install droplets
echo '<h2>Step '.(++$stepID).' : checking table droplets</h2>';
    echo '<div class="content">';
    $drops = (!in_array ( TABLE_PREFIX."mod_droplets", $check_tables)) ? "Install droplets" : "Upgrade droplets";
    echo '<b>'.$drops.'</b><br />';
     $file_name = (!in_array ( TABLE_PREFIX."mod_droplets", $check_tables) ? "install.php" : "upgrade.php");
     require_once (WB_PATH."/modules/droplets/".$file_name);
    echo '</div>';
 */

/**********************************************************
 *  - Adding field sec_anchor to settings table
 */
    echo '<h2>Step '.(++$stepID).' : Adding/Updating settings table</h2>';
    echo '<div class="content">';
    echo "<br />Adding string_dir_mode and string_file_mode to settings table<br />";
    $cfg = array(
        'confirmed_registration' => (defined('CONFIRMED_REGISTRATION')?CONFIRMED_REGISTRATION:'0'),
        'groups_updated' => (defined('GROUPS_UPDATED')?GROUPS_UPDATED:''),
        'page_icon_dir' => (defined('PAGE_ICON_DIR')?PAGE_ICON_DIR:'/templates/*/title_images'),
        'system_locked' => (defined('SYSTEM_LOCKED')?SYSTEM_LOCKED:'0'),
        'string_dir_mode' => (defined('STRING_DIR_MODE')?STRING_DIR_MODE:'0755'),
        'string_file_mode' => (defined('STRING_FILE_MODE')?STRING_FILE_MODE:'0644')
    );
    foreach($cfg as $key=>$value) {
        db_add_key_value($key, $value);
    }

/**********************************************************
 *  - Adding field sec_anchor to settings table
 */
    echo '<h2>Step '.(++$stepID).' : Adding/Updating settings table</h2>';
    echo '<div class="content">';
    echo "<br />Adding sec_anchor and website_signature to settings table<br />";
    $cfg = array(
        'sec_anchor' => (defined('SEC_ANCHOR')?SEC_ANCHOR:'wb_'),
        'website_signature' => (defined('WEBSITE_SIGNATURE')?WEBSITE_SIGNATURE:'')
    );
    foreach($cfg as $key=>$value) {
        db_add_key_value($key, $value);
    }

/**********************************************************
 *  - Adding redirect timer to settings table
 */
echo "<br />Adding redirect timer to settings table<br />";
$cfg = array(
    'redirect_timer' => (defined('REDIRECT_TIMER')?REDIRECT_TIMER:'1500')
);
foreach($cfg as $key=>$value) {
    db_add_key_value($key, $value);
}

/**********************************************************
 *  - Adding rename_files_on_upload to settings table
 */
echo "<br />Updating rename_files_on_upload to settings table<br />";
$cfg = array(
    'rename_files_on_upload' => (defined(RENAME_FILES_ON_UPLOAD)?RENAME_FILES_ON_UPLOAD:'ph.*?,cgi,pl,pm,exe,com,bat,pif,cmd,src,asp,aspx,js')
);
db_add_key_value( 'rename_files_on_upload', $cfg['rename_files_on_upload']);

/**********************************************************
 *  - Adding mediasettings to settings table
 */
echo "<br />Adding mediasettings and debug to settings table<br />";

$cfg = array(
    'debug' => (defined('DEBUG')?DEBUG:'false'),
    'mediasettings' => (defined('MEDIASETTINGS') ?MEDIASETTINGS:''),
);

foreach($cfg as $key=>$value) {
    db_add_key_value($key, $value);
}

/**********************************************************
 *  - Set wysiwyg_editor to settings table
 */
echo "<br />Set wysiwyg_editor to ckeditor<br />";
    db_update_key_value('settings', 'wysiwyg_editor', 'ckeditor');

/**********************************************************
 *  - Adding fingerprint_with_ip_octets to settings table
 */
echo "<br />Adding fingerprint_with_ip_octets to settings table<br />";
$cfg = array(
    'sec_token_fingerprint' => (defined('SEC_TOKEN_FINGERPRINT') ?SEC_TOKEN_FINGERPRINT:'true'),
    'sec_token_netmask4'    => (defined('SEC_TOKEN_NETMASK4') ?SEC_TOKEN_NETMASK4:'24'),
    'sec_token_netmask6'    => (defined('SEC_TOKEN_NETMASK6') ?SEC_TOKEN_NETMASK6:'64'),
    'sec_token_life_time'   => (defined('SEC_TOKEN_LIFE_TIME') ?SEC_TOKEN_LIFE_TIME:'180'),
    'wbmailer_smtp_port'    => (defined('WBMAILER_SMTP_PORT') ?WBMAILER_SMTP_PORT:'25'),
    'wbmailer_smtp_secure'  => (defined('WBMAILER_SMTP_SECURE') ?WBMAILER_SMTP_SECURE:'TLS')
);
foreach($cfg as $key=>$value) {
    db_add_key_value($key, $value);
}

/**********************************************************
 *  - Add field "redirect_type" to table "mod_menu_link"
 */
echo "<br />Adding field redirect_type to mod_menu_link table<br />";
db_add_field('mod_menu_link', 'redirect_type', "INT NOT NULL DEFAULT '301' AFTER `target_page_id`");
echo '</div>';

/**********************************************************
 *  - Update search no results database filed to create
 *  valid XHTML if search is empty
 */
if (version_compare(WB_VERSION, '2.8', '<'))
{
    echo "<br />Updating database field `no_results` of search table: ";
    $search_no_results = addslashes('<tr><td><p>[TEXT_NO_RESULTS]</p></td></tr>');
    $sql  = 'UPDATE `'.TABLE_PREFIX.'search` ';
    $sql .= 'SET `value`=\''.$search_no_results.'\' ';
    $sql .= 'WHERE `name`=\'no_results\'';
    echo ($database->query($sql)) ? ' $OK<br />' : ' $FAIL<br />';
}
/* *****************************************************************************
 * - check for deprecated / never needed files
 */
    if(sizeof($filesRemove)) {
        echo '<h2>Step '.(++$stepID).': Remove deprecated and old files</h2>';
    }
    $searches = array(
        '[ROOT]',
        '[ACCOUNT]',
        '[ADMIN]',
        '[INCLUDE]',
        '[INSTALL]',
        '[FRAMEWORK]',
        '[MEDIA]',
        '[MODULES]',
        '[PAGES]',
        '[TEMP]',
        '[TEMPLATE]'
    );
    $replacements = array(
        '/',
        '/account/',
        '/'.substr(ADMIN_PATH, strlen(WB_PATH)+1).'/',
        '/include/',
        '/install/',
        '/framework/',
        MEDIA_DIRECTORY.'/',
        '/modules/',
        PAGES_DIRECTORY.'/',
        '/temp/',
        '/templates/'
    );

        $aMsg = array();
        array_walk(
            $filesRemove,
            function (&$sFile) use($searches, $replacements) {
                $sFile = str_replace( '\\', '/', WB_PATH.str_replace($searches, $replacements, $sFile) );
            }
        );
       foreach ( $filesRemove as $sFileToDelete ) {
            if (false !== ($aExistingFiles = glob(dirname($sFileToDelete).'/*', GLOB_MARK)) ) {
                if ( in_array($sFileToDelete, $aExistingFiles) ) {
                    if ( is_writable($sFileToDelete) && unlink($sFileToDelete) ) {
                        print '<strong>Remove  '.$sFileToDelete.'</strong>'." $OK<br />";
                    } else {
                        $aMsg[] = $sFileToDelete;
                    }
                }
            }
        }
        unset($aExistingFiles);
        if( sizeof($aMsg) )
        {
            $sWbPath = str_replace('\\', '/', WB_PATH );
            array_walk($aMsg, function(&$sMsg) use ($sWbPath) { $sMsg = str_replace($sWbPath, '', $sMsg); });
            $msg = implode('<br />', $aMsg).'<br />';
            $msg = '<br /><br />Following files are deprecated, outdated or a security risk and
                    can not be removed automatically.<br /><br />Please delete them
                    using FTP and restart upgrade-script!<br /><br />'.$msg.'<br />';
            status_msg($msg, 'error warning', 'div');
            echo '<p style="font-size:120%;"><strong>WARNING: The upgrade script failed ...</strong></p>';
            echo '<form action="'.$_SERVER['SCRIPT_NAME'].'">';
            echo '&nbsp;<input name="send" type="submit" value="Restart upgrade script" />';
            echo '</form>';
            echo '<br /><br /></div></body></html>';
            exit;
        }


/**********************************************************
 * - check for deprecated / never needed folder
 */
    if(sizeof($dirRemove)) {
        echo '<h2>Step  '.(++$stepID).': Remove deprecated and old folders</h2>';
        $searches = array(
            '[ADMIN]',
            '[INCLUDE]',
            '[MEDIA]',
            '[MODULES]',
            '[PAGES]',
            '[TEMPLATE]'
        );
        $replacements = array(
            '/'.substr(ADMIN_PATH, strlen(WB_PATH)+1).'/',
            '/include/',
            MEDIA_DIRECTORY.'/',
            '/modules/',
            PAGES_DIRECTORY.'/',
            '/templates/',
        );
        $msg = '';
        foreach( $dirRemove as $dir ) {
            $dir = str_replace($searches, $replacements, $dir);
            $dir = WB_PATH.'/'.$dir;
            if( is_dir( $dir )) {
            // try to delete dir
                if(!rm_full_dir($dir)) {
                // save in err-list, if failed
                    $msg .= $dir.'<br />';
                } else {
                        print '<strong>Remove  '.$dir.'</strong>'." $OK<br />";
                }
            }
        }
        if($msg != '') {
            $msg = '<br /><br />Following files are deprecated, outdated or a security risk and
                    can not be removed automatically.<br /><br />Please delete them
                    using FTP and restart upgrade-script!<br /><br />'.$msg.'<br />';
            status_msg($msg, 'error warning', 'div');
            echo '<p style="font-size:120%;"><strong>WARNING: The upgrade script failed ...</strong></p>';
            echo '<form action="'.$_SERVER['SCRIPT_NAME'].'">';
            echo '&nbsp;<input name="send" type="submit" value="Restart upgrade script" />';
            echo '</form>';
            echo '<br /><br /></div></body></html>';
            exit;
        }
    }

/**********************************************************
 * upgrade modules if newer version is available
    $aModuleList = array_intersect($aModuleDirList, $aModuleWhiteList);
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( in_array($sModulName, $aModuleWhiteList).'O) '.$sModulName.'=='.$aModuleWhiteList[$sModulName] ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
 */

    echo '<h2>Step '.(++$stepID).' : Checking all addons with a newer version (upgrade)</h2>';
    echo '<div class="content">';
    $aModuleDirList = glob(WB_PATH.'/modules/*', GLOB_ONLYDIR|GLOB_ONLYDIR );
    $i = $upgradeID = 0;
#    $aModuleWhiteList = array_flip($aModuleWhiteList);
    foreach($aModuleDirList as $sModul)
    {
        $sModulName = basename($sModul);
        $i++;
        if (in_array($sModulName, $aModuleWhiteList) && file_exists($sModul.'/upgrade.php'))
        {
            $currModulVersion = get_modul_version ($sModulName, false);
            $newModulVersion =  get_modul_version ($sModulName, true);
            if((version_compare($currModulVersion, $newModulVersion, '<' ) )) {
                require($sModul.'/upgrade.php');
                load_module($sModul);
                echo '<h5> '.sprintf("[%2s]", (++$upgradeID)).' : Upgrade module \''.$sModulName.'\' from version '.$currModulVersion.' to version'.$newModulVersion.'</h5>';
            } else {
                echo '<h5 style="color: #16702B"> '.sprintf("[%2s]", (++$upgradeID)).' : Module \''.$sModulName.'\' -  You already have the current version '.$currModulVersion.'</h5>';
            }
        } else {
            echo '<h5 style="color: #C26106"> '.sprintf("[%2s]", (++$upgradeID)).' : Unchecked Module \''.$sModulName.'\' is  not registered in /install/ModuleWhiteList</h5>';

        }
    }
    echo '</div>';
/**********************************************************
 *  - Reload all addons
 */

    echo '<h2>Step '.(++$stepID).' : Reload all addons database entry (no upgrade)</h2>';
    echo '<div class="content">';
    echo '<br />Modules will be reloaded<br />';
/*
*/
    ////delete modules
    $sql = 'DELETE FROM `'.TABLE_PREFIX.'addons` '
         . 'WHERE `type` = \'module\'';
    $database->query($sql);
    // Load all modules
    if( ($handle = opendir(WB_PATH.'/modules/')) ) {
        while(false !== ($file = readdir($handle))) {
            if($file != '' AND substr($file, 0, 1) != '.' AND $file != 'admin.php' AND $file != 'index.php') {
                load_module(WB_PATH.'/modules/'.$file );
               //     upgrade_module($file, true);
            }
        }
        closedir($handle);
    }
    ////delete templates
    //$database->query("DELETE FROM ".TABLE_PREFIX."addons WHERE type = 'template'");
    // Load all templates
    if( ($handle = opendir(WB_PATH.'/templates/')) ) {
        while(false !== ($file = readdir($handle))) {
            if($file != '' AND substr($file, 0, 1) != '.' AND $file != 'index.php') {
                load_template(WB_PATH.'/templates/'.$file);
            }
        }
        closedir($handle);
    }
    echo '<br />Templates reloaded<br />';

    ////delete languages
    //$database->query("DELETE FROM ".TABLE_PREFIX."addons WHERE type = 'language'");
    // Load all languages
    if( ($handle = opendir(WB_PATH.'/languages/')) ) {
        while(false !== ($file = readdir($handle))) {
            if($file != '' AND substr($file, 0, 1) != '.' AND $file != 'index.php') {
                load_language(WB_PATH.'/languages/'.$file);
            }
        }
        closedir($handle);
    }
    echo '<br />Languages reloaded<br />';

/**********************************************************
 *  - End of upgrade script
 */

// require(WB_PATH.'/framework/initialize.php');

    if(!defined('DEFAULT_THEME')) { define('DEFAULT_THEME', $DEFAULT_THEME); }
    if(!defined('THEME_PATH')) { define('THEME_PATH', WB_PATH.'/templates/'.DEFAULT_THEME);}
    if(!defined('THEME_URL')) { define('THEME_URL', WB_URL.'/templates/'.DEFAULT_THEME);}

    if(!defined('DEFAULT_TEMPLATE')) { define('DEFAULT_TEMPLATE', $DEFAULT_TEMPLATE); }
    if(!defined('TEMPLATE_PATH')) { define('TEMPLATE_PATH', WB_PATH.'/templates/'.DEFAULT_TEMPLATE);}
    if(!defined('TEMPLATE_DIR')) { define('TEMPLATE_DIR', WB_URL.'/templates/'.DEFAULT_TEMPLATE);}
/**********************************************************
 *  - Set Version to new Version
 */
    echo '<br />Reload all addons database entry (no upgrade)<br />';
    echo '</div>';
    echo '<h2>Step '.(++$stepID).' : Update WebsiteBaker version number to '.VERSION.' '.SP.' '.' Revision ['.REVISION.'] </h2>';
    // echo ($database->query("UPDATE `".TABLE_PREFIX."settings` SET `value`='".VERSION."' WHERE `name` = 'wb_version'")) ? " $OK<br />" : " $FAIL<br />";
    db_update_key_value('settings', 'wb_version', VERSION);
    db_update_key_value('settings', 'wb_revision', REVISION);
    db_update_key_value('settings', 'wb_sp', SP);

    status_msg('<h2>Congratulations: The upgrade script is finished ...</h2>', 'info', 'div');

    // show buttons to go to the backend or frontend
    echo '<br />';
    if(defined('WB_URL')) {
        echo '<form action="'.WB_URL.'/">';
        echo '&nbsp;<input type="submit" value="kick me to the Frontend" />';
        echo '</form>';
    }
    if(defined('ADMIN_URL')) {
        echo '<form action="'.ADMIN_URL.'/">';
        echo '&nbsp;<input type="submit" value="kick me to the Backend" />';
        echo '</form>';
    }

    echo '<br /><br /></div></body></html>';

<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: tool.php 1543 2011-12-14 00:13:54Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/tool.php $
 * @lastmodified    $Date: 2011-12-14 01:13:54 +0100 (Mi, 14. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_URL') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

$msg = array();
$sModulName = basename(__DIR__);
$ModuleRel = '/modules/'.basename(__DIR__).'/';
$ModuleUrl = WB_URL.'/modules/'.basename(__DIR__).'/';
$ModulePath = WB_PATH.'/modules/'.basename(__DIR__).'/';
$js_back = ADMIN_URL.'/admintools/tool.php';
$ToolUrl = ADMIN_URL.'/admintools/tool.php?tool=droplets';

// Load Language file
if(LANGUAGE_LOADED) {
    if(!file_exists($ModulePath.'languages/'.LANGUAGE.'.php')) {
        require_once($ModulePath.'languages/EN.php');
    } else {
        require_once($ModulePath.'languages/'.LANGUAGE.'.php');
    }
}

if( !$admin->get_permission($sModulName,'module' ) ) {
    $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
    exit();
}

// Get userid for showing admin only droplets or not
$loggedin_user = ($admin->ami_group_member('1') ? 1 : $admin->get_user_id() );
$loggedin_group = $admin->get_groups_id();
$admin_user = ( ($admin->get_home_folder() == '') && ($admin->ami_group_member('1') ) || ($loggedin_user == '1'));

// And... action
$admintool_url = ADMIN_URL .'/admintools/index.php';
//removes empty entries from the table so they will not be displayed
$sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_droplets` '
     . 'WHERE name = \'\' ';
if( !$database->query($sql) ) {
    $msg[] = $database->get_error();
}
// if import failed after installation, should be only 1 time
$sql = 'SELECT COUNT(`id`) FROM `'.TABLE_PREFIX.'mod_droplets` ';
if( !$database->get_one($sql) ) {
    include('install.php');
}
?><br />
<div id="openModal" class="modalDialog" draggable="true">
    <div>    <a href="#close" title="Close" class="close">X</a>
       <header class="modal-label"><h2>Droplet <?php echo $DR_TEXT['HELP']; ?></h2></header>
          <div class="modal-inner">
              <iframe  src="<?php echo $ModuleUrl; ?>readme/readme.html" style="width: 100%;"></iframe>
          </div>
        <footer class="modal-label">
<!--
            <a href="http://websitebaker.org/" title="external">WebsiteBaker</a> is released under the
            <a href="http://www.gnu.org/licenses/gpl.html" title="WebsiteBaker is released under the GNU General Public License">GNU General Public License</a>
-->
        </footer>
    </div>
</div>

<div class="droplets" id="cb-droplets" >
<form action="<?php echo $ModuleUrl; ?>index.php" method="post" name="droplets_form"  enctype="multipart/form-data" >
    <?php echo $admin->getFTAN(); ?>
    <table class="droplets">
    <tr>
        <td >
            <button class="btn" type="submit" name="command" value="add_droplet"><?php echo $TEXT['ADD'].' '.$DR_TEXT['DROPLETS']; ?></button>
            <a class="btn" href="#import"><?php echo $DR_TEXT['IMPORT']; ?></a>
       </td>
        <td style="float: right;">
            <button class="btn" type="button" onclick="window.location='#openModal'" class="modal-header_btn modal-trigger btn-fixed">Droplet <?php echo $DR_TEXT['HELP']; ?></button>
            <button class="btn" type="submit" name="command" value="backup_droplets"><?php echo $DR_TEXT['BACKUP']; ?></button>
        </td>
    </tr>
    </table>
    <br />

<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$DR_TEXT['DROPLETS']; ?></h2>
<?php
$sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` ';
if (!$admin_user) {
    $sql .= 'WHERE `admin_view` <> 1 ';
}
$sql .= 'ORDER BY `modified_when` DESC';
$oDroplets = $database->query($sql);
$num_droplets = $oDroplets->numRows();
if($num_droplets > 0) {
//print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />'; 
//print_r( $oDroplets ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die(); 
?>
<script src="<?php echo $ModuleUrl; ?>js/wz_tooltip.js"></script>
<script src="<?php echo $ModuleUrl; ?>js/tip_balloon.js"></script>    <table class="droplets_data" >
    <thead>
        <tr>
            <th style="width: 3%;" >
      <label>
          <input name="select_all" id="select_all" type="checkbox" value="1"  />
      </label>
            </th>
            <th style="width: 3%;" ></th>
            <th style="width: 3%;" ></th>
            <th style="width: 3%;" ></th>
            <th style="width: 21%;"><?php echo $TEXT['NAME']; ?></th>
            <th style="width: 65%;"><?php echo $TEXT['DESCRIPTION']; ?></th>
            <th style="width: 4%;"><?php echo $TEXT['ACTIVE']; ?></th>
            <th style="width: 3%;"></th>
        </tr>
    </thead>
    <tbody>
    <?php
    while($aDroplets = $oDroplets->fetchRow(MYSQLI_ASSOC)) {
        $aComment =  array();
//        $aDroplets['code'] = 'return false;';
        $modified_user = $TEXT['UNKNOWN'];
        $modified_userid = 0;

        $sql = 'SELECT `display_name`,`username`, `user_id` FROM `'.TABLE_PREFIX.'users` '
        .'WHERE `user_id` = '.$aDroplets['modified_by'];
        $get_modified_user = $database->query($sql);
        if($get_modified_user->numRows() > 0) {
            $fetch_modified_user = $get_modified_user->fetchRow(MYSQLI_ASSOC);
            $modified_user = $fetch_modified_user['username'];
            $modified_userid = $fetch_modified_user['user_id'];
        }

        $iDropletIdKey = $aDroplets['id'];
        $iDropletIdKey = $admin->getIDKEY($aDroplets['id']);
        $comments = '';
//        $comments = str_replace(array("\r\n", "\n", "\r"), '<br >', $aDroplets['comments']);

        if (!strpos($comments,"[[")) $comments = "Use: [[".$aDroplets['name']."]]<br />".$comments;
        $comments = str_replace(array("[[", "]]"), array('<b>[[',']]</b>'), $comments);
        $valid_code = true;
        $valid_code = check_syntax($aDroplets['code']);
        if (!$valid_code === true) $comments = '<span color=\'red\'><strong>'.$DR_TEXT['INVALIDCODE'].'</strong></span><br />'.$comments;
        $unique_droplet = check_unique ($aDroplets['name']);
        if ($unique_droplet === false ) {$comments = '<span color=\'red\'><strong>'.$DR_TEXT['NOTUNIQUE'].'</strong></span><br />'.$comments;}
//        $comments = '<span>'.$comments.'</span>';
?><tr >
            <td >
               <input type="checkbox" name="cb[<?php echo $aDroplets['id']; ?>]" id="L<?php echo $aDroplets['id']; ?>cb" value="<?php echo $aDroplets['name']; ?>" />
            </td>
            <td >
                <button name="command" type="submit" class="noButton"  value="copy_droplets?droplet_id=<?php echo $iDropletIdKey; ?>" title="<?php echo $DR_TEXT['COPY']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/plus_16.png"  alt="Modify" />
                </button>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton"  value="modify_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/modify_16.png"  alt="Modify" />
                </button>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton"  value="modify_droplet?droplet_id=<?php echo $iDropletIdKey; ?>">
                        <?php if ($valid_code && $unique_droplet) { ?><img src="<?php echo $ModuleUrl; ?>img/droplet.png" alt=""/>
                        <?php } else {  ?><img src="<?php echo $ModuleUrl; ?>img/invalid.gif"  alt=""/><?php }  ?>
                </button>
            </td>
            <td onmouseover="TagToTip('tooltip_<?php echo $aDroplets['id']; ?>', BGCOLOR, '#F2F0A3', BALLOON, true, FONTSIZE, '10pt', HEIGHT,'0', BALLOONIMGPATH, '<?php echo $ModuleUrl; ?>img/tip_balloon/', BALLOONIMGEXT, 'gif' )" onmouseout="UnTip()">
                <button  class=" noButton" name="command" type="submit" class="noButton" value="modify_droplet?droplet_id=<?php echo $iDropletIdKey; ?>">
                    <?php echo $aDroplets['name']; ?>
                <span id="tooltip_<?php echo $aDroplets['id']; ?>"><?php echo trim($comments); ?></span></button>
            </td>
            <td onmouseover="TagToTip('tooltip_<?php echo $aDroplets['id']; ?>', BGCOLOR, '#F2F0A3', BALLOON, true, FONTSIZE, '10pt', BALLOONIMGPATH, '<?php echo $ModuleUrl; ?>img/tip_balloon/', BALLOONIMGEXT, 'gif' )" onmouseout="UnTip()">
                <?php echo substr($aDroplets['description'],0,90); ?>
            </td>
            <td >
                <b><?php if($aDroplets['active'] == 1){ echo '<span style="color: green;">'. $TEXT['YES']. '</span>'; } else { echo '<span style="color: red;">'.$TEXT['NO'].'</span>';  } ?></b>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton" style="width: auto;" value="delete_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" title="<?php echo $TEXT['DELETE']; ?>">
                    <img src="<?php echo THEME_URL; ?>/images/delete_16.png" alt="X" />
                </button>
            </td>
        </tr>
<?php 
//unset($aDroplets);
} ?>
      </tbody>
    </table>
<?php
$sBackupDir = $ModuleRel.'data/archiv/';
$aZipFiles = glob(WB_PATH.$ModuleRel.'data/archiv/*.zip', GLOB_NOSORT); 
?>
        <div class="droplet-import" id="import">
            <span ><?php echo $DR_TEXT['RESTORE']; ?></span>
            <span style="text-align: left; padding: 0.525em 0;">
                <select size="1" name="zipFiles" style="width: 30%;" >
                    <option style=" padding: 0.225em 0.455em;" value=""><?php echo $TEXT['PLEASE_SELECT']; ?></option>
<?php
foreach( $aZipFiles as $files ) {
      $value =  basename($files);
      $files = str_replace(WB_PATH, '', $files );
 ?>
                  <option style=" padding: 0.225em 0.455em;" value="<?php echo $files; ?>"><?php echo $value; ?></option>
<?php } ?></select>
                <button class="btn" type="submit" name="command" value="import_droplets"><?php echo $DR_TEXT['IMPORT']; ?></button>
                <button class="btn" type="submit" name="command" value="delete_archiv"><?php echo $TEXT['DELETE']; ?></button>
            </span>
          <div> 
              <span style="margin-left: 21%;"> </span>
              <span style="text-align: left; padding: 0.525em 0; display: inline-block; width: 63.5%;">
                  <input name="zipFiles" type="file" accept=".zip">
                  <button class="btn" name="command" value="import_droplets" type="submit"><?php echo $Droplet_Message['GENERIC_LOCAL_UPLOAD']; ?></button>
              </span>
          </div>
        </div>
</form>
</div>
<?php
}

function check_syntax($code) {
    return @eval('return true;' . $code);
}

function check_unique($name) {
    global $database;
    $retVal = 0;
    $sql = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_droplets` ';
    $sql .= 'WHERE `name` = \''.$name.'\'';
    $retVal = intval($database->get_one($sql));
    return ($retVal == 1);
}

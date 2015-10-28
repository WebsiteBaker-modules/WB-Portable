<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @copyright       WebsiteBaker Org. e.V.
 * @author          Christian Sommer
 * @author          Dietmar Wöllbrink
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: tool.php 1520 2011-11-09 00:12:37Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/output_filter/tool.php $
 * @lastmodified    $Date: 2011-11-09 01:12:37 +0100 (Mi, 09. Nov 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) { throw new RuntimeException('Illegal access'); }
/* -------------------------------------------------------- */

    $modPath = str_replace('\\', '/', dirname(__FILE__)).'/';
    $sModulName = basename(__DIR__);
    $js_back = ADMIN_URL.'/admintools/tool.php';
    $ToolUrl = ADMIN_URL.'/admintools/tool.php?tool='.$sModulName;
    if( !$admin->get_permission($sModulName,'module' ) ) {
        $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
    }

    $msgTxt = '';
    $msgCls = 'msg-box';
// include the modules language definitions
    if(is_readable($modPath.'languages/EN.php')) {
        require_once($modPath.'languages/EN.php');
    }
    if(is_readable($modPath.'languages/'.LANGUAGE .'.php')) {
        require_once($modPath.'languages/'.LANGUAGE .'.php');
    }
    // preset settings as default
    $sDefaults = array(
        'sys_rel'         => false,
        'opf'             => false,
        'email_filter'    => false,
        'mailto_filter'   => false,
        'at_replacement'  => '(at)',
        'dot_replacement' => '(dot)'
    );
    $data = $sDefaults;
// check if data was submitted
    if($doSave) {
        if ($admin->checkFTAN()) {
    // take over post - arguments
            $data['sys_rel']         = (bool)(isset($_POST['sys_rel'])
                                              ? intval($_POST['sys_rel'])
                                              : $sDefaults['sys_rel']);
            $data['opf']             = (bool)(isset($_POST['opf'])
                                              ? intval($_POST['opf'])
                                              : $sDefaults['opf']);
            $data['email_filter']    = (bool)(isset($_POST['email_filter'])
                                              ? intval($_POST['email_filter'])
                                              : $sDefaults['email_filter']);
            $data['mailto_filter']   = (bool)(isset($_POST['mailto_filter'])
                                              ? intval($_POST['mailto_filter'])
                                              : $sDefaults['mailto_filter']);
            $data['at_replacement']  = (isset($_POST['at_replacement'])
                                       ? trim(strip_tags($_POST['at_replacement']))
                                       : $sDefaults['at_replacement']) ?: $sDefaults['at_replacement'];
            $data['dot_replacement'] = (isset($_POST['dot_replacement'])
                                       ? trim(strip_tags($_POST['dot_replacement']))
                                       : $sDefaults['dot_replacement']) ?: $sDefaults['dot_replacement'];

            $sNameValPairs = '';
            foreach ($data as $index => $val) {
                $sNameValPairs .= ', (\''.$index.'\', \''.$database->escapeString($val).'\')';
            }
            $sValues = ltrim($sNameValPairs, ', ');
            $sql = 'REPLACE INTO `'.TABLE_PREFIX.'mod_output_filter` (`name`, `value`) '
                 . 'VALUES '.$sValues;
            if ($database->query($sql)) {
            //anything ok
                $msgTxt = $MESSAGE['RECORD_MODIFIED_SAVED'];
                $msgCls = 'msg-box';
            }else {
            // database error
                $msgTxt = $MESSAGE['RECORD_MODIFIED_FAILED'];
                $msgCls = 'error-box';
            }
        } else {
        // FTAN error
            $msgTxt = $MESSAGE['GENERIC_SECURITY_ACCESS'];
            $msgCls = 'error-box';
        }
    }else {
        // read settings from the database to show
        if (is_readable(__DIR__.'/OutputFilterApi.php')) {
            if (!function_exists('getOutputFilterSettings')) {
                require(__DIR__.'/OutputFilterApi.php');
            }
            $data = getOutputFilterSettings();
        }
    }
    // write out header if needed
    if(!$admin_header) { $admin->print_header(); }
    if( $msgTxt != '') {
    // write message box if needed
        echo '<div class="'.$msgCls.'">'.$msgTxt.'</div>';
    }
?>
<h2><?php echo $MOD_MAIL_FILTER['HEADING']; ?></h2>
<p><?php echo $MOD_MAIL_FILTER['HOWTO']; ?></p>
<form name="store_settings" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
    <?php echo $admin->getFTAN(); ?>
    <input type="hidden" name="action" value="save" />
    <table  class="row_a">
    <tr><td colspan="2"><strong><?php echo $MOD_MAIL_FILTER['BASIC_CONF'];?>:</strong></td></tr>
    <tr>
        <td width="35%"><?php echo $MOD_MAIL_FILTER['SYS_REL'];?>:</td>
        <td>
            <input type="radio" <?php echo $data['sys_rel'] ? 'checked="checked"' :'';?>
                name="sys_rel" value="1"><?php echo $MOD_MAIL_FILTER['ENABLED'];?>
            <input type="radio" <?php echo (!$data['sys_rel']) ? 'checked="checked"' :'';?>
                name="sys_rel" value="0"><?php echo $MOD_MAIL_FILTER['DISABLED'];?>
        </td>
    </tr>
    <tr>
        <td width="35%"><?php echo $MOD_MAIL_FILTER['opf'];?>:</td>
        <td>
            <input type="radio" <?php echo $data['opf'] ?'checked="checked"' :'';?>
                name="opf" value="1"><?php echo $MOD_MAIL_FILTER['ENABLED'];?>
            <input type="radio" <?php echo (!$data['opf']) ?'checked="checked"' :'';?>
                name="opf" value="0"><?php echo $MOD_MAIL_FILTER['DISABLED'];?>
        </td>
    </tr>
    <tr>
        <td width="35%"><?php echo $MOD_MAIL_FILTER['EMAIL_FILTER'];?>:</td>
        <td>
            <input type="radio" <?php echo $data['email_filter'] ?'checked="checked"' :'';?>
                name="email_filter" value="1"><?php echo $MOD_MAIL_FILTER['ENABLED'];?>
            <input type="radio" <?php echo (!$data['email_filter']) ?'checked="checked"' :'';?>
                name="email_filter" value="0"><?php echo $MOD_MAIL_FILTER['DISABLED'];?>
        </td>
    </tr>
    <tr>
        <td><?php echo $MOD_MAIL_FILTER['MAILTO_FILTER'];?>:</td>
        <td>
            <input type="radio" <?php echo $data['mailto_filter'] ?'checked="checked"' :'';?>
                name="mailto_filter" value="1"><?php echo $MOD_MAIL_FILTER['ENABLED'];?>
            <input type="radio" <?php echo (!$data['mailto_filter']) ?'checked="checked"' :'';?>
                name="mailto_filter" value="0"><?php echo $MOD_MAIL_FILTER['DISABLED'];?>
        </td>
    </tr>
    <tr><td colspan="2"><br /><strong><?php echo $MOD_MAIL_FILTER['REPLACEMENT_CONF'];?>:</strong></td></tr>
    <tr>
        <td><?php echo $MOD_MAIL_FILTER['AT_REPLACEMENT'];?>:</td>
        <td><input type="text" style="width: 160px" value="<?php echo $data['at_replacement'];?>"
            name="at_replacement"/></td>
    </tr>
    <tr>
        <td><?php echo $MOD_MAIL_FILTER['DOT_REPLACEMENT'];?>:</td>
        <td><input type="text" style="width: 160px" value="<?php echo $data['dot_replacement'];?>"
            name="dot_replacement"/></td>
    </tr>
    </table>
    <input type="submit" style="margin-top:10px; width:140px;" value="<?php echo $TEXT['SAVE']; ?>" />
</form>

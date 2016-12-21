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
 * @version         $Id: signup_form.php 1599 2012-02-06 15:59:24Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/account/signup_form.php $
 * @lastmodified    $Date: 2012-02-06 16:59:24 +0100 (Mo, 06. Feb 2012) $
 *
 */

// Must include code to stop this file being access directly
if(defined('WB_PATH') == false) { die("Cannot access this file directly"); }

$sCallingScript = WB_URL;
$redirect_url = ((isset($_SESSION['HTTP_REFERER']) && $_SESSION['HTTP_REFERER'] != '') ? $_SESSION['HTTP_REFERER'] : $sCallingScript );
$redirect_url = ( isset($redirect) && ($redirect!='') ? $redirect : $redirect_url);
require_once(WB_PATH.'/include/captcha/captcha.php');
/**
 * echo '<style type="text/css">';
 * include(WB_PATH .'/account/frontend.css');
 * echo "\n</style>\n";
 */

$error = array();
$success = array();

$sAddonName = basename(__DIR__);
if(file_exists(WB_PATH .'/'.$sAddonName.'/languages/EN.php')) {require(WB_PATH .'/'.$sAddonName.'/languages/EN.php');}
if(file_exists(WB_PATH .'/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {require(WB_PATH .'/'.$sAddonName.'/languages/'.LANGUAGE .'.php');}

if(isset($_POST['action']) && $_POST['action']=='send') {
    require(dirname(__FILE__).'/signup2.php');
}
if(sizeof($success)>0){
//$_SESSION['display_form'] = false;
?>
<p class="mod_preferences_success">
<?php
   foreach($success AS $value){ ?>
    <?php echo nl2br($value); ?>
<?php } ?>
</p>
<?php }

if($_SESSION['display_form']){
    if(sizeof($error)>0){
?>
    <p class="mod_preferences_error">
<?php
        echo implode('<br />', $error);
?>
    </p>
<?php }?>

    <div style="margin: 1em auto;">
        <button type="button" value="cancel" onclick="window.location = '<?php echo $redirect_url; ?>';"><?php print $TEXT['CANCEL'] ?></button>
    </div>
<?php if(!isset($display_form) || $display_form != false) { ?>
    <h1>&nbsp;<?php echo $TEXT['SIGNUP']; ?></h1>
    <form name="user" action="#" method="post" class="account">
        <?php echo $wb->getFTAN(); ?>
        <input type="hidden" name="action" value="send" />
        <input type="hidden" name="redirect" value="<?php echo $redirect_url; ?>" />
        <?php if(ENABLED_ASP) { // add some honeypot-fields
        ?>
        <div style="display:none;">
        <input type="hidden" name="submitted_when" value="<?php $t=time(); echo $t; $_SESSION['submitted_when']=$t; ?>" />
        <p class="nixhier">
        email-address:
        <label for="email-address">Leave this field email-address blank:</label>
        <input id="email-address" name="email-address" size="60" value="" /><br />
        username (id):
        <label for="name">Leave this field name blank:</label>
        <input id="name" name="name" size="60" value="" /><br />
        Full Name:
        <label for="full_name">Leave this field full_name blank:</label>
        <input id="full_name" name="full_name" size="60" value="" /><br />
        </p>
        <?php } ?>
        </div>
        <table class="account">
            <tbody>
                <tr>
                    <td style="width: 180px;"><?php echo $TEXT['USERNAME']; ?>:</td>
                    <td class="value_input">
                        <input type="text" name="username" maxlength="30" style="width:300px;"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $TEXT['DISPLAY_NAME']; ?>:</td>
                    <td class="value_input">
                        <input type="text" name="display_name" maxlength="255" style="width:300px;" />
                        <label> (<?php echo $TEXT['FULL_NAME']; ?>)</label>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $TEXT['EMAIL']; ?>:</td>
                    <td class="value_input">
                        <input type="text" name="email" maxlength="255" style="width:300px;"/>
                    </td>
                </tr>
<?php
// Captcha
    if(ENABLED_CAPTCHA) {
?>
                <tr>
                    <td class="field_title"><?php echo $TEXT['VERIFICATION']; ?>:</td>
                    <td><?php call_captcha(); ?></td>
                </tr>
<?php
    }
?>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="submit" name="submit" value="<?php echo $TEXT['SIGNUP']; ?>" />
                        <input type="reset" name="reset" value="<?php echo $TEXT['RESET']; ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <br />
    &nbsp;
<?php
    }
}

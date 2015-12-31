<?php
/**
 *
 * @category        modules
 * @package         menu_link
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: view.php 1420 2011-01-26 17:43:56Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/wysiwyg/modify.php $
 * @lastmodified    $Date: 2011-01-11 20:29:52 +0100 (Di, 11 Jan 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

// check if module language file exists for the language set by the user (e.g. DE, EN)
if(!file_exists(WB_PATH .'/modules/menu_link/languages/'.LANGUAGE .'.php')) {
    // no module language file exists for the language set by the user, include default module language file EN.php
    require_once(WB_PATH .'/modules/menu_link/languages/EN.php');
} else {
    // a module language file exists for the language defined by the user, load it
    require_once(WB_PATH .'/modules/menu_link/languages/'.LANGUAGE .'.php');
}

?>
<a href="<?php echo WB_URL; ?>">
<?php echo $MOD_MENU_LINK['TEXT']; ?>
</a>

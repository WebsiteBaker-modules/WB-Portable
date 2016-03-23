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
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}

// redirect menu-link
$this_page_id = PAGE_ID;

$php43 = version_compare(phpversion(), '4.3', '>=');

$sql  = 'SELECT `module`, `block` FROM `'.TABLE_PREFIX.'sections` ';
$sql .= 'WHERE `page_id` = '.(int)$this_page_id.' AND `module` = "menu_link"';
$query_this_module = $database->query($sql);
if($query_this_module->numRows() == 1)  // This is a menu_link. Get link of target-page and redirect
{
    // get target_page_id
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_menu_link` WHERE `page_id` = '.(int)$this_page_id;
    $query_tpid = $database->query($sql);
    if($query_tpid->numRows() == 1)
    {
        $res = $query_tpid->fetchRow();
        $target_page_id = $res['target_page_id'];
        $redirect_type = $res['redirect_type'];
        $anchor = ($res['anchor'] != '0' ? '#'.(string)$res['anchor'] : '');
        $extern = $res['extern'];
        // set redirect-type
        if($redirect_type == 301)
        {
            if($php43)
            {
                @header('HTTP/1.1 301 Moved Permanently', TRUE, 301);
            }
            else
            {
                @header('HTTP/1.1 301 Moved Permanently');
            }
        }
        if($target_page_id == -1)
        {
            if($extern != '')
            {
                $target_url = $extern.$anchor;
                @header('Location: '.$target_url);
                exit;
            }
        }
        else
        {
            // get link of target-page
            $sql  = 'SELECT `link` FROM `'.TABLE_PREFIX.'pages` WHERE `page_id` = '.$target_page_id;
            $target_page_link = $database->get_one($sql);
            if($target_page_link != null)
            {
                $target_url = WB_URL.PAGES_DIRECTORY.$target_page_link.PAGE_EXTENSION.$anchor;
                @header('Location: '.$target_url);
                exit;
            }
        }
    }
} else {

?>

<a href="<?php echo WB_URL; ?>">
<?php echo $MOD_MENU_LINK['TEXT']; ?>
</a>
<?php }

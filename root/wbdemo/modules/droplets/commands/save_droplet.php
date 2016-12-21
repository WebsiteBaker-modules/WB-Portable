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
 * @version         $Id: save_droplet.php 16 2016-09-13 20:52:49Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/commands/save_droplet.php $
 * @lastmodified    $Date: 2016-09-13 22:52:49 +0200 (Di, 13. Sep 2016) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
        if ( !class_exists('admin', false) ) { require($oReg->AppPath.'/framework/class.admin.php'); }
        // Include WB admin wrapper script
        $oApp = new admin('admintools', 'admintools',false);
        /* later
        if (!$oApp->checkFTAN()) {
        //    $oApp->print_header();
            $oApp->print_error('FTAN_DROPLET::'. $MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
        }
        */
        if (($droplet_id === false)) {
         //   $oApp->print_header();
            $oApp->print_error('IDKEY_DROPLET::'. $MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
        }
        // Validate all fields
        if( ($oApp->get_post('title') == '') && ($droplet_id==0) ) {
            $oApp->print_error($MESSAGE['GENERIC_FILL_IN_ALL'].' ( Droplet Name )', $ToolUrl );
        } else {
            $title = $oApp->StripCodeFromText($oApp->get_post('title'));
            $active = (int) $oApp->get_post('active');
            $oApp_view = (int) $oApp->get_post('admin_view');
            $oApp_edit = (int) $oApp->get_post('admin_edit');
            $show_wysiwyg = (int) $oApp->get_post('show_wysiwyg');
            $description = $oApp->get_post('description');
            $aForbiddenTags = array('<?php', '?>' , '<?');
            $content = str_replace($aForbiddenTags, '', $_POST['savecontent']);
            $comments = trim(($oApp->get_post('comments')));
            $modified_when = time();
            $modified_by = (int) $oApp->get_user_id();
        }
        $sqlBody = ''
                . '`active` = '.(int)$active.', '
                . '`admin_view` = '.(int)$oApp_view.', '
                . '`admin_edit` = '.(int)$oApp_edit.', '
                . '`show_wysiwyg` = '.(int)$show_wysiwyg.', '
                . '`description` = \''.$oDb->escapeString($description).'\', '
                . '`code` = \''.$oDb->escapeString($content).'\', '
                . '`comments` = \''.$oDb->escapeString($comments).'\', '
                . '`modified_when` = '.(int)$modified_when.', '
                . '`modified_by` = '.(int)$modified_by.' ';
        
        if ($droplet_id == 0){
            $title = getUniqueName($oDb, $title);
            $sql  = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets` SET '
                  . '`name` = \''.$oDb->escapeString($title).'\', ';
            $sqlWhere  = '';
            if (!$oDb->query($sql.$sqlBody)) {
                
            }
        } else {
            $sql = 'UPDATE `'.TABLE_PREFIX.'mod_droplets` SET ';
            $sqlWhere  = 'WHERE `id` = '.(int)$droplet_id;
            $oDb->query($sql.$sqlBody.$sqlWhere);}
        
        // Check if there is a db error, otherwise say successful
        if($oDb->is_error()) {
            msgQueue::add($oDb->get_error());
        } else {
            msgQueue::add( $TEXT['SUCCESS'], true );
        }
}


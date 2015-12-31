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
 * @version         $Id: delete_droplet.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/delete_droplet.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Get id
 $droplet_id = intval($admin->checkIDKEY($droplet_id, false, ''));
if (!$droplet_id) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl);
    exit();
}

if( !$admin->checkFTAN() ){
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
    exit();
}

if( !isset( $aRequestVars['DropletsToDelete'])  ) {
    
    $sDropletsToDelete = ( isset($droplet_id) && !isset( $aRequestVars['cb']) ? $droplet_id : '' );
    $iDELETED = (isset($droplet_id) ? 1 : 0 );
    if( isset( $aRequestVars['cb'])  ) {
        $aRequestVars['cb'] = array_flip(  $aRequestVars['cb'] );
        $aRequestVars['cb'] = array_unique($aRequestVars['cb'], SORT_NUMERIC);
        $iDELETED = sizeof( $aRequestVars['cb'] );
        $sDropletsToDelete = ( isset($droplet_id) ? implode(',',$aRequestVars['cb'] ) : '' );
    }
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` IN ('.$sDropletsToDelete.') ';
    $inDroplets = '';
    if ( $oRes = $database->query($sql)) {
        while( $aRow = $oRes->fetchRow( MYSQLI_ASSOC ) ) {
          $inDroplets .= $aRow['name'].', '; 
        }
    }

?>
<div class="droplets-delete" style="height: 20.525em;" >
    <h4 style="margin: 0; border-bottom: 1px solid #DDD; padding-bottom: 5px;">
        <a href="<?php echo $admintool_link;?>" title="<?php echo $HEADING['ADMINISTRATION_TOOLS']; ?>"><?php echo $HEADING['ADMINISTRATION_TOOLS']; ?></a>
        »
        <a href="<?php echo $ToolUrl;?>" title="<?php echo $sOverviewDroplets ?>" alt="<?php echo $sOverviewDroplets ?>">Droplets</a>
    </h4>

  <div id="droplets-delete" class="modal-Dialog" draggable="true">
    <form action="<?php echo $ModuleUrl.'index.php'; ?>" method="post">
          <input type="hidden" name="DropletsToDelete" value="<?php echo $sDropletsToDelete; ?>" />
          <?php echo $admin->getFTAN(); ?>
          <div id="customConfirm" style="display: block;">
               <a href="#close" title="Close" class="close" onclick="window.location='<?php echo $ToolUrl; ?>';">X</a>
              <header class=" modal-label"><?php echo $Droplet_Message['DELETE_DROPLETS']; ?></header>
              <div class="body">
                  <h3><?php echo $Droplet_Message['CONFIRM_DROPLET_DELETING']; ?></h3>
                  <p><?php echo rtrim($inDroplets, ', '); ?></p>
              </div>
              <div class="footer">
                  <button name="command" type="submit" value="delete_droplet?droplet_id=<?php echo $admin->getIDKEY($droplet_id); ?>" class="confirm"><?php echo $TEXT['DELETE']; ?></button>
                  <button name="cancel" class="cancel" type="button" onclick="window.location='<?php echo $ToolUrl; ?>';"><?php echo $TEXT['CANCEL']; ?></button>
              </div>
          </div>
    </form>
</div>
<?php
} elseif ( !isset($aRequestVars['cancel']) ) {
    $sDropletsToDelete = $aRequestVars['DropletsToDelete'];
    $iDELETED = sizeof( explode(',', $sDropletsToDelete) );
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` IN ('.$sDropletsToDelete.') ';

    // Delete droplet
    $database->query($sql);
    
    // Check if there is a db error, otherwise say successful
    if($database->is_error()) {
        msgQueue::add( $database->get_error().'<br />'.$sql );
    } else {
        msgQueue::add( sprintf("%'.02d", $iDELETED ).'  '.$DR_TEXT['DROPLETS_DELETED'], true );
    }
} else { /* do nothing */}
?></div><script >
<!--
domReady(function() {
    LoadOnFly('head', WB_URL+"<?php echo $ModuleRel.$sStyleFolder; ?>css/customAlert.css");
});
-->
</script>
<?php

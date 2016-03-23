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
 * @version         $Id: backup_droplets.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/backup_droplets.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
if( !$admin->checkFTAN() ){
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
    exit();
}

?><h4 style="margin: 0; border-bottom: 1px solid #DDD; padding-bottom: 5px;">
    <a href="<?php echo $admintool_link;?>" title="<?php echo $HEADING['ADMINISTRATION_TOOLS']; ?>"><?php echo $HEADING['ADMINISTRATION_TOOLS']; ?></a>
    »
    <a href="<?php echo $ToolUrl;?>" title="<?php echo $sOverviewDroplets ?>" alt="<?php echo $sOverviewDroplets ?>">Droplets</a>
</h4>

<?php
            if( isset( $_FILES['zipFiles'] ) && !$_FILES['zipFiles']['error']) {
                $aRequestVars['uploads']  = $_FILES['zipFiles'];
                $sArchiveFile = $_FILES['zipFiles']['tmp_name'];

                move_uploaded_file (
                     $_FILES['zipFiles']['tmp_name'] ,
                     WB_PATH.'/temp/'. $_FILES['zipFiles']['name'] 
                );
                $sArchiveFile = ( WB_PATH.'/temp/'. $_FILES['zipFiles']['name'] );
            } else {
                $sArchiveFile = ( WB_PATH.$aRequestVars['zipFiles']);
            }

if ( !is_readable( $sArchiveFile )  ) {
    msgQueue::add( $Droplet_Message['GENERIC_MISSING_ARCHIVE_FILE'] );
} else {
?><br />

<div class="droplets">
<form action="<?php echo $ModuleUrl; ?>index.php" method="post" name="droplets_form" >
    <?php echo $admin->getFTAN(); ?>
<!--
            <a class="btn" href="#import"><?php echo $DR_TEXT['IMPORT']; ?></a>
-->
    <table class="droplets">
    <tr>
        <td colspan="2" >
            <h2><?php echo basename($sArchiveFile); ?></h2>
       </td>
    </tr>
    <tr>
        <td style="width: 80%;">
            <div class="success"><?php echo $Droplet_Import['ARCHIV_LOADED']; ?></div>
        </td>
        <td style="float: right; vertical-align: middle;">
            <button class="btn" name="command" value="restore_droplets" type="submit"><?php echo $TEXT['EXECUTE']; ?></button>
            <button class="btn" type="button" onclick="window.location = '<?php echo $ToolUrl; ?>';"><?php echo $TEXT['CANCEL']; ?></button>
        </td>
    </tr>
    </table>
    <br />
<div class="cb-import" id="cb-droplets" >
<?php if ( is_readable( $sArchiveFile ) ) { 

    if( !class_exists('PclZip',false) ) { require( WB_PATH.'/include/pclzip/pclzip.lib.php'); }
    $oArchive = new PclZip( $sArchiveFile );
    $aFilesInArchiv = $oArchive->listContent();
    if ($aFilesInArchiv == 0) {
        msgQueue::add( $Droplet_Message['GENERIC_MISSING_ARCHIVE_FILE'] );
    } else {
?><table class="droplets_import" style="margin-bottom: 1.225em;">
  <thead>
    <tr>
      <th  style="width: 3%;">
          <label>
              <input name="select_all" id="select_all" type="checkbox" value="1"  />
          </label>
      </th>
      <th style="width: 3%;"></th>
      <th style="width: 3%;"></th>
      <th style="width: 30%;"><?php echo $Droplet_Header['FILENAME']; ?></th>
      <th style="width: 8%;text-align: right;"><?php echo $Droplet_Header['SIZE']; ?></th>
      <th style="width: 12%;text-align: right;padding-right: 0.525em;"><?php echo $Droplet_Header['DATE']; ?></th>
    </tr>
  </thead>
  <tbody>
<?php 
foreach( $aFilesInArchiv as $key=>$value ) {
?>
    <tr>
      <td style="text-align: center;">
         <input type="checkbox" name="restore_id[<?php echo $value['index']; ?>]" id="L<?php echo $value['index']; ?>cb" value="<?php echo $value['index']; ?>" />
      </td>
      <td style="text-align: center; font-weight: normal;"><?php echo $value['index']; ?></td>
      <td style="text-align: center;">
      
      <?php if ( $value['folder'] ) { ?><img src="<?php echo THEME_URL; ?>/images/folder_16.png" alt=""/>
      <?php } ?>
      </td>
      <td style="text-align: left;"><?php echo $value['filename']; ?></td>
      <td style="text-align: right;"><?php echo $value['size']; ?> Byte(s)</td>
      <td style="text-align: right;"><?php echo date(DATE_FORMAT, $value['mtime']); ?></td>
    </tr>
<?php
}
?>
    <tr id="import">
      <td colspan="6">
          <?php
echo $admin->getFTAN();
?>
          <input name="ArchiveFile" type="hidden" value="<?php echo $sArchiveFile; ?>" />
      </td>
    </tr>
  </tbody>
</table>
    <button  class="btn" name="command" value="restore_droplets" type="submit"><?php echo $DR_TEXT['IMPORT']; ?></button>
<?php
    }
}
?>
    <button class="btn" type="button" onclick="window.location = '<?php echo $ToolUrl; ?>';"><?php echo $TEXT['CANCEL']; ?></button>

 </form>

</div>

<?php
}

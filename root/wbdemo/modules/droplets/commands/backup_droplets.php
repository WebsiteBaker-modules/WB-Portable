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

print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />'; 
print_r( $aFilesInDir ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die(); 
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

$sOverviewDroplets = $TEXT['LIST_OPTIONS'];

// suppress to print the header, so no new FTAN will be set
//$admin = new admin('Addons', 'templates_uninstall', false);
if( !$admin->checkFTAN() ){
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
    exit();
}
// After check print the header

if( !function_exists( 'make_dir' ) )  {  require(WB_PATH.'/framework/functions.php');  }
if(!function_exists('insertDropletFile')) { require('droplets.functions.php'); }
//$admin->print_header();
?>
<h4 style="margin: 0; border-bottom: 1px solid #DDD; padding-bottom: 5px;">
    <a href="<?php echo $admintool_link;?>" title="<?php echo $HEADING['ADMINISTRATION_TOOLS']; ?>"><?php echo $HEADING['ADMINISTRATION_TOOLS']; ?></a>
    »
    <a href="<?php echo $ToolUrl;?>" title="<?php echo $sOverviewDroplets ?>" alt="<?php echo $sOverviewDroplets ?>">Droplets</a>
</h4>
<?php

// create backup filename with pre index
$sBackupDir = $ModuleRel.'data/archiv/';
make_dir( WB_PATH.$sBackupDir );

$sDropletDir = $ModuleRel.'example/';

$sTimeStamp = '_'.strftime('%Y%m%d_%H%M%S', time()+ TIMEZONE ).'.zip';

$FilesInDB = '*';
$aFullList = glob( WB_PATH.$ModuleRel.'data/archiv/*.zip', GLOB_NOSORT); 

if( isset( $aRequestVars['cb'] ) && sizeof( $aRequestVars['cb']) == 1  ) {
    $FilesInDB  = '';
    foreach( $aRequestVars['cb'] as $FileName ) { 
        $sSearchFor = $FileName;
        $FilesInDB .= '\''.$FileName.'\'';
    }
    $sBackupName = 'Droplet_'.$FileName.$sTimeStamp;
} elseif( isset( $aRequestVars['cb'] ) && sizeof( $aRequestVars['cb'] > 1 ) ) {
    $FilesInDB  = '';
    foreach( $aRequestVars['cb'] as $FileName ) { 
        $sSearchFor = $FileName;
        $FilesInDB .= '\''.$FileName.'\',';
    }
    $sBackupName = 'DropletsBackup'.$sTimeStamp;
} else {
    $sSearchFor  = 'DropletsFullBackup';
    $sBackupName = 'DropletsFullBackup'.$sTimeStamp;
}

$aFilesInDir = array();
foreach ($aFullList as $index =>$sItem) {
    if (preg_match('/[0-9]+_('.$sSearchFor.'_[^\.]*?)\.zip/si', $sItem, $aMatch)) {
        $aFilesInDir[$index+1] = $aMatch[1];
    }
}

unset($aFullList);

$sZipFile = $sBackupDir.$sBackupName;

$sFilesToZip = backupDropletFromDatabase( WB_PATH.$sDropletDir, $FilesInDB );

if( !class_exists('PclZip',false) ) { require( WB_PATH.'/include/pclzip/pclzip.lib.php'); }
$archive = new PclZip( WB_PATH.$sZipFile );

$archiveList = $archive->create( $sFilesToZip , PCLZIP_OPT_REMOVE_ALL_PATH );

if ($archiveList == 0){
    echo 'Packaging error: '.$archive->errorInfo(true);
    msgQueue::add("Error : ".$archive->errorInfo(true));
} elseif(is_readable(WB_PATH.$sBackupDir)) {
?>

<header class="droplets"><h4 >Create archive: <?php echo basename($sZipFile); ?></h4></header>

<section class="droplets drop-outer">
<ol>
<?php
    foreach($archiveList AS $key=>$aDroplet ) {
?>
    <li>Backup <strong> <?php echo $aDroplet['stored_filename']; ?></strong></li>
<?php } ?>

</ol>
<div class="drop-backup">
<h2>Backup created - <a class="btn" href="<?php echo WB_URL.$sBackupDir.$sBackupName; ?>"><?php echo $Droplet_Message['GENERIC_LOCAL_DOWNLOAD']; ?></a>
                  <button style="padding: 0.2825em 0.8525em; " name="cancel" class="btn" type="button" onclick="window.location='<?php echo $ToolUrl; ?>';"><?php echo $TEXT['CANCEL']; ?></button>

</h2>
</div>
</section>
<?php  } else {
    msgQueue::add('Backup not created - '.$TEXT['BACK'].'');
}


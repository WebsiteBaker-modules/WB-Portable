<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: droplets.functions.php 2070 2014-01-03 01:21:42Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/droplets/droplets.functions.php $
 * @lastmodified    $Date: 2014-01-03 02:21:42 +0100 (Fr, 03. Jan 2014) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {

    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
}
/* -------------------------------------------------------- */

function prepareDropletToFile($aDroplet) {
    $retVal = '';
    $aComment =  array();
    $sDescription = '//:'.(($aDroplet['description']!='') ? $aDroplet['description']: 'Add a desription');
    $sComments = '';
    $aComment = explode("\n", $aDroplet['comments']);
    if( (sizeof($aComment)) ){
        foreach($aComment as $isComments) {
          if( trim($isComments) !='') {
              $sComments .= '//:'.$isComments."\n";
          }
        }
    }
    if( !$sComments ){
        $sComments .= '//:use [['.$aDroplet['name'].']]'."\n";
    }
    $sCode = '';
    $aCode = explode("\n",$aDroplet['code']);
    if( (sizeof($aCode)) ){
        foreach($aCode AS $isCode) {
          if( $isCode!='') {
                $sCode .= $isCode."\n";
          }
        }
    }
 
    $retVal = $sDescription."\n".$sComments.rtrim($sCode,"\n");
    return $retVal;
}

function backupDropletFromDatabase( $sTmpDir, $FilesInDB='*' ) {
    global $database;
    $retVal = '';
    $FilesInDB = rtrim($FilesInDB, ',');
    $sqlWhere = ( ($FilesInDB=='*') ? '': 'WHERE `name` IN ('.$FilesInDB.') ');
    $sql = 'SELECT `name`,`description`,`comments`,`code`  FROM `'.TABLE_PREFIX.'mod_droplets` '
         . $sqlWhere
         . 'ORDER BY `modified_when` DESC';
    if( $oRes = $database->query($sql) ) {
        while($aDroplet = $oRes->fetchRow(MYSQLI_ASSOC)) {
            $sData = prepareDropletToFile($aDroplet);
            $sFileName = $sTmpDir.$aDroplet['name'].'.php';
            if(file_put_contents($sFileName,$sData)) {
                $retVal .= $sFileName.',';
            }
        }
    }
    return $retVal;
}

/**
 * importDropletToDB()
 * 
 * @param mixed $aDroplet
 * @param mixed $msg
 * @param mixed $bOverwriteDroplets
 * @return
 */
function insertDroplet( array $aDroplet, $bUpdateDroplets = false ) {
        global $admin, $database;
        $sImportDroplets = '';
        $oDb = $database;
        $extraSql = '';
        $sPattern = "#//:#im";
        $sDropletFile = $aDroplet['name'];
        $sDropletFile = preg_replace('/^\xEF\xBB\xBF/', '', $sDropletFile);
        $sDropletName = pathinfo ($sDropletFile, PATHINFO_FILENAME);
        // get right $aFileData a) from Zip or b) from File
        if( isset($aDroplet['content']) ) { 
            $aFileData = $aDroplet['content']; 
            $sFileData = $aFileData[0];
            $bRetval  = (bool)preg_match_all($sPattern, $sFileData, $matches, PREG_SET_ORDER);
            if ( $bRetval == false ) { return $bRetval; }
        }
        if( isset($aDroplet['output']) ) { $aFileData = file($sDropletFile); }
        // prepare table mod_droplets fields
        if( sizeof($aFileData) > 0 ) {
                // get description, comments and oode
                $bDescription = false;
                $bComments = false;
                $bCode = false;
                $sDescription = '';
                $sComments = '';
                $sCode = '';
                while ( sizeof($aFileData) > 0 ) {
                    $sSqlLine = trim(array_shift($aFileData));
                    $isNotCode = (bool)preg_match($sPattern, $sSqlLine);
                    if( $isNotCode==true ) {
// first step line is description
                        if($bDescription==false) {
                            $sDescription .= str_replace('//:','',$sSqlLine);
                            $bDescription = true;
                        } else {
// second step fill comments
                            $sComments .= trim(str_replace('//:','',$sSqlLine)."\n");
                        }
                    } else {
// third step fill code
                        $sCode .= str_replace('//:','',$sSqlLine)."\n";
                    }
                }
        }
            // TODO future set parameter to class RawDropletInterface
            $sql = 'SELECT `name` FROM `'.TABLE_PREFIX.'mod_droplets` '
                 . 'WHERE `name` LIKE \''.addcslashes($oDb->escapeString($sDropletName), '%_').'\' ';
            if( !( $sTmpName = $oDb->get_one($sql)) )
            {
                $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets`';
                $sImportDroplets = $sDropletName ;
            } elseif ($bUpdateDroplets) {
                $sDropletName = $sTmpName;
                $sql = 'UPDATE `'.TABLE_PREFIX.'mod_droplets` ';
                $extraSql = 'WHERE `name` = \''.addcslashes($oDb->escapeString($sDropletName), '%_').'\' ';
                $sImportDroplets = $sDropletName;
            }
            if( !isset($sTmpName) || $bUpdateDroplets) {
              $iModifiedWhen = time();
              $iModifiedBy = (method_exists($admin, 'get_user_id') && ($admin->get_user_id()!=null) ? $admin->get_user_id() : 1);
              $sql .= 'SET  `name` =\''.$oDb->escapeString($sDropletName).'\','
                   .       '`description` =\''.$oDb->escapeString($sDescription).'\','
                   .       '`comments` =\''.$oDb->escapeString($sComments).'\','
                   .       '`code` =\''.$oDb->escapeString($sCode).'\','
                   .       '`modified_when` = '.$iModifiedWhen.','
                   .       '`modified_by` = '.$iModifiedBy.','
                   .       '`active` = 1'
                   .       $extraSql;
          }
          if( $oDb->query($sql) ) {
          } else {
          }

    return ($sImportDroplets != '') ? $sImportDroplets : false;
}

function insertDropletFile($aDropletFiles,&$msg,$bOverwriteDroplets)
{
    global $database;
    $oDb = $database;
    $admin = new admin ('##skip##');
    $OK  = ' <span style="color:#006400; font-weight:bold;">OK</span> ';
    $FAIL = ' <span style="color:#ff0000; font-weight:bold;">FAILED</span> ';
    foreach ($aDropletFiles as $sDropletFile) {
        $msgSql = '';
        $extraSql = '';
        $sDropletName = pathinfo ($sDropletFile, PATHINFO_FILENAME);
        $sql = 'SELECT `name` FROM `'.TABLE_PREFIX.'mod_droplets` '
             . 'WHERE `name` LIKE \''.addcslashes($oDb->escapeString($sDropletName), '%_').'\' ';
        if( !( $sTmpName = $oDb->get_one($sql)) )
        {
            $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets`';
            $msgSql = 'INSERT Droplet `'.$oDb->escapeString($sDropletName).'` INTO`'.TABLE_PREFIX.'mod_droplets`'." $OK";
        } elseif ($bOverwriteDroplets) 
        {
            $sDropletName = $sTmpName;
            $sql = 'UPDATE `'.TABLE_PREFIX.'mod_droplets` ';
            $extraSql = 'WHERE `name` = \''.addcslashes($oDb->escapeString($sDropletName), '%_').'\' ';
            $msgSql   = 'UPDATE Droplet `'.$sDropletName.'` INTO`'.TABLE_PREFIX.'mod_droplets`'." $OK";
        }
// get description, comments and oode
        $sDropletFile = preg_replace('/^\xEF\xBB\xBF/', '', $sDropletFile);
        if( ($msgSql!='') && ($aFileData = file($sDropletFile)) ) {
                $bDescription = false;
                $bComments = false;
                $bCode = false;
                $sDescription = '';
                $sComments = '';
                $sCode = '';
                $sPattern = "#//:#im";
                while ( sizeof($aFileData) > 0 ) {
                    $sSqlLine = trim(array_shift($aFileData));
                    $isNotCode = (bool)preg_match($sPattern, $sSqlLine);
                    if( $isNotCode==true ) {
// first step line is description
                        if($bDescription==false) {
                            $sDescription .= str_replace('//:','',$sSqlLine);
                            $bDescription = true;
                        } else {
// second step fill comments
                            $sComments .= str_replace('//:','',$sSqlLine)."\n";
                        }
                    } else {
// third step fill code
                        $sCode .= str_replace('//:','',$sSqlLine)."\n";
                    }
                }
            $iModifiedWhen = time();
            $iModifiedBy = (method_exists($admin, 'get_user_id') && ($admin->get_user_id()!=null) ? $admin->get_user_id() : 1);
            $sql .= 'SET  `name` =\''.$oDb->escapeString($sDropletName).'\','
                 .       '`description` =\''.$oDb->escapeString($sDescription).'\','
                 .       '`comments` =\''.$oDb->escapeString($sComments).'\','
                 .       '`code` =\''.$oDb->escapeString($sCode).'\','
                 .       '`modified_when` = '.$iModifiedWhen.','
                 .       '`modified_by` = '.$iModifiedBy.','
                 .       '`active` = 1'
                 .       $extraSql;
        }
        if( $oDb->query($sql) ) {
            if( $msgSql!='' ) { $msg[] = $msgSql; }
        } else {
            $msg[] = $oDb->get_error();
        }
    }
    return;
}
/* -------------------------------------------------------- */

function isDropletFile($sFileName)
{
    $bRetval = false;
    $matches = array();
    if(($sFileData = file_get_contents($sFileName)) !== false)
    {
//        $sPattern = "#(?://:)+[\w]*\w?#is";
//        $sPattern = "#//:[\w].+#imS";
        $sPattern = "#//:#im";
        $bRetval  = (bool)preg_match_all($sPattern, $sFileData, $matches, PREG_SET_ORDER);
    }
    return $bRetval;
}

/* -------------------------------------------------------- */
    function getDropletFromFiles($sBaseDir)
    {
        $aRetval = array();
        $oIterator = new DirectoryIterator($sBaseDir);
        foreach ($oIterator as $fileInfo) {
        // iterate the directory
            if($fileInfo->isDot()) continue;
            $sFileName = rtrim(str_replace('\\', '/', $fileInfo->getPathname()), '/');
            if($fileInfo->isFile()) {
            // only droplets are interesting
                if((file_exists($sFileName) && isDropletFile($sFileName))) {
                // if dir has no corresponding accessfile remember it
                    $aRetval[] = $sFileName;
                }
            }
        }
        return $aRetval;
    }

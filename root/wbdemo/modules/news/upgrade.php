<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://www.websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade.php 1593 2012-02-01 22:29:36Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/upgrade.php $
 * @lastmodified    $Date: 2012-02-01 23:29:36 +0100 (Mi, 01. Feb 2012) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
/* **** START UPGRADE ******************************************************* */
if(!function_exists('mod_news_Upgrade'))
{
    function mod_news_Upgrade()
    {
        global $database, $msg, $admin, $MESSAGE, $globalStarted,$callingScript;
        $sPagesPath = WB_PATH.PAGES_DIRECTORY;
        $sPostsPath = $sPagesPath.'/posts';
        $msg = array();
    // create /posts/ - directory if not exists
        if(!file_exists($sPostsPath)) {
            if(is_writable($sPagesPath)) {
                make_dir(WB_PATH.PAGES_DIRECTORY.'/posts/');
            }else {
                if(!$globalStarted){
                    $msg[] = ($MESSAGE['PAGES_CANNOT_CREATE_ACCESS_FILE']);
                }else {
                    $msg[] = $MESSAGE['PAGES_CANNOT_CREATE_ACCESS_FILE'].'<br />';
                    return $msg;
                }
            }
            if (!$globalStarted) {echo 'directory "'.PAGES_DIRECTORY.'/posts/" created.<br />'; }
        }
        $aTable = array('mod_news_posts','mod_news_groups','mod_news_comments','mod_news_settings');
        for($x=0; $x<sizeof($aTable);$x++) {
            if(($sOldType = $database->getTableEngine(TABLE_PREFIX.$aTable[$x]))) {
                if(('myisam' != strtolower($sOldType))) {
                    if(!$database->query('ALTER TABLE `'.TABLE_PREFIX.$aTable[$x].'` Engine = \'MyISAM\' ')) {
                        $msg[] = $database->get_error();
                    }
                }
            } else {
                $msg[] = $database->get_error();
            }
        }
    // check if new fields must be added
        $doImportDate = true;
        if(!$database->field_exists(TABLE_PREFIX.'mod_news_posts', 'created_when')) {
            if(!$database->field_add(TABLE_PREFIX.'mod_news_posts', 'created_when',
                                    'INT NOT NULL DEFAULT \'0\' AFTER `commenting`')) {
                if (!$globalStarted){
                    echo $MESSAGE['RECORD_MODIFIED_FAILED'].'<br />';
                    return $msg;
                }else {
                    $admin->print_error($MESSAGE['RECORD_MODIFIED_FAILED']);
                }
            }
            if (!$globalStarted) { echo 'datafield `'.TABLE_PREFIX.'mod_news_posts`.`created_when` added.<br />'; }
        } else { $doImportDate = false; }
        if(!$database->field_exists(TABLE_PREFIX.'mod_news_posts', 'created_by')) {
            if(!$database->field_add(TABLE_PREFIX.'mod_news_posts', 'created_by',
                                    'INT NOT NULL DEFAULT \'0\' AFTER `created_when`')) {
                if (!$globalStarted){
                    echo $MESSAGE['RECORD_MODIFIED_FAILED'].'<br />';
                    return ;
                } else {
                    $admin->print_error($MESSAGE['RECORD_MODIFIED_FAILED']);
                }
            }
            if (!$globalStarted) {echo 'datafield `'.TABLE_PREFIX.'mod_news_posts`.`created_by` added.<br />'; }
        }
    // preset new fields `created_by` and `created_when` from existing values
        if($doImportDate) {
            $sql  = 'UPDATE `'.TABLE_PREFIX.'mod_news_posts` '
                  . 'SET `created_by`=`posted_by`, `created_when`=`posted_when`';
            $database->query($sql);
        }

    // now iterate through all existing accessfiles,
    // write its creation date into database
        $oDir = new DirectoryIterator($sPostsPath);
        $count = 0;
        foreach ($oDir as $fileinfo)
        {
            $fileName = $fileinfo->getFilename();
            if((!$fileinfo->isDot()) &&
               ($fileName != 'index.php') &&
               (substr_compare($fileName,PAGE_EXTENSION,(0-strlen(PAGE_EXTENSION)),strlen(PAGE_EXTENSION)) === 0)
              )
            {
            // save creation date from old accessfile
                if($doImportDate) {
                    $link = '/posts/'.preg_replace('/'.preg_quote(PAGE_EXTENSION).'$/i', '', $fileinfo->getFilename());
                    $sql  = 'UPDATE `'.TABLE_PREFIX.'mod_news_posts` SET '
                          . '`created_when`='.$fileinfo->getMTime().' '
                          . 'WHERE `link`=\''.$database->escapeString($link).'\' '
                          .   'AND `created_when`= 0';
                    $database->query($sql);
                }
            // delete old access file
                unlink($fileinfo->getPathname());
                $count++;
            }
        }
        unset($oDir);
        if ($globalStarted && $count > 0) {
            $msg[] = 'save date of creation from '.$count.' old accessfiles and delete these files.<br />';
        }
// ************************************************
    // Check the validity of 'create-file-timestamp' and balance against 'posted-timestamp'
        $sql  = 'UPDATE `'.TABLE_PREFIX.'mod_news_posts` ';
        $sql .= 'SET `created_when`=`published_when` ';
        $sql .= 'WHERE `published_when`<`created_when`';
        $database->query($sql);
        $sql  = 'UPDATE `'.TABLE_PREFIX.'mod_news_posts` ';
        $sql .= 'SET `created_when`=`posted_when` ';
        $sql .= 'WHERE `published_when`=0 OR `published_when`>`posted_when`';
        $database->query($sql);
// ************************************************
    // rebuild all access-files
        $count = 0;
        $backSteps = preg_replace('@^'.preg_quote(WB_PATH).'@', '', $sPostsPath);
        $backSteps = str_repeat( '../', substr_count($backSteps, '/'));
        $sql  = 'SELECT `page_id`,`post_id`,`section_id`,`link` ';
        $sql .= 'FROM `'.TABLE_PREFIX.'mod_news_posts`';
        $sql .= 'WHERE `link` != \'\'';
        if( ($resPosts = $database->query($sql)) )
        {
            while( $recPost = $resPosts->fetchRow() )
            {
                $file = $sPagesPath.$recPost['link'].PAGE_EXTENSION;
                $content =
                    '<?php'."\n".
                    '// *** This file is generated by WebsiteBaker Ver.'.VERSION."\n".
                    '// *** Creation date: '.date('c')."\n".
                    '// *** Do not modify this file manually'."\n".
                    '// *** WB will rebuild this file from time to time!!'."\n".
                    '// *************************************************'."\n".
                    "\t".'$page_id    = '.$recPost['page_id'].';'."\n".
                    "\t".'$section_id = '.$recPost['section_id'].';'."\n".
                    "\t".'$post_id    = '.$recPost['post_id'].';'."\n".
                    "\t".'$post_section = '.$recPost['section_id'].';'."\n".
                    "\t".'require(\''.$backSteps.'index.php\');'."\n".
                    '// *************************************************'."\n";
                if( file_put_contents($file, $content) !== false ) {
                // Chmod the file
                    change_mode($file);
                }else {
                    if(!$globalStarted){
                        echo $MESSAGE['PAGES_CANNOT_CREATE_ACCESS_FILE'].'<br />';
                        return;
                    } else {
                        $msg[] = ($MESSAGE['PAGES_CANNOT_CREATE_ACCESS_FILE']);
                    }
                }
                $count++;
            }
        }
        if ($globalStarted) { $msg[] = 'created '.$count.' new accessfiles.'; }
        return $msg;
    }
}

// ------------------------------------
    $callingScript = $_SERVER["SCRIPT_NAME"];
    $globalStarted = preg_match('/upgrade\-script\.php$/', $callingScript);
/*
    $tmp = 'upgrade-script.php';
    $globalStarted = substr_compare($callingScript, $tmp,(0-strlen($tmp)),strlen($tmp)) === 0;
*/
    $aMsg = mod_news_Upgrade();
    if (!$globalStarted && sizeof($aMsg)) {print implode("\n", $aMsg)."\n";}

/* **** END UPGRADE ********************************************************* */
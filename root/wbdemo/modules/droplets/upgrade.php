<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       2004-2009, Ryan Djurovich
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: upgrade.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource        $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/upgrade.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_URL') == false) { die('Cannot access '.basename(__dir__).'/'.basename(__file__).' directly'); }
/* -------------------------------------------------------- */

$table_name = TABLE_PREFIX .'mod_droplets';
$description = 'INT NOT NULL default 0 ';
$database->field_add($table_name,'show_wysiwyg',$description.'AFTER `active`' );
$database->field_add($table_name,'admin_view',$description.'AFTER `active`' );
$database->field_add($table_name,'admin_edit',$description.'AFTER `active`' );


    //add all droplets from the droplet subdirectory
    $folder=opendir(WB_PATH.'/modules/droplets/example/.');
    $names = array();
    while ($file = readdir($folder)) {
        $ext=strtolower(substr($file,-4));
        if ($ext==".php"){
            if ($file<>"index.php" ) {
                $names[count($names)] = $file;
            }
        }
    }
    closedir($folder);

    foreach ($names as $dropfile)
    {
        if ( check_unique($dropfile) ) { continue; }
        $droplet = addslashes(getDropletCodeFromFile($dropfile));
        if ($droplet != "")
        {
            $description = "Example Droplet";
            $comments = "Example Droplet";
            $cArray = explode("\n",$droplet);
            if (substr($cArray[0],0,3) == "//:") {
                $description = trim(substr($cArray[0],3));
                array_shift ( $cArray );
            }
            if (substr($cArray[0],0,3) == "//:") {
                $comments = trim(substr($cArray[0],3));
                array_shift ( $cArray );
            }
            $droplet = implode ( "\n", $cArray );
            $name = substr($dropfile,0,-4);
            $modified_when = time();
            $modified_by = (method_exists($admin, 'get_user_id') && ($admin->get_user_id()!=null) ? $admin->get_user_id() : 1);
            $sql  = 'INSERT INTO `'.TABLE_PREFIX.'mod_droplets` SET ';
            $sql .= '`name` = \''.$name.'\', ';
            $sql .= '`code` = \''.$droplet.'\', ';
            $sql .= '`description` = \''.$description.'\', ';
            $sql .= '`comments` = \''.$comments.'\', ';
            $sql .= '`active` = 1, ';
            $sql .= '`modified_when` = '.$modified_when.', ';
            $sql .= '`modified_by` = '.$modified_by;
            if( !$database->query($sql) ) {
                $msg[] = $database->get_error();
            }
            // do not output anything if this script is called during fresh installation
            // if (method_exists($admin, 'get_user_id')) echo "Droplet import: $name<br/>";
        }
    }

    function getDropletCodeFromFile ( $dropletfile ) {
        $data = '';
        $filename = WB_PATH."/modules/droplets/example/".$dropletfile;
        if (file_exists($filename)) {
            $filehandle = fopen ($filename, "r");
            $data = fread ($filehandle, filesize ($filename));
            fclose($filehandle);
            // unlink($filename); doesnt work in unix
        }
        return $data;
    }

function check_unique($name) {
    global $database;
    $name = substr($name,0,-4);
    $retVal = 0;
    $sql = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'mod_droplets` ';
    $sql .= 'WHERE `name` = \''.$name.'\'';
    $retVal = intval($database->get_one($sql));
    return ($retVal == 1);
}


<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://www.websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: install.php 1544 2011-12-15 15:57:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/install.php $
 * @lastmodified    $Date: 2011-12-15 16:57:59 +0100 (Do, 15. Dez 2011) $
 *
 */

if(defined('WB_PATH'))
{
    // create tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}
      if(!function_exists('insertDropletFile')) { require('droplets.functions.php'); }
      $sBaseDir = rtrim(str_replace('\\', '/',realpath(dirname(__FILE__).'/example/')), '/').'/';
        $aDropletFiles = getDropletFromFiles($sBaseDir);
        $bOverwriteDroplets = false;
        insertDropletFile($aDropletFiles,$msg,$bOverwriteDroplets);

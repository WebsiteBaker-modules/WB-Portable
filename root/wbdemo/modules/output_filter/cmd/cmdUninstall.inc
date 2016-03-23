<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @author          Christian Sommer, WB-Project, Werner v.d. Decken
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: uninstall.php 1520 2011-11-09 00:12:37Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/output_filter/uninstall.php $
 * @lastmodified    $Date: 2011-11-09 01:12:37 +0100 (Mi, 09. Nov 2011) $
 *
 */
if(defined('WB_PATH'))
{
    // create tables from sql dump file
    if (is_readable( $sAddonPath.'/install-struct.sql')) {
        $database->SqlImport( $sAddonPath.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}

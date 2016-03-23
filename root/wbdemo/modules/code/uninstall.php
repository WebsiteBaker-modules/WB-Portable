<?php
/**
 *
 * @category        modules
 * @package         code
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: uninstall.php 1428 2011-02-07 04:55:31Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/code/uninstall.php $
 * @lastmodified    $Date: 2011-02-07 05:55:31 +0100 (Mo, 07. Feb 2011) $
 *
 */
if(defined('WB_PATH'))
{
    // delete tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}

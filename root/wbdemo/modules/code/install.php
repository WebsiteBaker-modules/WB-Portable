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
 * @version         $Id: install.php 1374 2011-01-10 12:21:47Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/code/install.php $
 * @lastmodified    $Date: 2011-01-10 13:21:47 +0100 (Mo, 10. Jan 2011) $
 *
 */

if(defined('WB_PATH'))
{
    // create tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}

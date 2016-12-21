<?php
/**
 *
 * @category        modules
 * @package         
 * @subpackage      
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
    throw new IllegalFileException();
} else {
    $msg = '';
    $sTable = TABLE_PREFIX.'mod_captcha_control';
    if(($sOldType = $database->getTableEngine($sTable))) {
        if(('myisam' != strtolower($sOldType))) {
            if(!$database->query('ALTER TABLE `'.$sTable.'` Engine = \'MyISAM\' ')) {
                $msg = $database->get_error();
            }
        }
    } else {
        $msg = $database->get_error();
    }
// ------------------------------------
}

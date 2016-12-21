<?php
/**
 *
 * @category        admin
 * @package         admintools
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: thumb.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/media/thumb.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */

if(!defined('WB_URL'))
{
    $config_file = realpath('../../config.php');
    if(file_exists($config_file) && !defined('WB_URL'))
    {
        require($config_file);
    }
}
//if(!class_exists('admin', false)){ include(WB_PATH.'/framework/class.admin.php'); }
$modulePath = dirname(__FILE__);

/*
// Get image
    $requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
    $image = (isset(${$requestMethod}['img']) ? ${$requestMethod}['img'] : '');
print '<pre style="text-align: left;"><strong>function '.__FUNCTION__.'( '.''.' );</strong>  basename: '.basename(__FILE__).'  line: '.__LINE__.' -> <br />';
print_r( $_GET ); print '</pre>';  die(); // flush ();sleep(10);
*/

if (isset($_GET['img']) && isset($_GET['t'])) {
    if(!class_exists('PhpThumbFactory', false)){ include($modulePath.'/inc/ThumbLib.inc.php'); }
//    require_once($modulePath.'/inc/ThumbLib.inc.php');
    $image = addslashes($_GET['img']);
    $type = intval($_GET['t']);
//    $media = WB_PATH.MEDIA_DIRECTORY.'/';
    $thumb = PhpThumbFactory::create(WB_PATH.MEDIA_DIRECTORY.'/'.$image);

    if ($type == 1) {
        $thumb->adaptiveResize(20,20);
//        $thumb->resize(30,30);
//        $thumb->cropFromCenter(80,50);
//         $thumb->resizePercent(40);
    } else {
        $thumb->Resize(300,300);
//         $thumb->resizePercent(25);
//        $thumb->cropFromCenter(80,50);
    }
    $thumb->show();

 }

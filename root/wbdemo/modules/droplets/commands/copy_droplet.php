<?php
/**
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category        modules
 * @package         droplets
 * @subpackage      copy_droplets
 * @author          Dietmar Wöllbrink
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.4
 * @requirements    PHP 5.4 and higher
 * @version         $Id: copy_droplet.php 16 2016-09-13 20:52:49Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/commands/copy_droplet.php $
 * @lastmodified    $Date: 2016-09-13 22:52:49 +0200 (Di, 13. Sep 2016) $
 *
 */
 /* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
//$sTimeStamp  = '_'.strftime('%Y%m%d_%H%M%S', time()+ TIMEZONE );
$sTimeStamp  = '';
if ($iDropletAddId === false) {
    $oApp->print_error('COPY_DROPLET::'.$MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl);
    exit();
}
    // Get header and footer
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` = '.$iDropletAddId;
    $oDroplet = $oDb->query($sql);
    $aCopyDroplet = $oDroplet->fetchRow(MYSQLI_ASSOC);
//    $aCopyDroplet['name'] = '';
    $content = (htmlspecialchars($aCopyDroplet['code']));
    $droplet_id = $oApp->getIDKEY(0);

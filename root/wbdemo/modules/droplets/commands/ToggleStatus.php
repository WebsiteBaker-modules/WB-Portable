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
 * @subpackage      ToggleStatus
 * @author          Dietmar Wöllbrink
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.4
 * @requirements    PHP 5.4 and higher
 * @version         $Id: ToggleStatus.php 16 2016-09-13 20:52:49Z dietmar $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb2-modules/addons/droplets/commands/ToggleStatus.php $
 * @lastmodified    $Date: 2016-09-13 22:52:49 +0200 (Di, 13. Sep 2016) $
 *
 */
 /* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
/*
*/
if ($droplet_id === false) {
    $oApp->print_error('TOGGLE_DROPLET_IDKEY::'.$MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl);
    exit();
}
    $sql  = 'SELECT `active` FROM `'.TABLE_PREFIX.'mod_droplets` ';
    $sqlWhere  = 'WHERE `id` = '.(int)$droplet_id;
    $val = !(bool)$oDb->get_one($sql.$sqlWhere);
    $sql = 'UPDATE `'.TABLE_PREFIX.'mod_droplets` SET '
//         .  '`active`='.$val.' ';
         .  '`active`='.($val ? true : 0).' ';
   if (!$oDb->query($sql.$sqlWhere)){
        msgQueue::add($sql.$sqlWhere.'<br />TOGGLE_DROPLET::'.$oDb->get_error() );
   } else {
//            msgQueue::add('TOGGLE_DROPLET::'.$TEXT['SUCCESS'], true );
   }


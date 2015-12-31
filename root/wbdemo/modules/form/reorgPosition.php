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
 * @package         news
 * @subpackage      reorgPosition
 * @author          Dietmar Wöllbrink
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
 
require( dirname(dirname((__DIR__))).'/config.php' );
require(WB_PATH.'/modules/admin.php');
$backlink = ADMIN_URL.'/pages/modify.php?page_id='.(int)$page_id;
if(!$admin->checkFTAN('GET')) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}
if ( !class_exists('order', false) ) { require(WB_PATH.'/framework/class.order.php'); }
$form   = new order(TABLE_PREFIX.'mod_form_fields', 'position', 'field_id', 'section_id');
$form->clean( $section_id );

$admin->print_success($TEXT['SUCCESS'], $backlink );


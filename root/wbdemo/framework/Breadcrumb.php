<?php
/*
 * Breadcrumb.php
 *
 * Copyright 2014 Manuela von der Decken <manuela@isteam.de>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 *
 */
class Breadcrumb
{

    protected $oDb             = null;
    protected $oCore           = null;
    protected $oLang           = null;
    protected $bShowLinks      = true;
    protected $aFieldsToReturn = array(
        'link'          => '',
        'target'        => '',
        'page_title'    => '',
        'menu_title'    => '',
        'description'   => '',
        'visibility'    => '',
        'language'      => '',
        'modified_when' => '',
        'display_name'  => ''
    );

/**
 * Constructor of class Breadcrumb.
 * @param frontend $oCore valid core object
 * @param database $oDb   valid database object
 * @return void
 */
    public function __construct(frontend $oCore, database $oDb)
    {
        $this->oDb   = $oDb;
        $this->oCore = $oCore;
    }
/**
 * build list of pages
 * @param int $iStartLevel  breadcrumb starts with given menu level
 * @param int $iMaxDeph  show max number of items. last one is current page always
 * @return array list of pages
 */
    public function buildList($iStartLevel = 0, $iMaxDepth = 0)
    {
        $aItems = array();
        if ($this->oCore->page_id == 0) return $aItems;
        $aList = $this->oCore->page_trail;
    // remove pages less then iStartLevel
        if ($iStartLevel > (sizeof($aList)-1)) {
            $iStartLevel = (sizeof($aList)-1);
        }
        $aList = array_splice($aList, $iStartLevel);
    // trim leading pages to iMaxDepth pages
        if ($iMaxDepth > 0 && sizeof(aList) > $iMaxDepth) {
            $aList = array_slice($aList, (0 - $iMaxDepth))
        }
        $sql = 'SELECT `p`.*, '
             .        'IFNULL(`u`.`display_name`, \'*** Guest ***\') '
             . 'FROM `'.TABLE_PREFIX.'pages` `p` '
             .      'LEFT OUTER JOIN `'.TABLE_PREFIX.'users` `u` '
             . 'ON `p`.`modified_by`=`u`.`user_id` '
             . 'WHERE `p`.`page_id` IN ('.implode(',', $aList).') '
             . 'ORDER BY `p`.`level`';
        if (($oPages = $this->oDb->query($sql))) {
        // get all matching pages
            while (($aPage = $oPages->fetchRow(MYSQL_ASSOC))) {
            // iterate all maching pages
                if (
                // hide link if one of following condition matches
                    !(
                        $this->oCore->ami_group_member($aPage['viewing_users'])
                        || $this->oCore->is_group_match(
                               $aPage['viewing_groups'],
                               $this->oCore->get_groups_id()
                           )
                    ) // no link for no rights
                    || $aPage['visibility'] == 'hidden' // no link for hidden pages
                    || $aPage['visibility'] == 'none'   // no link for not published pages
                    || $aList[sizeof($aList)-1] == $aPage['page_id'] // no link for current page
                ) {
                // hide link of this page
                    $aPage['link'] = '';
                } else {
                // complete link
                    $aPage['link'] = WB_URL.PAGES_DIRECTORY
                                   . $aPage['link']
                                   . PAGE_EXTENSION;
                }
                // remove all unneeded fields and add this page to result
                $aItems[] = array_intersect_key($aPage, $this->aFieldsToReturn);
            }
        }
        return $aItems;
    }

/**
 * generate list and show it using a twig template
 * @param Twig_Template $oTemplate  a valid Twig_Template object
 * @param int $iStartLevel  breadcrumb starts with given menu level
 * @param int $iMaxDeph  show max number of items. last one is current page always
 * @return string  the rendered template
 */
    public function show(Twig_Template $oTemplate, $iStartLevel = 0, $iMaxDepth = 0)
    {
        $aTwigData = array(
            'Items' => $this->buildList($iStartLevel, $iMaxDepth),
            'Lang'  => $GLOBALS['MENU']
        );
        $sRetval = $oTemplate->render($aTwigData);
        return $sRetval;
    }

/**
 * setReturnFields
 * @param mixed $mFieldsList list of fields as array or CSL
 * @return void
 * @description here you can set a complete new set of fields to return
 */
    public function setReturnFields($mFieldsList)
    {
        if (! is_array($mFieldsList)) {
            $mFieldsList = preg_split('/[\s,;\|]+/s', $mFieldsList, null, PREG_SPLIT_NO_EMPTY);
        }
        $this->aFieldsToReturn = array();
        foreach ($mFieldsList as $sField) {
            $this->aFieldsToReturn[$sField] = '';
        }
        if (sizeof($this->aFieldsToReturn) == 0) {
            $this->aFieldsToReturn['link'] = '';
            $this->aFieldsToReturn['menu_title'] = '';
        }
    }

/**
 * add an additional field for return fields
 * @param string $sFieldName name of the field to add
 * @return void
 */
    public function addReturnField($sFieldName)
    {
        if (!isset($this->aFieldsToReturn[$sFieldName])) {
            $this->aFieldsToReturn[$sFieldName] = '';
        }
    }


}

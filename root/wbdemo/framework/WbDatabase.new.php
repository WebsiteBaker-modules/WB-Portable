<?php

/*
 * Copyright (C) 2016 Manuela v.d.Decken <manuela@isteam.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Description of WbDatabase
 *
 * @category     Core
 * @package      Core package
 * @subpackage   Name of subpackage if needed
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      GNU General Public License 3.0
 * @version      0.0.0
 * @revision     $Revision: $
 * @lastmodified $Date: $
 * @since        File available since 06.08.2016
 * @deprecated   no / since 0000/00/00
 * @description  xxx
 */
class WbDatabase
{
    private static $_oInstances = array();
    protected $oDb = null;
//    protected $sTablePrefix = '';

/**
 * __constructor
 *  prevent from public instancing
 */
    final protected function  __construct()
    {
        $this->oDb = $GLOBALS['database'];
//        $this->sTablePrefix = TABLE_PREFIX;

    }
/**
 * prevent from cloning
 */
    final private function __clone() {}
/**
 * get a valid instance of this class
 * @param string $sIdentifier selector for several different instances
 * @return WbDatabase object
 */
    final public static function getInstance($sIdentifier = 'core')
    {
        if( !isset(self::$_oInstances[$sIdentifier])) {
            $c = __CLASS__;
            $oInstance = new $c;
            $oInstance->sInstanceIdentifier = $sIdentifier;
            self::$_oInstances[$sIdentifier] = $oInstance;
        }
        return self::$_oInstances[$sIdentifier];
    }
/**
 * disconnect and kills an existing instance
 * @param string $sIdentifier selector for instance to kill
 */
    final public static function killInstance($sIdentifier)
    {
        if($sIdentifier != 'core') {
            if( isset(self::$_oInstances[$sIdentifier])) {
                self::$_oInstances[$sIdentifier]->disconnect();
                unset(self::$_oInstances[$sIdentifier]);
            }
        }
    }

    /**
 * default Getter for some properties
 * @param string $sPropertyName
 * @return mixed NULL on error or missing property
 */
    public function __get($sPropertyName)
    {
        switch ($sPropertyName):
            case 'getDbHandle':
            case 'db_handle':
            case 'DbHandle':
                $retval = $this->oDb->db_handle;
                break;
            case 'getDbName':
            case 'db_name':
            case 'DbName':
                $retval = $this->oDb->db_name;
                break;
            case 'getLastInsertId':
            case 'LastInsertId':
                $retval = $this->oDb->getLastInsertId();
                break;
            case 'getTablePrefix':
            case 'TablePrefix':
                $retval = TABLE_PREFIX;
//                $retval = $this->sTablePrefix;
                break;
            case 'getQueryCount':
            case 'QueryCount':
                $retval = 0;
                break;
            default:
                $retval = null;
                break;
        endswitch;
        return $retval;
    }

    public function query($statement)
    {
        return $this->oDb->query($statement);
    }
    public function doQuery($statement)
    {
        return $this->query($statement);
    }
    public function get_one( $statement )
    {
        return $this->oDb->get_one($statement);
    }
    public function getOne( $sStatement )
    {
        return $this->oDb->get_one($statement);
    }
    public function set_error($message = null)
    {
        $this->oDb->set_error($message = null);
    }
    public function setError($message = null)
    {
        $this->set_error($message = null);
    }
    public function is_error()
    {
        return $this->oDb->is_error();
    }
    public function isError()
    {
        return $this->is_error();
    }
    public function get_error()
    {
        return $this->oDb->get_error();
    }
    public function getError()
    {
        return $this->get_error;
    }
    public function escapeString($sUnescapedString)
    {
        return $this->oDb->escapeString($sUnescapedString);
    }
    public function escapeLike($sUnescapedString)
    {
        return addcslashes($sUnescapedString, '_%');
    }
    public function getLastInsertId()
    {
        return $this->__get('LastInsertId');
    }

    public function fetchArray (){
        return $this->oDb->fetchArray();
    }

    public function fetchRow($typ = MYSQLI_BOTH){
        return $this->oDb->fetchRow($typ);
    }


    public function SqlImport(
        $sSqlDump,
        $sTablePrefix  = '',
        $mAction       = true,
        $sTblEngine    = 'MyISAM',
        $sTblCollation = 'utf8_unicode_ci'
    ) {
        return $this->oDb->SqlImport($sSqlDump, $sTablePrefix, $mAction, $sTblEngine, $sTblCollation);
    }
    public function getTableEngine($table)
    {
        return $this->oDb->getTableEngine($table);
    }

}

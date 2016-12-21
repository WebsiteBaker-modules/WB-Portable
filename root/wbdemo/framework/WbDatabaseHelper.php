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
 */
/**
 * WbDatabaseHelper.php
 *
 * @category     Core
 * @package      Core_database
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @author       Dietmar W. <dietmar.woellbrink@websitebaker.org>
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.0.9
 * @revision     $Revision: 2105 $
 * @lastmodified $Date: 2014-11-24 18:41:13 +0100 (Mo, 24 Nov 2014) $
 * @deprecated   from WB version number 2.9
 * @description  Mysql database wrapper for use with websitebaker up to version 2.8.4
 */

abstract class WbDatabaseHelper {


/**
 * Alias for isField()
 * @deprecated from WB-2.8.5 and higher
 */
    public function field_exists($table_name, $field_name)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->isField($table_name, $field_name);
    }
/*
 * @param string full name of the table (incl. TABLE_PREFIX)
 * @param string name of the field to seek for
 * @return bool true if field exists
 */
    public function isField($table_name, $field_name)
    {
        $sql = 'DESCRIBE `'.$table_name.'` `'.$field_name.'` ';
        $doQuery = $this->doQuery($sql);
        return ($doQuery->numRows() != 0);
    }
/**
 * Alias for isIndex()
 * @deprecated from WB-2.8.5 and higher
 */
    public function index_exists($table_name, $index_name, $number_fields = 0)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->isIndex($table_name, $index_name, $number_fields = 0);
    }
/*
 * isIndex
 * @param string full name of the table (incl. TABLE_PREFIX)
 * @param string name of the index to seek for
 * @return bool true if field exists
 */
    public function isIndex($table_name, $index_name, $number_fields = 0)
    {
        $number_fields = intval($number_fields);
        $keys = 0;
        $sql = 'SHOW INDEX FROM `'.$table_name.'`';
        if (($res_keys = $this->doQuery($sql))) {
            while (($rec_key = $res_keys->fetchAssoc())) {
                if ( $rec_key['Key_name'] == $index_name ) {
                    $keys++;
                }
            }

        }
        if ( $number_fields == 0 ) {
            return ($keys != $number_fields);
        } else {
            return ($keys == $number_fields);
        }
    }
/**
 * Alias for addField()
 * @deprecated from WB-2.8.5 and higher
 */
    public function field_add($table_name, $field_name, $description)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->addField($table_name, $field_name, $description);
    }
/*
 * @param string full name of the table (incl. TABLE_PREFIX)
 * @param string name of the field to add
 * @param string describes the new field like ( INT NOT NULL DEFAULT '0')
 * @return bool true if successful, otherwise false and error will be set
 */
    public function addField($table_name, $field_name, $description)
    {
        if (!$this->isField($table_name, $field_name)) {
        // add new field into a table
            $sql = 'ALTER TABLE `'.$table_name.'` ADD '.$field_name.' '.$description.' ';
            $doQuery = $this->doQuery($sql);
            $this->set_error(mysqli_error($this->oDbHandle));
            if (!$this->isError()) {
                return ( $this->isField($table_name, $field_name) ) ? true : false;
            }
        } else {
            $this->setError('field \''.$field_name.'\' already exists');
        }
        return false;
    }
/**
 * Alias for modifyField()
 * @deprecated from WB-2.8.5 and higher
 */
    public function field_modify($table_name, $field_name, $description)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->modifyField($table_name, $field_name, $description);
    }
/*
 * @param string $table_name: full name of the table (incl. TABLE_PREFIX)
 * @param string $field_name: name of the field to add
 * @param string $description: describes the new field like ( INT NOT NULL DEFAULT '0')
 * @return bool: true if successful, otherwise false and error will be set
 */
    public function modifyField($table_name, $field_name, $description)
    {
        $retval = false;
        if ($this->isField($table_name, $field_name)) {
        // modify a existing field in a table
            $sql  = 'ALTER TABLE `'.$table_name.'` MODIFY `'.$field_name.'` '.$description;
            $retval = ( $this->doQuery($sql) ? true : false);
            $this->setError(mysqli_error($this->oDbHandle));
        }
        return $retval;
    }
/**
 * Alias for removeField()
 * @deprecated from WB-2.8.5 and higher
 */
    public function field_remove($table_name, $field_name)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->removeField($table_name, $field_name);
    }
/*
 * @param string $table_name: full name of the table (incl. TABLE_PREFIX)
 * @param string $field_name: name of the field to remove
 * @return bool: true if successful, otherwise false and error will be set
 */
    public function removeField($table_name, $field_name)
    {
        $retval = false;
        if ($this->isField($table_name, $field_name)) {
        // modify a existing field in a table
            $sql  = 'ALTER TABLE `'.$table_name.'` DROP `'.$field_name.'`';
            $retval = ( $this->doQuery($sql, $this->oDbHandle) ? true : false );
        }
        return $retval;
    }
/**
 * Alias for addIndex()
 * @deprecated from WB-2.8.5 and higher
 */
    public function index_add($table_name, $index_name, $field_list, $index_type = 'KEY')
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->addIndex($table_name, $index_name, $field_list, $index_type);
    }
/*
 * @param string $table_name: full name of the table (incl. TABLE_PREFIX)
 * @param string $index_name: name of the new index (empty string for PRIMARY)
 * @param string $field_list: comma seperated list of fields for this index
 * @param string $index_type: kind of index (PRIMARY, UNIQUE, KEY, FULLTEXT)
 * @return bool: true if successful, otherwise false and error will be set
 */
    public function addIndex($table_name, $index_name, $field_list, $index_type = 'KEY')
    {
       $retval = false;
       $field_list = explode(',', (str_replace(' ', '', $field_list)));
       $number_fields = sizeof($field_list);
       $field_list = '`'.implode('`,`', $field_list).'`';
       $index_name = (($index_type == 'PRIMARY') ? $index_type : $index_name);
       if ( $this->isIndex($table_name, $index_name, $number_fields) ||
            $this->isIndex($table_name, $index_name))
       {
           $sql  = 'ALTER TABLE `'.$table_name.'` ';
           $sql .= 'DROP INDEX `'.$index_name.'`';
           if (!$this->doQuery($sql)) { return false; }
       }
       $sql  = 'ALTER TABLE `'.$table_name.'` ';
       $sql .= 'ADD '.$index_type.' ';
       $sql .= (($index_type == 'PRIMARY') ? 'KEY ' : '`'.$index_name.'` ');
       $sql .= '( '.$field_list.' ); ';
       if ($this->doQuery($sql)) { $retval = true; }
       return $retval;
   }
/**
 * Alias for removeIndex()
 * @deprecated from WB-2.8.5 and higher
 */
    public function index_remove($table_name, $index_name)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->removeIndex($table_name, $index_name);
    }
/*
 * @param string $table_name: full name of the table (incl. TABLE_PREFIX)
 * @param string $field_name: name of the field to remove
 * @return bool: true if successful, otherwise false and error will be set
 */
    public function removeIndex($table_name, $index_name)
    {
        $retval = false;
        if ($this->isIndex($table_name, $index_name)) {
        // modify a existing field in a table
            $sql  = 'ALTER TABLE `'.$table_name.'` DROP INDEX `'.$index_name.'`';
            $retval = ( $this->doQuery($sql) ? true : false );
        }
        return $retval;
    }
/**
 * Alias for importSql()
 * @deprecated from WB-2.8.5 and higher
 */
    public function SqlImport($sSqlDump,
                              $sTablePrefix = '',
                              $sAction      = 'install',
                              $sEngine      = 'MyISAM',
                              $sCollation   = 'utf8_unicode_ci')
    {
        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        $oImport = new SqlImport($this, $sSqlDump);
        return $oImport->doImport($sAction);
    }

/**
 * retuns the type of the engine used for requested table
 * @param string $table name of the table, including prefix
 * @return boolean/string false on error, or name of the engine (myIsam/InnoDb)
 */
    public function getTableEngine($table)
    {
        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        $retVal = false;
        $mysqlVersion = mysqli_get_server_info($this->oDbHandle);
        $engineValue = (version_compare($mysqlVersion, '5.0') < 0) ? 'Type' : 'Engine';
        $sql = 'SHOW TABLE STATUS FROM `' . $this->sDbName . '` LIKE \'' . $table . '\'';
        if (($result = $this->doQuery($sql))) {
            if (($row = $result->fetchAssoc())) {
                $retVal = $row[$engineValue];
            }
        }
        return $retVal;
    }
}

// end of class WbDatabaseHelper
/*--------------------------------------------------------------------------------------*/
/* this functions are placed inside this file temporarely until a better place is found */
/* function to update a var/value-pair(s) in table ****************************
 * nonexisting keys are inserted
 * @param string $table: name of table to use (without prefix)
 * @param mixed $key:    a array of key->value pairs to update
 *                       or a string with name of the key to update
 * @param string $value: a sting with needed value, if $key is a string too
 * @return bool:  true if any keys are updated, otherwise false
 */
    function updateDbKeyValue($table, $key, $value = '')
    {
        $oDb = WbDatabase::getInstance();
        $table = preg_replace('/^'.preg_quote($oDb->TablePrefix, '/').'/s', '', $table);
        if (!is_array($key)) {
            if (trim($key) != '') {
                $key = array( trim($key) => trim($value) );
            } else {
                $key = array();
            }
        }
        $retval = true;
        $sNameValPairs = '';
        foreach ($key as $index => $val) {
            $sNameValPairs .= ', (\''.$index.'\', \''.$oDb->escapeString($val).'\')';
        }
        $sValues = ltrim($sNameValPairs, ', ');
        if ($sValues != '') {
            $sql = 'REPLACE INTO `'.$oDb->TablePrefix.$table.'` (`name`, `value`) '
                 . 'VALUES '.$sValues;
            if (!$oDb->doQuery($sql)) {
                $retval = false;
            }
        }
        return $retval;
    }
/**
 * Alias for updateDbKeyValue()
 * @param string $table: name of table to use (without prefix)
 * @param mixed $key:    a array of key->value pairs to update
 *                        or a string with name of the key to update
 * @param string $value: a sting with needed value, if $key is a string too
 * @return bool:  true if any keys are updated, otherwise false
 * @deprecated from 2.8.4
 */
    function db_update_key_value($table, $key, $value = '')
    {
//        trigger_error('Deprecated function call: '.basename(__FILE__).'::'.__FUNCTION__, E_USER_DEPRECATED);
        return updateDbKeyValue($table, $key, $value);
    }

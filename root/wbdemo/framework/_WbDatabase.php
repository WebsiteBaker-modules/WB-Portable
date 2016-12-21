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
 * WbDatabase.php
 *
 * @category     Core
 * @package      Core_database
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.1.1
 * @revision     $Revision: 2131 $
 * @lastmodified $Date: 2015-06-23 13:38:43 +0200 (Di, 23. Jun 2015) $
 * @description  Mysqli database wrapper for use with websitebaker version 2.8.4
 */

/* -------------------------------------------------------- */
@define('DATABASE_CLASS_LOADED', true);

    define('MYSQLI_SEEK_LAST',            -1);
    define('MYSQLI_SEEK_FIRST',            0);
/* define the old mysql consts for Backward compatibility */
    if (!defined('MYSQL_ASSOC'))
    {
        define('MYSQL_SEEK_LAST',            -1);
        define('MYSQL_SEEK_FIRST',            0);
        define('MYSQL_ASSOC',                 1);
        define('MYSQL_NUM',                   2);
        define('MYSQL_BOTH',                  3);
        define('MYSQL_CLIENT_COMPRESS',      32);
        define('MYSQL_CLIENT_IGNORE_SPACE', 256);
        define('MYSQL_CLIENT_INTERACTIVE', 1024);
        define('MYSQL_CLIENT_SSL',         2048);
    }

class WbDatabase extends WbDatabaseHelper {

    private static $_oInstances = array();

    protected $oDbHandle    = null; // readonly from outside
    protected $sDbName      = '';
    protected $sInstanceIdentifier = '';
    protected $sTablePrefix = '';
    protected $sCharset     = '';
    protected $connected    = false;
    protected $error        = '';
    protected $error_type   = '';
    protected $iQueryCount  = 0;

/**
 * __constructor
 *  prevent from public instancing
 */
    final private function  __construct() {}
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
 * Establish connection
 * @param string $url
 * @return bool
 * @throws WbDatabaseException
 * @description opens a connection using connect URL<br />
 *              Example for SQL-Url:  'mysql://user:password@example.com[:3306]/database?charset=utf8&tableprefix=xx_'
 */
    public function doConnect($url = '')
    {
        if ($this->connected) { return $this->connected; } // prevent from reconnecting
        $this->connected = false;
        if ($url != '') {
        // parse URL and extract connection data
            $aIni = parse_url($url);
            $scheme   = isset($aIni['scheme']) ? $aIni['scheme'] : 'mysqli';
            $hostname = isset($aIni['host']) ? $aIni['host'] : '';
            $username = isset($aIni['user']) ? $aIni['user'] : '';
            $password = isset($aIni['pass']) ? $aIni['pass'] : '';
            $hostport = isset($aIni['port']) ? $aIni['port'] : '3306';
            $hostport = $hostport == '3306' ? null : $hostport;
            $db_name  = ltrim(isset($aIni['path']) ? $aIni['path'] : '', '/\\');
            $sTmp = isset($aIni['query']) ? $aIni['query'] : '';
            $aQuery = explode('&', $sTmp);
            foreach ($aQuery as $sArgument) {
                $aArg = explode('=', $sArgument);
                switch (strtolower($aArg[0])) {
                    case 'charset':
                        $this->sCharset = strtolower(preg_replace('/[^a-z0-9]/i', '', $aArg[1]));
                        break;
                    case 'tableprefix':
                        $this->sTablePrefix = $aArg[1];
                        break;
                    default:
                        break;
                }
            }
            $this->sDbName = $db_name;
        } else {
            throw new WbDatabaseException('Missing parameter: unable to connect database');
        }
        $this->oDbHandle = @mysqli_connect($hostname, $username, $password, $db_name, $hostport);
        if (!$this->oDbHandle) {
            throw new WbDatabaseException('unable to connect \''.$scheme.'://'.$hostname.':'.$hostport.'\'');
        } else {
            if ($this->sCharset) {
                @mysqli_query($this->oDbHandle, 'SET NAMES '.$this->sCharset);
                mysqli_set_charset($this->oDbHandle, $this->sCharset);
            }
            $this->connected = true;
        }
        return $this->connected;
    }
/**
 * disconnect database
 * @return bool
 * @description Disconnect current object from the database<br />
 *              the 'core' connection can NOT be disconnected!
 */
    public function disconnect()
    {
        if ($this->connected == true && $oInstance->sInstanceIdentifier != 'core') {
            mysqli_close($this->oDbHandle);
            $this->connected = false;
            return true;
        }
        return false;
    }
/**
 * Alias for doQuery()
 * @deprecated from WB-2.8.4 and higher
 */
    public function query($statement)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->doQuery($statement);
    }
/**
 * execute query
 * @param string $statement the SQL-statement to execute
 * @return null|\mysql
 */
    public function doQuery($statement) {
        $oRetval = null;
        $this->iQueryCount++;
        $mysql = new mysql($this->oDbHandle, $statement);
        $this->setError($mysql->error($this->oDbHandle));
        if (!$mysql->error()) {
            $oRetval = $mysql;
        }
        $this->setError($mysql->error($this->oDbHandle));
        return $oRetval;
    }
/**
 * Alias for getOne()
 * @deprecated from WB-2.8.4 and higher
 */
    public function get_one( $statement )
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->getOne($statement);
    }
    // Gets the first column of the first row
/**
 * Gets the first column of the first row
 * @param string $statement  SQL-statement
 * @return null|mixed
 */
    public function getOne( $sStatement )
    {
        $sRetval = null;
        if (($oRecSet = $this->doQuery($sStatement))) {
            if (($aRecord = $oRecSet->fetchArray(MYSQL_NUM))) {
                $sRetval = $aRecord[0];
            }
        }
        return ($this->isError() ? null : $sRetval);
    }
/**
 * Alias for setError()
 * @deprecated from WB-2.8.4 and higher
 */
    public function set_error($message = null)
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        $this->setError($message = null);
    }
    // Set the DB error
/**
 * setError
 * @param string $message
 */
    public function setError($message = null)
    {
        $this->error = $message;
    }
/**
 * Alias for isError
 * @deprecated from WB-2.8.4 and higher
 */
    public function is_error()
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->isError();
    }
/**
 * isError
 * @return bool
 */
    public function isError()
    {
        return (!empty($this->error)) ? true : false;
    }
/**
 * Alias for getError
 * @deprecated from WB-2.8.4 and higher
 */
    public function get_error()
    {
//        trigger_error('Deprecated function call: '.__CLASS__.'::'.__METHOD__, E_USER_DEPRECATED);
        return $this->getError();
    }
/**
 * get last Error
 * @return string
 */
    public function getError()
    {
        return $this->error;
    }
/**
 * Protect class from property injections
 * @param string name of property
 * @param mixed value
 * @throws WbDatabaseException
 */
    public function __set($name, $value)
    {
        throw new WbDatabaseException('tried to set a readonly or nonexisting property ['.$name.']!! ');
    }
/**
 * default Getter for some properties
 * @param string name of the Property
 * @return NULL on error | valid property
 */
    public function __get($sPropertyName)
    {
        switch ($sPropertyName) {
            case 'getDbHandle': // << set deprecated
            case 'db_handle': // << set deprecated
//                trigger_error('Deprecated property call: '.__CLASS__.'::'.__METHOD__.'(getDbHandle|db_handle)', E_USER_DEPRECATED);
            case 'DbHandle':
                $retval = $this->oDbHandle;
                break;
            case 'getLastInsertId': // << set deprecated
//                trigger_error('Deprecated property call: '.__CLASS__.'::'.__METHOD__.'(getLastInsertId)', E_USER_DEPRECATED);
            case 'LastInsertId':
                $retval = $this->getLastInsertId();
                break;
            case 'getDbName': // << set deprecated
            case 'db_name': // << set deprecated
//                trigger_error('Deprecated property call: '.__CLASS__.'::'.__METHOD__.'(getDbName|db_name)', E_USER_DEPRECATED);
            case 'DbName':
                $retval = $this->sDbName;
                break;
            case 'getTablePrefix': // << set deprecated
//                trigger_error('Deprecated property call: '.__CLASS__.'::'.__METHOD__.'(getTablePrefix)', E_USER_DEPRECATED);
            case 'TablePrefix':
                $retval = $this->sTablePrefix;
                break;
            case 'getQueryCount': // << set deprecated
//                trigger_error('Deprecated property call: '.__CLASS__.'::'.__METHOD__.'(getQueryCount)', E_USER_DEPRECATED);
            case 'QueryCount':
                $retval = $this->iQueryCount;
                break;
            default:
                $retval = null;
                break;
        }
        return $retval;
    } // __get()
/**
 * Escapes special characters in a string for use in an SQL statement
 * @param string $unescaped_string
 * @return string
 */
    public function escapeString($sUnescapedString)
    {
        return mysqli_real_escape_string($this->oDbHandle, $sUnescapedString);
    }
/**
 * Escapes wildchar characters in a string for use in an SQL-LIKE statement
 * @param string $unescaped_string
 * @return string
 */
    public function escapeLike($sUnescapedString)
    {
        return addcslashes($sUnescapedString, '_%');
    }
/**
 * Last inserted Id
 * @return bool|int false on error, 0 if no record inserted
 */
    public function getLastInsertId()
    {
        return mysqli_insert_id($this->oDbHandle);
    }

} /// end of class database
// //////////////////////////////////////////////////////////////////////////////////// //
/**
 * WbDatabaseException
 *
 * @category     Core
 * @package      Core_database
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      2.9.0
 * @revision     $Revision: 2131 $
 * @lastmodified $Date: 2015-06-23 13:38:43 +0200 (Di, 23. Jun 2015) $
 * @description  Exceptionhandler for the WbDatabase and depending classes
 */
class WbDatabaseException extends AppException {}

/**
 * mysql
 *
 * @category     Core
 * @package      Core_database
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      2.9.0
 * @revision     $Revision: 2131 $
 * @lastmodified $Date: 2015-06-23 13:38:43 +0200 (Di, 23. Jun 2015) $
 * @description  MYSQL result object for requests
 *
 */
class mysql {

    private $result    = null;
    private $oDbHandle = null;
    private $error     = '';

    public function __construct($oHandle, $sStatement)
    {
        $this->oDbHandle = $oHandle;
        $this->query($sStatement);
    }
/**
 * query sql statement
 * @param  string $statement
 * @return object
 * @throws WbDatabaseException
 */
    public function query($sStatement)
    {
        $this->result = @mysqli_query($this->oDbHandle, $sStatement);
        if ($this->result === false) {
            if (DEBUG) {
                throw new WbDatabaseException(mysqli_error($this->oDbHandle));
            } else {
                throw new WbDatabaseException('Error in SQL-Statement');
            }
        }
        $this->error = mysqli_error($this->oDbHandle);
        return $this->result;
    }
/**
 * numRows
 * @return integer
 * @description number of returned records
 */
    public function numRows()
    {
        return mysqli_num_rows($this->result);
    }
/**
 * fetchRow
 * @param  int $typ MYSQL_BOTH(default) | MYSQL_ASSOC | MYSQL_NUM // DEPRECATED
 * @return array with numeric indexes
 * @description get current record and increment pointer
 */
    public function fetchRow($typ = MYSQLI_BOTH)
    {
        if ($typ != MYSQLI_NUM) {
//            trigger_error('Deprecated call: '.__CLASS__.'::'.__METHOD__.' for MYSQLI_ASSOC|MYSQL_BOTH', E_USER_DEPRECATED);
            return mysqli_fetch_array($this->result, $typ);
        } else {
            return mysqli_fetch_row($this->result);
        }
    }
/**
 * fetchAssoc
 * @return array with assotiative indexes
 * @description get current record and increment pointer
 */
    public function fetchAssoc()
    {
        return mysqli_fetch_assoc($this->result);
    }
/**
 * fetchArray
 * @param  int $iType MYSQL_ASSOC(default) | MYSQL_BOTH | MYSQL_NUM
 * @return array of current record
 * @description get current record and increment pointer
 */
    public function fetchArray($iType = MYSQLI_ASSOC)
    {
        if ($iType < MYSQLI_ASSOC || $iType > MYSQLI_BOTH) {
            $iType = MYSQLI_ASSOC;
        }
        return mysqli_fetch_array($this->result, $iType);
    }
/**
 * fetchObject
 * @param  string $sClassname Name of the class to use. Is no given use stdClass
 * @param  string $aParams    optional array of arguments for the constructor
 * @return object
 * @description get current record as an object and increment pointer
 */
    public function fetchObject($sClassName = null, array $aParams = null)
    {
        if ($sClassName === null || class_exists($sClassName)) {
            return mysqli_fetch_object($this->result, $sClassName, $aParams);
        } else {
            throw new WbDatabaseException('Class <'.$sClassName.'> not available on request of mysqli_fetch_object()');
        }
    }
/**
 * fetchAll
 * @param  int $iType MYSQL_ASSOC(default) | MYSQL_NUM
 * @return array of rows
 * @description get all records of the result set
 */
    public function fetchAll($iType = MYSQLI_ASSOC)
    {
        $iType = $iType != MYSQLI_NUM ? MYSQLI_ASSOC : MYSQLI_NUM;

        if (function_exists('mysqli_fetch_all')) { # Compatibility layer with PHP < 5.3
            $aRetval = mysqli_fetch_all($this->result, $iType);
        } else {
            for ($aRetval = array(); ($aTmp = mysqli_fetch_array($this->result, $iType));) { $aRetval[] = $aTmp; }
        }
        return $aRetval;
//        return mysqli_fetch_all($this->result, $iType);
    }
/**
 * rewind
 * @return bool
 * @description set the recordpointer to the first record || false on error
 */
    public function rewind()
    {
        return $this->seekRow(MYSQLI_SEEK_FIRST);
    }
/**
 * seekRow
 * @param int $position also can be MYSQLI_SEEK_FIRST||MYSQLI_SEEK_LAST
 * @return bool
 * @description set the pointer to the given record || false on error
 */
    public function seekRow( $position = MYSQLI_SEEK_FIRST )
    {
        $pmax = $this->numRows() - 1;
        $p = (($position < 0 || $position > $pmax) ? $pmax : $position);
        return mysqli_data_seek($this->result, $p);
    }
/**
 * freeResult
 * @return bool
 * @description remove retult object from memeory
 */
    public function freeResult()
    {
        return mysqli_free_result($this->result);
    }
/**
 * Get error
 * @return string || null if no error
 */
    public function error()
    {
        if (isset($this->error)) {
            return $this->error;
        } else {
            return null;
        }
    }

}

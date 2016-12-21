<?php


 class SP7Database
{
    private static $_oInstances = array();
    protected $oDb          = null;
/*
    protected $bWbDbType    = true;
    protected $sTablePrefix = '';
    protected $getOne       = '';
    protected $fetchArray   = '';
    protected $fetchRow     = '';
    protected $isError      = '';
    protected $is_error      = '';
    protected $getError      = '';
    protected $get_error      = '';
*/
    public function __construct()
    {
        if (class_exists('WbDdatabase', false)) {
            $this->oDb = WbDatabase::getInstance();
            $this->sTablePrefix = $this->oDb->TablePrefix;
//                $this->getOne       = 'getOne';
//                $this->fetchArray   = 'fetchArray';
//                $this->fetchRow     = 'fetchArray';
        } else {
            $this->oDb = $GLOBALS['database'];
            $this->sTablePrefix = TABLE_PREFIX;
//                $this->getOne       = 'get_one';
//                $this->fetchArray   = 'fetchArray';
//                $this->fetchRow     = 'fetchRow';
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
            $retval = $this->getLastInsertId();
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

    public function is_error()
    {
        return $this->isError();
    }

    public function isError()
    {
        return (!empty($this->error)) ? true : false;
        if (class_exists('WbDdatabase', false)) {
            return $this->oDb->getError();
        } else {
            return $this->oDb->is_error();
        }
    }

    public function get_error()
    {
        return $this->getError();
//        if (class_exists('WbDdatabase', false)) {
//            return $this->oDb->getError();
//        } else {
//            return $this->oDb->is_error();
//        }
    }

    public function getError()
    {
        if (class_exists('WbDdatabase', false)) {
            return $this->oDb->getError();
        } else {
            return $this->oDb->is_error();
        }
    }


    public function get_one ($sql=''){
        return $this->getOne($sql);
//        if (class_exists('WbDdatabase', false)) {
//            return $this->oDb->getOne($sql);
//        } else {
//            return $this->oDb->get_one($sql);
//        }
    }

    public function getOne ($sql=''){
        if (class_exists('WbDdatabase', false)) {
            return $this->oDb->getOne($sql);
        } else {
            return $this->oDb->get_one($sql);
        }
    }

    public function query ($sql=''){
        return $this->doQuery($sql);
//        if (class_exists('WbDdatabase', false)) {
//            return $this->oDb->doQuery($sql);
//        } else {
//            return $this->oDb->query($sql);
//        }
    }

    public function doQuery ($sql=''){
        if (class_exists('WbDdatabase', false)) {
            return $this->oDb->doQuery($sql);
        } else {
            return $this->oDb->query($sql);
        }
    }

    public function fetchArray ($typ = MYSQLI_BOTH){
        if (class_exists('WbDdatabase', false)) {
            return $this->oDb->fetchArray($typ);
        } else {
            return $this->fetchRow($typ);
        }
    }

    public function fetchRow($typ = MYSQLI_BOTH){
        if (class_exists('WbDdatabase', false)) {
            return $this->oDb->fetchArray();
        } else {
            return $this->oDb->fetchRow($typ);
        }
    }

public function escapeString($unescaped_string)
{
    return mysqli_real_escape_string($this->db_handle, $unescaped_string);
}

public function getLastInsertId()
{
    return mysqli_insert_id($this->db_handle);
}

} // end of SP7Database
/*---------------------------------------------------------------------------------------*/
    $_SESSION['database'] = $database;
    $database = new SP7Database();

$class_methods = get_class_methods($database);
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $class_methods ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
/*
*/

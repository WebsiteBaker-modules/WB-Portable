<?php
/**
 *
 * @category        framework
 * @package         initialize
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: initialize.php 1638 2012-03-13 23:01:47Z darkviper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/framework/initialize.php $
 * @lastmodified    $Date: 2012-03-14 00:01:47 +0100 (Mi, 14. Mrz 2012) $
 *
 */

$sStarttime = array_sum(explode(" ", microtime()));
$aPhpFunctions = get_defined_functions();
/**
 * sanitize $_SERVER['HTTP_REFERER']
 * @param string $sWbUrl qualified startup URL of current application
 */
function SanitizeHttpReferer($sWbUrl = WB_URL) {
    $sTmpReferer = '';
    if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
        define('ORG_REFERER', ($_SERVER['HTTP_REFERER'] ?: ''));
        $aRefUrl = parse_url($_SERVER['HTTP_REFERER']);
        if ($aRefUrl !== false) {
            $aRefUrl['host'] = isset($aRefUrl['host']) ? $aRefUrl['host'] : '';
            $aRefUrl['path'] = isset($aRefUrl['path']) ? $aRefUrl['path'] : '';
            $aRefUrl['fragment'] = isset($aRefUrl['fragment']) ? '#'.$aRefUrl['fragment'] : '';
            $aWbUrl = parse_url(WB_URL);
            if ($aWbUrl !== false) {
                $aWbUrl['host'] = isset($aWbUrl['host']) ? $aWbUrl['host'] : '';
                $aWbUrl['path'] = isset($aWbUrl['path']) ? $aWbUrl['path'] : '';
                if (strpos($aRefUrl['host'].$aRefUrl['path'], $aWbUrl['host'].$aWbUrl['path']) !== false) {
                    $aRefUrl['path'] = preg_replace('#^'.$aWbUrl['path'].'#i', '', $aRefUrl['path']);
                    $sTmpReferer = WB_URL.$aRefUrl['path'].$aRefUrl['fragment'];
                }
                unset($aWbUrl);
            }
            unset($aRefUrl);
        }
    }
    $_SERVER['HTTP_REFERER'] = $sTmpReferer;
}
/**
 * makePhExp
 * @param array list of names for placeholders
 * @return array reformatted list
 * @description makes an RegEx-Expression for preg_replace() of each item in $aList
 *              Example: from 'TEST_NAME' it mades '/\[TEST_NAME\]/s'
 */
function makePhExp($sList)
{
    $aList = func_get_args();
//    return preg_replace('/^(.*)$/', '/\[$1\]/s', $aList);
    return preg_replace('/^(.*)$/', '[$1]', $aList);
}

/* ***************************************************************************************
 * Start initialization                                                                  *
 ****************************************************************************************/// aktivate exceptionhandler ---
//    throw new Exception('PHP-'.PHP_VERSION.' found, but at last PHP-5.3.6 required !!');
// Stop execution if PHP version is too old
if (version_compare(PHP_VERSION, '5.3.6', '<')) {
// PHP less then 5.3.6 is prohibited ---
    if (version_compare(PHP_VERSION, '5.3.6', '<')) {
        $sMsg = '<p style="color: #ff0000;">WebsiteBaker is not able to run with PHP-Version less then 5.3.6!!<br />'
              . 'Please change your PHP-Version to any kind from 5.3.6 and up!<br />'
              . 'If you have problems to solve that, ask your hosting provider for it.<br  />'
              . 'The very best solution is the use of PHP-5.5 and up</p>';
        die($sMsg);
    }
}

/* -------------------------------------------------------- */
if ( !defined('WB_PATH')) { define('WB_PATH', dirname(__DIR__)); }
// *** initialize Exception handling
if(!function_exists('globalExceptionHandler')) {
    include(__DIR__.'/globalExceptionHandler.php');
}
// *** initialize Error handling
$sErrorLogFile = dirname(__DIR__).'/var/logs/php_error.log';
if (ini_get('display_errors')) {
    ini_set('display_errors', 'off');
}
if (!file_exists($sErrorLogFile)) {
    file_put_contents($sErrorLogFile, 'created: ['.date('c').']'.PHP_EOL, FILE_APPEND);
}
ini_set('log_errors', 1);
ini_set ('error_log', $sErrorLogFile);

/**
 * Read DB settings from configuration file
 * @return array
 * @throws RuntimeException
 *
 */
function initReadSetupFile()
{
// check for valid file request. Becomes more stronger in next version
//    initCheckValidCaller(array('save.php','index.php','config.php','upgrade-script.php'));
    $aCfg = array();
    $sSetupFile = dirname(dirname(__FILE__)).'/setup.ini.php';
    if(is_readable($sSetupFile) && !defined('WB_URL')) {
        $aCfg = parse_ini_file($sSetupFile, true);
        if (!isset($aCfg['Constants']) || !isset($aCfg['DataBase'])) {
            throw new InvalidArgumentException('configuration missmatch in setup.ini.php');
        }
        foreach($aCfg['Constants'] as $key=>$value) {
            switch($key):
                case 'DEBUG':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    if(!defined('DEBUG')) { define('DEBUG', $value); }
                    break;
                case 'WB_URL': // << case is set deprecated
                case 'AppUrl':
                    $value = trim(str_replace('\\', '/', $value), '/');
                    if(!defined('WB_URL')) { define('WB_URL', $value); }
                    break;
                case 'ADMIN_DIRECTORY': // << case is set deprecated
                case 'AcpDir':
                    $value = trim(str_replace('\\', '/', $value), '/');
                    if(!defined('ADMIN_DIRECTORY')) { define('ADMIN_DIRECTORY', $value); }
                    break;
                default:
                    if(!defined($key)) { define($key, $value); }
                    break;
            endswitch;
        }
    }
    return $aCfg;
//      throw new RuntimeException('unable to read setup.ini.php');
}
/**
 * Set constants for system/install values
 * @throws RuntimeException
 */
function initSetInstallWbConstants($aCfg) {
    if (sizeof($aCfg)) {
        foreach($aCfg['Constants'] as $key=>$value) {
            switch($key):
                case 'DEBUG':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    if(!defined('DEBUG')) { define('DEBUG', $value); }
                    break;
                case 'WB_URL': // << case is set deprecated
                case 'AppUrl':
                    $value = trim(str_replace('\\', '/', $value), '/');
                    if(!defined('WB_URL')) { define('WB_URL', $value); }
                    break;
                case 'ADMIN_DIRECTORY': // << case is set deprecated
                case 'AcpDir':
                    $value = trim(str_replace('\\', '/', $value), '/');
                    if(!defined('ADMIN_DIRECTORY')) { define('ADMIN_DIRECTORY', $value); }
                    if(!preg_match('/xx[a-z0-9_][a-z0-9_\-\.]+/i', 'xx'.ADMIN_DIRECTORY)) {
                        throw new RuntimeException('Invalid admin-directory: ' . ADMIN_DIRECTORY);
                    }
                    break;
                default:
                    if(!defined($key)) { define($key, $value); }
                    break;
            endswitch;
        }
    }
    if(!defined('WB_PATH')){ define('WB_PATH', dirname(__DIR__)); }
    if(!defined('ADMIN_URL')){ define('ADMIN_URL', rtrim(WB_URL, '/\\').'/'.ADMIN_DIRECTORY); }
    if(!defined('ADMIN_PATH')){ define('ADMIN_PATH', WB_PATH.'/'.ADMIN_DIRECTORY); }
    if(!defined('WB_REL')){
        $x1 = parse_url(WB_URL);
        define('WB_REL', (isset($x1['path']) ? $x1['path'] : ''));
    }
    if(!defined('ADMIN_REL')){ define('ADMIN_REL', WB_REL.'/'.ADMIN_DIRECTORY); }
    if(!defined('DOCUMENT_ROOT')) {
        define('DOCUMENT_ROOT', preg_replace('/'.preg_quote(str_replace('\\', '/', WB_REL), '/').'$/', '', str_replace('\\', '/', WB_PATH)));
        $_SERVER['DOCUMENT_ROOT'] = DOCUMENT_ROOT;
    }
    if(!defined('TMP_PATH')){ define('TMP_PATH', WB_PATH.'/temp'); }

    if (defined('DB_TYPE'))
    {
    // import constants for compatibility reasons
        $db = array();
        if (defined('DB_TYPE'))      { $db['type']         = DB_TYPE; }
        if (defined('DB_USERNAME'))  { $db['user']         = DB_USERNAME; }
        if (defined('DB_PASSWORD'))  { $db['pass']         = DB_PASSWORD; }
        if (defined('DB_HOST'))      { $db['host']         = DB_HOST; }
        if (defined('DB_PORT'))      { $db['port']         = DB_PORT; }
        if (defined('DB_NAME'))      { $db['name']         = DB_NAME; }
        if (defined('DB_CHARSET'))   { $db['charset']      = DB_CHARSET; }
        if (defined('TABLE_PREFIX')) { $db['table_prefix'] = TABLE_PREFIX; }
    } else {
        foreach($aCfg['DataBase'] as $key=>$value) {
            switch($key):
                case 'type':
                    if(!defined('DB_TYPE')) { define('DB_TYPE', $value); }
                    break;
                case 'user':
                    if(!defined('DB_USERNAME')) { define('DB_USERNAME', $value); }
                    break;
                case 'pass':
                    if(!defined('DB_PASSWORD')) { define('DB_PASSWORD', $value); }
                    break;
                case 'host':
                    if(!defined('DB_HOST')) { define('DB_HOST', $value); }
                    break;
                case 'port':
                    if(!defined('DB_PORT')) { define('DB_PORT', $value); }
                    break;
                case 'name':
                    if(!defined('DB_NAME')) { define('DB_NAME', $value); }
                    break;
                case 'charset':
                    if(!defined('DB_CHARSET')) { define('DB_CHARSET', $value); }
                    break;
                default:
                    $key = strtoupper($key);
                    if(!defined($key)) { define($key, $value); }
                    break;
            endswitch;
        }
    }
}

/**
 * WbErrorHandler()
 *
 * @param mixed $iErrorCode
 * @param mixed $sErrorText
 * @param mixed $sErrorFile
 * @param mixed $iErrorLine
 * @return
 */
function WbErrorHandler($iErrorCode, $sErrorText, $sErrorFile, $iErrorLine)
{
     if (!(error_reporting() & $iErrorCode) || ini_get('log_errors') == 0) {
        return false;
    }
    $bRetval = false;
    $sErrorLogFile = ini_get ('error_log');
    if (!is_writeable($sErrorLogFile)){return false;}
    $sErrorType = E_NOTICE ;
    $aErrors = array(
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        E_USER_NOTICE     => 'E_USER_NOTICE',
        E_USER_WARNING    => 'E_USER_WARNING',
        E_DEPRECATED      => 'E_DEPRECATED',
        E_NOTICE          => 'E_NOTICE',
        E_WARNING         => 'E_WARNING',
        E_CORE_WARNING    => 'E_CORE_WARNING',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_STRICT          => 'E_STRICT',
    );
    if (array_key_exists($iErrorCode, $aErrors)) {
        $sErrorType = $aErrors[$iErrorCode];
        $bRetval = true;
    }
    $aBt= debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $x = sizeof($aBt) -1;
    $x = $x < 0 ? 0 : ($x <= 2 ? $x : 2);
    $sEntry = date('c').' '.'['.$sErrorType.'] '.str_replace(dirname(__DIR__), '', $sErrorFile).':['.$iErrorLine.'] '
            . ' from '.str_replace(dirname(__DIR__), '', $aBt[$x]['file']).':['.$aBt[$x]['line'].'] '
            . (@$aBt[$x]['class'] ? $aBt[$x]['class'].$aBt[$x]['type'] : '').$aBt[$x]['function'].' '
            . '"'.$sErrorText.'"'.PHP_EOL;
    file_put_contents($sErrorLogFile, $sEntry, FILE_APPEND);
    return $bRetval;
}
/* ***************************************************************************************
 * Start initialization                                                                  *
 ****************************************************************************************/
// activate errorhandler
    set_error_handler('WbErrorHandler');
    if (! defined('SYSTEM_RUN')) { define('SYSTEM_RUN', true); }
// load configuration ---
    $aCfg = initReadSetupFile();
    initSetInstallWbConstants($aCfg);
// ---------------------------
// get Database connection data from configuration

/*
    $aSqlData = initGetDbConnectData($aCfg, $sDbConnectType);
// Create global database instance ---
// remove critical data from memory
    unset($aSqlData, $aCfg);
    $oDb = $database = WbDatabase::getInstance();
    if($sDbConnectType == 'dsn') {
        $bTmp = $oDb->doConnect($aSqlData['dsn'], $aSqlData['user'], $aSqlData['password'], null, $aSqlData['addons']);
    }else {
        $bTmp = $oDb->doConnect($aSqlData['url']);
    }
    if(!defined('TABLE_PREFIX')) { define('TABLE_PREFIX', $oDb->TablePrefix); }
*/
//
if (!defined('ADMIN_DIRECTORY')) { define('ADMIN_DIRECTORY', 'admin'); }
if (!preg_match('/xx[a-z0-9_][a-z0-9_\-\.]+/i', 'xx'.ADMIN_DIRECTORY)) {
    throw new RuntimeException('Invalid admin-directory: ' . ADMIN_DIRECTORY);
}
if ( !defined('ADMIN_URL')) { define('ADMIN_URL', WB_URL.'/'.ADMIN_DIRECTORY); }
if ( !defined('ADMIN_PATH')) { define('ADMIN_PATH', WB_PATH.'/'.ADMIN_DIRECTORY); }
if ( !defined('WB_REL')){
    $x1 = parse_url(WB_URL);
    define('WB_REL', (isset($x1['path']) ? $x1['path'] : ''));
}
if ( !defined('DOCUMENT_ROOT')) {
    define('DOCUMENT_ROOT', preg_replace('/'.preg_quote(str_replace('\\', '/', WB_REL), '/').'$/', '', str_replace('\\', '/', WB_PATH)));
    $_SERVER['DOCUMENT_ROOT'] = DOCUMENT_ROOT;
}
if (file_exists(WB_PATH.'/framework/class.database.php')) {
    // sanitize $_SERVER['HTTP_REFERER']
    SanitizeHttpReferer(WB_URL);
    date_default_timezone_set('UTC');
    // register TWIG autoloader ---
    $sTmp = dirname(dirname(__FILE__)).'/include/Sensio/Twig/lib/Twig/Autoloader.php';
    if (!class_exists('Twig_Autoloader') && is_readable($sTmp)){
        include $sTmp;
        Twig_Autoloader::register();
    }
// register PHPMailer autoloader ---
    $sTmp = dirname(dirname(__FILE__)).'/include/phpmailer/PHPMailerAutoload.php';
    if (!function_exists('PHPMailerAutoload') && is_readable($sTmp)) {
        require($sTmp);
    }

    if (!class_exists('database', false)){
      // load database class
      require(__DIR__.'/class.database.php');
      // Create database class
      $database = new database();
    }

/*
    require_once(WB_PATH.'/framework/class.database.php');
    $database = new database();
*/
    // activate frontend OutputFilterApi (initialize.php)
    if (is_readable(WB_PATH .'/modules/output_filter/OutputFilterApi.php')) {
        if (!function_exists('OutputFilterApi')) {
            include WB_PATH .'/modules/output_filter/OutputFilterApi.php';
        }
    } else {
        throw new RuntimeException('missing mandatory global OutputFilterApi!');
    }
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        @ini_set("magic_quotes_runtime", 0); // Disable magic_quotes_runtime
        @ini_set("magic_quotes_gpc", 0); // Disable magic_quotes_gpc
    }
    if (get_magic_quotes_gpc()) {
        $unescape = function(&$value, $key) {
            $value = stripslashes($value);
        };
        array_walk_recursive($_POST, $unescape);
        array_walk_recursive($_GET,  $unescape);
        array_walk_recursive($_REQUEST, $unescape);
        array_walk_recursive($_COOKIE, $unescape);
    }
    // Get website settings (title, keywords, description, header, and footer)
    $sql = 'SELECT `name`, `value` FROM `'.TABLE_PREFIX.'settings`';
    if (($get_settings = $database->query($sql))) {
        $x = 0;
        while ($setting = $get_settings->fetchRow(MYSQLI_ASSOC)) {
            $setting_name  = strtoupper($setting['name']);
            $setting_value = $setting['value'];
            if ($setting_value == 'false') {
                $setting_value = false;
            }
            if ($setting_value == 'true') {
                $setting_value = true;
            }
            @define($setting_name, $setting_value);
            $x++;
        }
    } else {
        die($database->get_error());
    }
    if (!$x) {
        throw new RuntimeException('no settings found');
    }
    @define('DO_NOT_TRACK', (isset($_SERVER['HTTP_DNT'])));

    if (!defined('DEBUG')){ define('DEBUG', false); }
    $string_file_mode = defined('STRING_FILE_MODE')?STRING_FILE_MODE:'0644';
    @define('OCTAL_FILE_MODE',(int) octdec($string_file_mode));
    $string_dir_mode = defined('STRING_DIR_MODE')?STRING_DIR_MODE:'0755';
    @define('OCTAL_DIR_MODE',(int) octdec($string_dir_mode));
//    $sSecMod = (defined('SECURE_FORM_MODULE') && SECURE_FORM_MODULE != '') ? '.'.SECURE_FORM_MODULE : '';
//    $sSecMod = WB_PATH.'/framework/SecureForm'.$sSecMod.'.php';
//    require_once($sSecMod);
    if (!defined("WB_INSTALL_PROCESS")) {
    // get CAPTCHA and ASP settings
        $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_captcha_control`';
        if (($get_settings = $database->query($sql)) &&
            ($setting = $get_settings->fetchRow(MYSQLI_ASSOC))
        ) {
            @define('ENABLED_CAPTCHA', (($setting['enabled_captcha'] == '1') ? true : false));
            @define('ENABLED_ASP', (($setting['enabled_asp'] == '1') ? true : false));
            @define('CAPTCHA_TYPE', $setting['captcha_type']);
            @define('ASP_SESSION_MIN_AGE', (int)$setting['asp_session_min_age']);
            @define('ASP_VIEW_MIN_AGE', (int)$setting['asp_view_min_age']);
            @define('ASP_INPUT_MIN_AGE', (int)$setting['asp_input_min_age']);
        } else {
            throw new RuntimeException('CAPTCHA-Settings not found');
        }
    }
    // set error-reporting
    if (intval(ER_LEVEL) > 0 ) {
        error_reporting( ER_LEVEL );
        if (intval(ini_get ( 'display_errors' )) == 0 ) {
            ini_set('display_errors', 0);
        }
    }
    // Start a session
    if (!defined('SESSION_STARTED')) {
        session_name(APP_NAME.'-sid');
        @session_start();
        define('SESSION_STARTED', true);
    }
    if (defined('ENABLED_ASP') && ENABLED_ASP && !isset($_SESSION['session_started'])) {
        $_SESSION['session_started'] = time();
    }
    // Get users language
    if (
        isset($_GET['lang']) AND
        $_GET['lang'] != '' AND
        !is_numeric($_GET['lang']) AND
        strlen($_GET['lang']) == 2
    ) {
        define('LANGUAGE', strtoupper($_GET['lang']));
        $_SESSION['LANGUAGE']=LANGUAGE;
    } else {
        if (isset($_SESSION['LANGUAGE']) AND $_SESSION['LANGUAGE'] != '') {
            define('LANGUAGE', $_SESSION['LANGUAGE']);
        } else {
            define('LANGUAGE', DEFAULT_LANGUAGE);
        }
    }
    $sCachePath = dirname(__DIR__).'/temp/cache/';
    if (!file_exists($sCachePath)) {
        if (!mkdir($sCachePath)) { $sCachePath = dirname(__DIR__).'/temp/'; }
    }
    // Load Language file(s)
    $sCurrLanguage = '';
    $slangFile = WB_PATH.'/languages/EN.php';
    if (is_readable($slangFile)) {
        require $slangFile;
        $sCurrLanguage ='EN';
    }
    if ($sCurrLanguage != DEFAULT_LANGUAGE) {
        $slangFile = WB_PATH.'/languages/'.DEFAULT_LANGUAGE.'.php';
        if (is_readable($slangFile)) {
            require $slangFile;
            $sCurrLanguage = DEFAULT_LANGUAGE;
        }
    }
    if ($sCurrLanguage != LANGUAGE) {
        $slangFile = WB_PATH.'/languages/'.LANGUAGE.'.php';
        if (is_readable($slangFile)) {
            require $slangFile;
        }
    }
    if (!class_exists('Translate', false)) {
        include __DIR__.'/Translate.php';
    }
    $oTrans = Translate::getInstance();
    $oTrans->initialize(array('EN', DEFAULT_LANGUAGE, LANGUAGE), $sCachePath); // 'none'
    // Get users timezone
    if (isset($_SESSION['TIMEZONE'])) {
        define('TIMEZONE', $_SESSION['TIMEZONE']);
    } else {
        define('TIMEZONE', DEFAULT_TIMEZONE);
    }
    // Get users date format
    if (isset($_SESSION['DATE_FORMAT'])) {
        define('DATE_FORMAT', $_SESSION['DATE_FORMAT']);
    } else {
        define('DATE_FORMAT', DEFAULT_DATE_FORMAT);
    }
    // Get users time format
    if (isset($_SESSION['TIME_FORMAT'])) {
        define('TIME_FORMAT', $_SESSION['TIME_FORMAT']);
    } else {
        define('TIME_FORMAT', DEFAULT_TIME_FORMAT);
    }
    // Set Theme dir
    define('THEME_URL', WB_URL.'/templates/'.DEFAULT_THEME);
    define('THEME_PATH', WB_PATH.'/templates/'.DEFAULT_THEME);
    // extended wb_settings
    define('EDIT_ONE_SECTION', false);
    define('EDITOR_WIDTH', 0);
}

function newAdmin($section_name= '##skip##', $section_permission = 'start', $auto_header = true, $auto_auth = true)
{
    if (isset($GLOBALS['admin']) && $GLOBALS['admin'] instanceof admin) {
        unset($GLOBALS['admin']);
        usleep(10000);
    }
    return new admin($section_name, $section_permission, $auto_header, $auto_auth);
}

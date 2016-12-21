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
 * WbAdaptor.php
 *
 * @category     Core
 * @package      Core_package
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.0.1
 * @revision     $Revision: 2120 $
 * @lastmodified $Date: 2015-03-10 18:16:46 +0100 (Di, 10 Mrz 2015) $
 * @since        File available since 18.01.2013
 * @deprecated   This class will be removed if Registry comes activated
 * @description  This adaptor is a temporary replacement for the future registry class
 */
class WbAdaptor {

/** active instance */
    protected static $oInstance = null;
/** array hold settings */
    protected $aProperties = array();
/**  */
    protected $aObjects = array('Db' => null, 'Trans' => null, 'App' => null);
/** vars which             */
    protected $aReservedVars = array('Db', 'Trans', 'App');
/** constructor */
    protected function __construct()
    {
        $this->aProperties = array('System' => array(), 'Request' => array());
    }
/**
 * Get active instance
 * @return WbAdaptor
 */
    public static function getInstance()
    {
        if(self::$oInstance == null) {
            $c = __CLASS__;
            self::$oInstance = new $c();

        }
        return self::$oInstance;
    }
/**
 * set the global database object
 * @param WbDatabase $oDb
 */
    public function setDatabase(WbDatabase $oDb)
    {
        $this->aObjects['Db'] = $oDb;
        return true;
    }
/**
 * set the global translation object
 * @param Translate $oTrans
 */
    public function setTranslate(Translate $oTrans)
    {
        $this->aObjects['Trans'] = $oTrans;
        return true;
    }
/**
 * set the global application object
 * @param wb $oApp
 */
    public function setApplication(wb $oApp)
    {
        $this->aObjects['App'] = $oApp;
    }
/**
 * handle unknown properties
 * @param string name of the property
 * @param mixed value to set
 * @throws InvalidArgumentException
 */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->aProperties['System'])) {
            throw new InvalidArgumentException('tried to set readonly or nonexisting property [ '.$name.' }!! ');
        } else {
            $this->aProperties['Request'][$name] = $value;
        }
    }
/**
 * Get value of a variable
 * @param string name of the variable
 * @return mixed
 */
    public function __get($sVarname)
    {
        if (isset($this->aObjects[$sVarname]) && !is_null($this->aObjects[$sVarname])) {
            return $this->aObjects[$sVarname];
        }
        if (isset($this->aProperties['System'][$sVarname])) {
            return $this->aProperties['System'][$sVarname];
        } elseif ( isset($this->aProperties['Request'][$sVarname])) {
            return $this->aProperties['Request'][$sVarname];
        } else {
            return null;
        }
    }
/**
 * Check if var is set
 * @param string name of the variable
 * @return bool
 */
    public function __isset($sVarname)
    {
        if (isset($this->aObjects[$sVarname]) && !is_null($this->aObjects[$sVarname])) {
            return true;
        }
        $bRetval = (isset($this->aProperties['System'][$sVarname]) ||
                    isset($this->aProperties['Request'][$sVarname]));
        return $bRetval;
    }
/**
 * Import WB-Constants
 */
    public function getWbConstants()
    {
    // first reinitialize arrays
        $this->aProperties = array(
            'System' => array(),
            'Request' => array()
        );
    // get all defined constants
        $aConsts = get_defined_constants(true);
    // iterate all user defined constants
        foreach ($aConsts['user'] as $sKey=>$sVal) {
            if (in_array($sKey, $this->aReservedVars)) { continue; }
        // skip possible existing database constants
            if (preg_match('/^db_|^TABLE_PREFIX$/i', $sKey)) { continue; }
        // change all path items to trailing slash scheme and assign the new naming syntax
            switch($sKey):
                case 'DEBUG':
                    $this->aProperties['System']['Debug'] = intval($sVal);
                    $this->aProperties['System']['DebugLevel'] = intval($sVal);
                    break;
                case 'WB_URL':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AppUrl';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'WB_REL':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AppRel';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'WB_PATH':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AppPath';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'ADMIN_URL':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AcpUrl';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'ADMIN_REL':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AcpRel';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'ADMIN_PATH':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AcpPath';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'THEME_URL':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'ThemeUrl';
                    $this->aProperties['Request'][$sKey] = $sVal;
                    break;
                case 'THEME_REL':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'ThemeRel';
                    $this->aProperties['Request'][$sKey] = $sVal;
                    break;
                case 'THEME_PATH':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'ThemePath';
                    $this->aProperties['Request'][$sKey] = $sVal;
                    break;
                case 'TMP_PATH':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'TempPath';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'ADMIN_DIRECTORY':
                    $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'AcpDir';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'DOCUMENT_ROOT':
                    $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'DocumentRoot';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'PAGES_DIRECTORY':
                    $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                    $sVal = $sVal=='/' ? '' : $sVal;
                    $sKey = 'PagesDir';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'MEDIA_DIRECTORY':
                    $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                    $sKey = 'MediaDir';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'DEFAULT_TEMPLATE':
                    $sVal = trim(str_replace('\\', '/', $sVal), '/');
                    $sKey = 'DefaultTemplate';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'TEMPLATE':
                    $sVal = trim(str_replace('\\', '/', $sVal), '/');
                    $sKey = 'Template';
                    $this->aProperties['Request'][$sKey] = $sVal;
                    break;
                case 'DEFAULT_THEME':
                    $sVal = trim(str_replace('\\', '/', $sVal), '/');
                    $sKey = 'DefaultTheme';
                    $this->aProperties['System'][$sKey] = $sVal;
                    $this->aProperties['Request']['Theme'] = trim($sVal, '/');
                    break;
                case 'OCTAL_FILE_MODE':
                    $sVal = ((intval($sVal) & ~0111)|0600); // o-x/g-x/u-x/o+rw
                    $sKey = 'OctalFileMode';
                    $this->aProperties['System']['OctalFileMode'] = $sVal;
                    $this->aProperties['System']['FileModeOctal'] = $sVal;
                    break;
                case 'OCTAL_DIR_MODE':
                    $sVal = (intval($sVal) |0711); // o+rwx/g+x/u+x
                    $sKey = 'OctalDirMode';
                    $this->aProperties['System']['OctalDirMode'] = $sVal;
                    $this->aProperties['System']['DirModeOctal'] = $sVal;
                    break;
                case 'WB_VERSION':
                    $sKey = 'AppVersion';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'WB_REVISION':
                    $sKey = 'AppRevision';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'WB_SP':
                    $sKey = 'AppServicePack';
                    $this->aProperties['System'][$sKey] = $sVal;
                    break;
                case 'PAGE_ICON_DIR':
                    $sKey = 'PageIconDir';
                    $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                    $this->aProperties['Request'][$sKey] = $sVal;
                    break;
                case 'TEMPLATE_DIR':
                    break;
                default:
                    $aSysList = array(
                    // list of values which should be placed in ['System']
                        'DefaultCharset','DefaultDateFormat','DefaultLanguage','DefaultTimeFormat',
                        'DefaultTimezone','DevInfos'
                    );
                    // convert 'true' or 'false' strings into boolean
                    $sVal = ($sVal == 'true' ? true : ($sVal == 'false' ? false : $sVal));
                    // reformatting constant names
                    $sKey = str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($sKey))));
                    if (in_array($sKey, $aSysList)) {
                        $this->aProperties['System'][$sKey] = $sVal;
                    } else {
                        $this->aProperties['Request'][$sKey] = $sVal;
                    }
                    break;
            endswitch;
        }
/* now set values which needs dependencies */
        if (!isset($this->aProperties['Request']['Template']) || $this->aProperties['Request']['Template'] == '') {
            $this->aProperties['Request']['Template'] = $this->DefaultTemplate;
        }
        $this->aProperties['System']['AppName'] = 'WebsiteBaker';
        if (isset($this->Template)) {
            $this->aProperties['Request']['TemplateDir']  = 'templates/'.$this->Template.'/';
            $this->aProperties['Request']['TemplateUrl']  = $this->AppUrl.'templates/'.$this->Template.'/';
            $this->aProperties['Request']['TemplatePath'] = $this->AppPath.'templates/'.$this->Template.'/';
        }
/* correct PageIconDir if necessary */
        $this->aProperties['Request']['PageIconDir'] = str_replace('/*/', '/'.$this->Template, $this->PageIconDir);

        $this->aProperties['System']['VarPath'] = $this->aProperties['System']['AppPath'].'var/';
        $this->aProperties['System']['VarUrl'] = $this->aProperties['System']['AppUrl'].'var/';
        $this->aProperties['System']['VarRel'] = $this->aProperties['System']['AppRel'].'var/';
/* cleanup arrays */
        $this->aProperties['Request'] = array_diff_key(
            $this->aProperties['Request'],
            $this->aProperties['System']
        );

    }
// temporary method for testing purposes only
    public function showAll()
    {
        ksort($this->_aSys['System']);
        ksort($this->_aSys['Request']);
        return $this->_aSys;
    }

} // end of class WbAdaptor


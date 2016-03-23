<?php
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
//if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
if( !isset( $oReg ) && !class_exists('WbAdaptor')) {
    $oThis = new stdClass;
    // first reinitialize arrays
    $oThis->aObjects = array('Db' => null, 'Trans' => null, 'App' => null);
    $oApp    = $oThis->aObjects['App']    = ( @$GLOBALS['wb'] ?: null );
    $oApp    = $oThis->aObjects['App']    = ( @$GLOBALS['admin'] ?: $oApp );
    $oDb     = $oThis->aObjects['Db']     = ( @$GLOBALS['database'] ?: null );
    $oTrans  = $oThis->aObjects['Trans']  = ( @$GLOBALS['Trans'] ?: null );

    $oThis->aReservedVars = array('Db', 'Trans', 'App');
    $oThis->aProperties = array(
        'System' => array(),
        'Request' => array()
    );
        // get all defined constants
    $aConsts = get_defined_constants(true);
    // iterate all user defined constants
    foreach ($aConsts['user'] as $sKey=>$sVal) {
        if (in_array($sKey, $oThis->aReservedVars)) { continue; }
    // skip possible existing database constants
        if (preg_match('/^db_/i', $sKey)) { continue; }
    // change all path items to trailing slash scheme and assign the new naming syntax
        switch($sKey):
            case 'DEBUG':
                $oThis->aProperties['System']['Debug'] = intval($sVal);
                $oThis->aProperties['System']['DebugLevel'] = intval($sVal);
                break;
            case 'WB_URL':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AppUrl';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'WB_REL':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AppRel';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'WB_PATH':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AppPath';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'ADMIN_URL':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AcpUrl';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'ADMIN_REL':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AcpRel';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'ADMIN_PATH':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AcpPath';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'THEME_URL':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'ThemeUrl';
                $oThis->aProperties['Request'][$sKey] = $sVal;
                break;
            case 'THEME_REL':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'ThemeRel';
                $oThis->aProperties['Request'][$sKey] = $sVal;
                break;
            case 'THEME_PATH':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'ThemePath';
                $oThis->aProperties['Request'][$sKey] = $sVal;
                break;
            case 'TMP_PATH':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'TempPath';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'ADMIN_DIRECTORY':
                $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'AcpDir';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'DOCUMENT_ROOT':
                $sVal = rtrim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'DocumentRoot';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'PAGES_DIRECTORY':
                $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                $sVal = $sVal=='/' ? '' : $sVal;
                $sKey = 'PagesDir';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'MEDIA_DIRECTORY':
                $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                $sKey = 'MediaDir';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'DEFAULT_TEMPLATE':
                $sVal = trim(str_replace('\\', '/', $sVal), '/');
                $sKey = 'DefaultTemplate';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'TEMPLATE':
                $sVal = trim(str_replace('\\', '/', $sVal), '/');
                $sKey = 'Template';
                $oThis->aProperties['Request'][$sKey] = $sVal;
                break;
            case 'DEFAULT_THEME':
                $sVal = trim(str_replace('\\', '/', $sVal), '/');
                $sKey = 'DefaultTheme';
                $oThis->aProperties['System'][$sKey] = $sVal;
                $oThis->aProperties['Request']['Theme'] = trim($sVal, '/');
                break;
            case 'OCTAL_FILE_MODE':
                $sVal = ((intval($sVal) & ~0111)|0600); // o-x/g-x/u-x/o+rw
                $sKey = 'OctalFileMode';
                $oThis->aProperties['System']['OctalFileMode'] = $sVal;
                $oThis->aProperties['System']['FileModeOctal'] = $sVal;
                break;
            case 'OCTAL_DIR_MODE':
                $sVal = (intval($sVal) |0711); // o+rwx/g+x/u+x
                $sKey = 'OctalDirMode';
                $oThis->aProperties['System']['OctalDirMode'] = $sVal;
                $oThis->aProperties['System']['DirModeOctal'] = $sVal;
                break;
            case 'WB_VERSION':
                $sKey = 'AppVersion';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'WB_REVISION':
                $sKey = 'AppRevision';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'WB_SP':
                $sKey = 'AppServicePack';
                $oThis->aProperties['System'][$sKey] = $sVal;
                break;
            case 'PAGE_ICON_DIR':
                $sKey = 'PageIconDir';
                $sVal = trim(str_replace('\\', '/', $sVal), '/').'/';
                $oThis->aProperties['Request'][$sKey] = $sVal;
                break;
            case 'TEMPLATE_DIR':
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
                    $oThis->aProperties['System'][$sKey] = $sVal;
                } else {
                    $oThis->aProperties['Request'][$sKey] = $sVal;
                }
                break;
        endswitch;
    }

/* now set values which needs dependencies */
        if (!isset($oThis->aProperties['Request']['Template']) || $oThis->aProperties['Request']['Template'] == '') {
            $oThis->aProperties['Request']['Template'] = $oThis->aProperties['System']['DefaultTemplate'];
        }
        $oThis->aProperties['System']['AppName'] = 'WebsiteBaker';
        if (isset($oThis->aProperties['Request']['Template'])) {
            $oThis->aProperties['Request']['TemplateDir']  = 'templates/'.$oThis->aProperties['Request']['Template'].'/';
            $oThis->aProperties['Request']['TemplateUrl']  = $oThis->aProperties['System']['AppUrl'].'templates/'.$oThis->aProperties['Request']['Template'].'/';
            $oThis->aProperties['Request']['TemplatePath'] = $oThis->aProperties['System']['AppPath'].'templates/'.$oThis->aProperties['Request']['Template'].'/';
        }
/* correct PageIconDir if necessary */
//        $oThis->aProperties['Request']['PageIconDir'] = str_replace('/*/', '/'.$oThis->aProperties['Request']['Template'], $oReg->aProperties['Request']['PageIconDir']);

        $oThis->aProperties['System']['VarPath'] = $oThis->aProperties['System']['AppPath'].'var/';
        $oThis->aProperties['System']['VarUrl'] = $oThis->aProperties['System']['AppUrl'].'var/';
//        $oThis->aProperties['System']['VarRel'] = $oThis->aProperties['System']['AppRel'].'var/';
/* cleanup arrays */
        $oThis->aProperties['Request'] = array_diff_key(
            $oThis->aProperties['Request'],
            $oThis->aProperties['System']
        );

        ksort($oThis->aProperties['System']);
        ksort($oThis->aProperties['Request']);

        $aReg = array_merge( $oThis->aProperties['System'], $oThis->aProperties['Request'],  $oThis->aObjects );
//        foreach ($aReg as $key => $value) { $oReg->$key = $value; }
        $oReg = new ArrayObject( $aReg, ArrayObject::ARRAY_AS_PROPS );

}

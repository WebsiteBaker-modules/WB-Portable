<?php

/*
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
 * SecureTokens.php
 *
 * @category      Core
 * @package       Core_Security
 * @subpackage    WB-2.8.4 and up
 * @copyright     Manuela v.d.Decken <manuela@isteam.de>
 * @author        Manuela v.d.Decken <manuela@isteam.de>
 * @license       http://www.gnu.org/licenses/gpl.html   GPL License
 * @
 * @version       0.1.2
 * @revision      $Revision: $
 * @link          $HeadURL: $
 * @lastmodified $Date: $
 * @since         File available since 12.09.2015
 * @description
 * This class is a replacement for the former class SecureForm using the SecureTokensInterface
 *
 * Settings for this class
 * TYPE    KONSTANTE             REGISTY-VAR                  DEFAULTWERT
 * boolean SEC_TOKEN_FINGERPRINT ($oReg->SecTokenFingerprint) [default=true]
 * integer SEC_TOKEN_NETMASK4    ($oReg->SecTokenNetmask4)    0-255 [default=24]
 * integer SEC_TOKEN_NETMASK6    ($oReg->SecTokenNetmask6)    0-128 [default=64]
 * integer SEC_TOKEN_LIFE_TIME   ($oReg->SecTokenLifeTime)    1800 | 2700 | 3600[default] | 7200
*/

class SecureTokens
{
/**
 * possible settings for TokenLifeTime in seconds
 * @description seconds for 30min / 45min / 1h / 75min / 90min / 105min / 2h
 */
/** minimum lifetime in seconds */
    const LIFETIME_MIN  = 1800; // 30min
/** maximum lifetime in seconds */
    const LIFETIME_MAX  = 7200; // 120min (2h)
/** stepwidth between min and max */
    const LIFETIME_STEP =  900; // 15min
/** lifetime in seconds to use in DEBUG mode if negative value is given (-1) */
    const DEBUG_LIFETIME = 300; // 5
/** array to hold all tokens from the session */
    private $aTokens = array(
        'default' => array('value' => 0, 'expire' => 0, 'instance' => 0)
    );
/** the salt for this run */
    private $sSalt            = '';
/** fingerprint of the current connection */
    private $sFingerprint     = '';
/** the FTAN token which is valid for this run */
    private $aLastCreatedFtan = null;
/** the time when tokens expired if they created in this run */
    private $iExpireTime      = 0;
/** id of the current instance */
    private $sCurrentInstance  = null;
/** id of the instance to remove */
    private $sInstanceToDelete = null;
/** id of the instance to update expire time */
    private $sInstanceToUpdate = null;
/* --- settings for SecureTokens ------------------------------------------------------ */
/** use fingerprinting to encode */
    private $bUseFingerprint = true;
/** maximum lifetime of a token in seconds */
    private $iTokenLifeTime   = 1800; // 30min / 45min / 1h / 2h (default = 30min)
/** bit length of the IPv4 Netmask (0-32 // 0 = off  default = 24) */
    private $iNetmaskLengthV4 = 0;
/** bit length of the IPv6 Netmask (0-128 // 0 = off  default = 64) */
    private $iNetmaskLengthV6 = 0;

/**
 * constructor
 * @param (void)
 */
    protected function __construct()
    {
    // load settings if available
        $this->getSettings();
    // generate salt for calculations in this run
        $this->sSalt        = $this->generateSalt();
    // generate fingerprint for the current connection
        $this->sFingerprint = $this->buildFingerprint();
    // define the expiretime for this run
        $this->iExpireTime  = time() + $this->iTokenLifeTime;
    // calculate the instance id for this run
        $this->sCurrentInstance = $this->encode64(md5($this->iExpireTime.$this->sSalt));
    // load array of tokens from session
        $this->loadTokens();
    // at first of all remove expired tokens
        $this->removeExpiredTokens();
    }

/**
 * destructor
 */
    final public function __destruct()
    {
        foreach ($this->aTokens as $sKey => $aToken) {
            if ($aToken['instance'] == $this->sInstanceToUpdate) {
                $this->aTokens[$sKey]['instance'] = $this->sCurrentInstance;
                $this->aTokens[$sKey]['expire']   = $this->iExpireTime;
            } elseif ($aToken['instance'] == $this->sInstanceToDelete) {
                unset($this->aTokens[$sKey]);
            }
        }
        $this->saveTokens();
    }

/**
 * returns all TokenLifeTime values
 * @return array
 */
    final public function getTokenLifeTime()
    {
        return array(
            'min'   => self::LIFETIME_MIN,
            'max'   => self::LIFETIME_MAX,
            'step'  => self::LIFETIME_STEP,
            'value' => $this->iTokenLifeTime
        );
    }

/**
 * Dummy method for backward compatibility
 * @return void
 * @deprecated from WB-2.8.3-SP5
 */
    final public function createFTAN() { ; } // do nothing

/**
 * Dummy method for backward compatibility
 * @return void
 * @deprecated from WB-2.8.3-SP5
 */
    final public function clearIDKEY() { ; } // do nothing

/**
 * returns the current FTAN
 * @param bool $mode: true or POST returns a complete prepared, hidden HTML-Input-Tag (default)
 *                     false or GET returns an GET argument 'key=value'
 * @return mixed:     array or string
 * @deprecated the param $mMode is set deprecated
 *              string retvals are set deprecated. From versions after 2.8.4 retval will be array only
 */
    final public function getFTAN($mMode = 'POST')
    {
        if (is_null($this->aLastCreatedFtan)) {
            $sFtan = md5($this->sSalt);
            $this->aLastCreatedFtan = $this->addToken(
                substr($sFtan, rand(0,15), 16),
                substr($sFtan, rand(0,15), 16)
            );
        }
        $aFtan = $this->aTokens[$this->aLastCreatedFtan];
        $aFtan['name']  = $this->aLastCreatedFtan;
        $aFtan['value'] = $this->encode64(md5($aFtan['value'].$this->sFingerprint));
        if (is_string($mMode)) {
            $mMode = strtoupper($mMode);
        } else {
            $mMode = $mMode === true ? 'POST' : 'GET';
        }
        switch ($mMode):
            case 'POST':
                return '<input type="hidden" name="'.$aFtan['name'].'" value="'
                      .$aFtan['value'].'" title="">';
                break;
            case 'GET':
                return $aFtan['name'].'='.$aFtan['value'];
                break;
            default:
                return array('name' => $aFtan['name'], 'value' => $aFtan['value']);
        endswitch;
    }

/**
 * checks received form-transactionnumbers against session-stored one
 * @param string $mode: requestmethode POST(default) or GET
 * @return bool:    true if numbers matches against stored ones
 *
 * requirements: an active session must be available
 * this check will prevent from multiple sending a form. history.back() also will never work
 */
final public function checkFTAN($mMode = 'POST')
{
        $bRetval = false;
        // get the POST/GET arguments
        $aArguments = (strtoupper($mMode) == 'POST' ? $_POST : $_GET);
        // encode the value of all matching tokens
        $aMatchingTokens = array_map(
            array($this, 'checkFtanCallback'),
    //            function ($aToken) {
    //                return $this->encode64(md5($aToken['value'].$this->sFingerprint));
    //            },
                // extract all matching tokens from $this->aTokens
                array_intersect_key($this->aTokens, $aArguments)
        );
        // extract all matching arguments from $aArguments
        $aMatchingArguments = array_intersect_key($aArguments, $this->aTokens);
        // get all tokens with matching values from match lists
        $aHits = array_intersect($aMatchingTokens, $aMatchingArguments);
        foreach ($aHits as $sTokenName => $sValue) {
            $bRetval = true;
            $this->removeToken($sTokenName);
        }
        return $bRetval;
    }
/**
 * store value in session and returns an accesskey to it
 * @param mixed $mValue can be numeric, string or array
 * @return string
 */
    final public function getIDKEY($mValue)
    {
        if (is_array($mValue) == true) {
            // serialize value, if it's an array
            $mValue = serialize($mValue);
        }
        // crypt value with salt into md5-hash and return a 16-digit block from random start position
        $sTokenName = $this->addToken(
            substr(md5($this->sSalt.(string)$mValue), mt_rand(0,15), 16),
            $mValue
        );
        return $sTokenName;
    }

/*
 * search for key in session and returns the original value
 * @param string $sFieldname: name of the POST/GET-Field containing the key or hex-key itself
 * @param mixed $mDefault: returnvalue if key not exist (default 0)
 * @param string $sRequest: requestmethode can be POST or GET or '' (default POST)
 * @return mixed: the original value (string, numeric, array) or DEFAULT if request fails
 * @description: each IDKEY can be checked only once. Unused Keys stay in list until they expire
 */
    final public function checkIDKEY($sFieldname, $mDefault = 0, $sRequest = 'POST', $bPreserve = false)
    {
        $mReturnValue = $mDefault; // set returnvalue to default
        $sRequest = strtoupper($sRequest);
        switch ($sRequest) {
            case 'POST':
            case 'GET':
                $sTokenName = $GLOBALS['_'.$sRequest][$sFieldname] ?: $sFieldname;
                break;
            default:
                $sTokenName = $sFieldname;
        }
         if (preg_match('/^[0-9a-f]{16}$/i', $sTokenName)) {
        // key must be a 16-digit hexvalue
            if (array_key_exists($sTokenName, $this->aTokens)) {
            // check if key is stored in IDKEYs-list
                $mReturnValue = $this->aTokens[$sTokenName]['value']; // get stored value
                $this->removeToken($sTokenName, $bPreserve);   // remove from list to prevent multiuse
                if (preg_match('/.*(?<!\{).*(\d:\{.*;\}).*(?!\}).*/', $mReturnValue)) {
                // if value is a serialized array, then deserialize it
                    $mReturnValue = unserialize($mReturnValue);
                }
            }
        }
        return $mReturnValue;
    }

/**
 * make a valid LifeTime value from given integer on the rules of class SecureTokens
 * @param integer  $iLifeTime
 * @return integer
 */
    final public function sanitizeLifeTime($iLifeTime)
    {
        $iLifeTime = intval($iLifeTime);
        for ($i = self::LIFETIME_MIN; $i <= self::LIFETIME_MAX; $i += self::LIFETIME_STEP) {
            $aLifeTimes[] = $i;
        }
        $iRetval = array_pop($aLifeTimes);
        foreach ($aLifeTimes as $iValue) {
            if ($iLifeTime <= $iValue) {
                $iRetval = $iValue;
                break;
            }
        }
        return $iRetval;
    }
// ***************************************************************************************
// *** from here private methods only                                                  ***
// ***************************************************************************************
/**
 * load all tokens from session
 */
    private function loadTokens()
    {
        if (isset($_SESSION['TOKENS'])) {
            $this->aTokens = unserialize($_SESSION['TOKENS']);
        } else {
            $this->saveTokens();
        }
    }

/**
 * save all tokens into session
 */
    private function saveTokens()
    {
        $_SESSION['TOKENS'] = serialize($this->aTokens);
    }

/**
 * add new token to the list
 * @param string $sTokenName
 * @param string $sValue
 * @return string  name(index) of the token
 */
    private function addToken($sTokenName, $sValue)
    {
        // limit TokenName to 16 digits
        $sTokenName = substr($sTokenName, 0, 16);
        // make sure, first digit is a alpha char [a-f]
        $sTokenName[0] = dechex(10 + (hexdec($sTokenName[0]) % 5));
        // loop as long the generated TokenName already exists in list
        while (isset($this->aTokens[$sTokenName])) {
            $aTmp = str_split($sTokenName, 8);
            // increment lower word of Tokenname
            $aTmp[1] = sprintf('%08x', hexdec($aTmp[1])+1);
            $sTokenName = implode('', $aTmp);
        }
        // store Token in list
        $this->aTokens[$sTokenName] = array(
            'value'    => $sValue,
            'expire'   => $this->iExpireTime,
            'instance' => $this->sCurrentInstance
        );
        return $sTokenName;
     }
/**
 * remove the token, called sTokenName from list
 * @param type $sTokenName
 */
    private function removeToken($sTokenName, $bPreserve = false)
    {
        if (isset($this->aTokens[$sTokenName])) {
            if ($bPreserve) {
                $this->sInstanceToUpdate = $this->aTokens[$sTokenName]['instance'];
            } else {
                $this->sInstanceToDelete = $this->aTokens[$sTokenName]['instance'];
            }
            unset($this->aTokens[$sTokenName]);
        }
    }

/**
 * remove all expired tokens from list
 */
    private function removeExpiredTokens()
    {
        $iTimestamp = time();
        foreach ($this->aTokens as $sTokenName => $aToken) {
            if ($aToken['expire'] <= $iTimestamp && $aToken['expire'] != 0){
                unset($this->aTokens[$sTokenName]);
            }
        }
    }

/**
 * generate a runtime depended hash
 * @return string  md5 hash
 */
    private function generateSalt()
    {
        list($fUsec, $fSec) = explode(" ", microtime());
        $sSalt = (string)rand(10000, 99999)
               . (string)((float)$fUsec + (float)$fSec)
               . (string)rand(10000, 99999);
        return md5($sSalt);
    }

/**
 * build a simple fingerprint
 * @return string
 */
    private function buildFingerprint()
    {
        if (!$this->bUseFingerprint) { return md5('this_is_a_dummy_only'); }
        $sClientIp = '127.0.0.1';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            $aTmp = preg_split('/\s*?,\s*?/', $_SERVER['HTTP_X_FORWARDED_FOR'], null, PREG_SPLIT_NO_EMPTY);
            $sClientIp = array_pop($aTmp);
        }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $sClientIp = $_SERVER['REMOTE_ADDR'];
        }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $sClientIp = $_SERVER['HTTP_CLIENT_IP'];
        }
        $aSelfTest = array_chunk(stat(__FILE__), 11);
        unset($aSelfTest[0][8]);
        return md5(
            __FILE__ . PHP_VERSION . implode('', $aSelfTest[0])
            . (array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : 'AGENT')
            . $this->calcClientIpHash($sClientIp)
        );
    }


/**
 * mask IPv4 as well IPv6 addresses with netmask and make a md5 hash from
 * @param string $sClientIp IP as string from $_SERVER['REMOTE_ADDR']
 * @return md5 value of masked ip
 * @description this method does not accept the IPv6/IPv4 mixed format
 *               like "2222:3333:4444:5555:6666:7777:192.168.1.200"
 */
    private function calcClientIpHash($sRawIp)
    {
        if (strpos($sRawIp, ':') === false) {
        // calculate IPv4
            $iIpV4 = ip2long($sRawIp);
        // calculate netmask
            $iMask = ($this->iNetmaskLengthV4 < 1)
                ? 0
                : bindec(
                    str_repeat('1', $this->iNetmaskLengthV4).
                    str_repeat('0', 32 - $this->iNetmaskLengthV4)
                );
        // apply mask
            $sIp = long2ip($iIpV4 & $iMask);
        } else {
        // sanitize IPv6
            $iWords = 8 - count(preg_split('/:/', $sRawIp, null, PREG_SPLIT_NO_EMPTY));
            $sReplacement = $iWords ? implode(':', array_fill(0, $iWords, '0000')) : '';
            $sClientIp = trim(preg_replace('/\:\:/', ':'.$sReplacement.':', $sRawIp), ':');
            $aIpV6 = array_map(
                function($sPart) {
                    return str_pad($sPart, 4, '0', STR_PAD_LEFT);
                },
                preg_split('/:/', $sClientIp)
            );
        // calculate netmask
            $iMask = $this->iNetmaskLengthV6;
            if ($this->iNetmaskLengthV6 < 1) {
                $aMask = array_fill(0, 8, str_repeat('0', 16));
            } else {
                $aMask = str_split(
                    str_repeat('1', $this->iNetmaskLengthV6).
                    str_repeat('0', 128 - $this->iNetmaskLengthV6),
                    16
                );
            }
        // apply mask
            array_walk(
                $aIpV6,
                function(&$sWord, $iIndex) use ($aMask) {
                    $sWord = sprintf('%04x', hexdec($sWord) & bindec($aMask[$iIndex]));
                }
            );
            $sIp = implode(':', $aIpV6);
        }
        return md5($sIp); // return the hash
    }

/**
 * encode a hex string into a 64char based string
 * @param string $sMd5Hash
 * @return string
 * @description reduce the 32char length of a MD5 to 22 chars
 */
    private function encode64($sMd5Hash)
    {
        return rtrim(base64_encode(pack('h*',$sMd5Hash)), '+-= ');
    }

// callback method, needed for PHP-5.3.x only    
   private function checkFtanCallback($aToken)
    {
        return $this->encode64(md5($aToken['value'].$this->sFingerprint));
    }

/**
 * read settings if available
 */
    private function getSettings()
    {
        if (!class_exists('WbAdaptor', false)) {
        // for WB before 2.8.4
            $this->bUseFingerprint  = defined('SEC_TOKEN_FINGERPRINT')
                                      ? SEC_TOKEN_FINGERPRINT
                                      : $this->bUseFingerprint;
            $this->iNetmaskLengthV4 = defined('SEC_TOKEN_NETMASK4')
                                      ? SEC_TOKEN_NETMASK4
                                      : $this->iNetmaskLengthV4;
            $this->iNetmaskLengthV6 = defined('SEC_TOKEN_NETMASK6')
                                      ? SEC_TOKEN_NETMASK6
                                      : $this->iNetmaskLengthV6;
            $this->iTokenLifeTime   = defined('SEC_TOKEN_LIFE_TIME')
                                      ? SEC_TOKEN_LIFE_TIME
                                      : $this->iTokenLifeTime;
        } else {
        // for WB from 2.8.4 and up
            $this->bUseFingerprint  = isset($this->oReg->SecTokenFingerprint)
                                      ? $this->oReg->SecTokenFingerprint
                                      : $this->bUseFingerprint;
            $this->iNetmaskLengthV4 = isset($this->oReg->SecTokenNetmask4)
                                      ? $this->oReg->SecTokenNetmask4
                                      : $this->iNetmaskLengthV4;
            $this->iNetmaskLengthV6 = isset($this->oReg->SecTokenNetmask6)
                                      ? $this->oReg->SecTokenNetmask6
                                      : $this->iNetmaskLengthV6;
            $this->iTokenLifeTime   = isset($this->oReg->SecTokenLifeTime)
                                      ? $this->oReg->SecTokenLifeTime
                                      : $this->iTokenLifeTime;
        }
        $this->iNetmaskLengthV4 = ($this->iNetmaskLengthV4 < 1 || $this->iNetmaskLengthV4 > 32)
                                  ? 0 :$this->iNetmaskLengthV4;
        $this->iNetmaskLengthV6 = ($this->iNetmaskLengthV6 < 1 || $this->iNetmaskLengthV6 > 128)
                                  ? 0 :$this->iNetmaskLengthV6;
        $this->iTokenLifeTime   = $this->sanitizeLifeTime($this->iTokenLifeTime);
        if ($this->iTokenLifeTime <= self::LIFETIME_MIN && DEBUG) {
            $this->iTokenLifeTime = self::DEBUG_LIFETIME;
        }
    }


} // end of class SecureTokens

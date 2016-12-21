<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WbAutoloader
{

    static private $aSearchpatterns = array();
    static private $aReplacements   = array();
    static private $aAbbreviations  = array();
/**
 * Register WB - CoreAutoloader as SPL autoloader
 * @param array $aAbbreviations list of 'directory'=>'shortKey'
 */
    static public function doRegister(array $aAbbreviations)
    {
        if (!sizeof(self::$aSearchpatterns)) {
            if (sizeof($aAbbreviations > 0)) {
                self::$aAbbreviations = $aAbbreviations;
                self::$aSearchpatterns[] = '/(^.[^_].*$)/i';
                self::$aReplacements[] = basename(__DIR__).'_\1';
                foreach ($aAbbreviations as $shortKey => $value) {
                    self::$aSearchpatterns[] = '/^'.$shortKey.'_/i';
                    self::$aReplacements[] = $value.'_';
                }
            }
        }
        spl_autoload_register(array(new self, 'CoreAutoloader'));
    }
/**
 * tries autoloading the given class
 * @param  string $sClassName
 */
    static public function CoreAutoloader($sClassName)
    {
        $sFileName = '';
        $sBaseDir = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/').'/';
        $sClassName = preg_replace(self::$aSearchpatterns, self::$aReplacements, $sClassName);
        $sFileName = $sBaseDir.str_replace('_', '/', $sClassName);
        if (! is_readable($sFileName.'.php')) {
        // alternatively search for file with prefix 'class.'
            $sFileName = dirname($sFileName).'/class.'.basename($sFileName);
            if (! is_readable($sFileName.'.php')) {
                $sFileName = '';
            }
        }

        if ($sFileName) {echo $sFileName.'<br />'; include($sFileName.'.php'); }
    }
/**
 *
 * @return array list of abbreviations
 */
    static public function getAbbreviations()
    {
        return self::$aAbbreviations;
    }
} // end class Autoloader
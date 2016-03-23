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
 *
 * Sanitize.php
 *
 * @category     Security
 * @package      Security_Sanitize
 * @subpackage   Name of the subpackage if needed
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.0.1
 * @revision     $Revision: $
 * @link         $HeadURL: $
 * @lastmodified $Date: $
 * @since        File available since 10.03.2016
 * @description  this class provides several methods for sanitizing.
 */
class Sanitize {

    /* constants for StripFromText */
    const REMOVE_PHP     =  1;    // BIT #0 - remove all PHP-Code
    const REMOVE_DROPLET =  2;    // BIT #1 - remove Droplet tags
    const REMOVE_COMMENT =  4;    // BIT #2 - remove HTML Comments
    const REMOVE_SCRIPT  =  8;    // BIT #3 - remove external and internal Javascript (no inline events)
    const REMOVE_STYLES  = 16;    // BIT #4 - remove external and internal style sheets (no inline)
    const REMOVE_DEFAULT = 26;    // a combination of BITS #1 + #3 + #4

    /** constructor */
    protected function __construct() {
        ;
    }
/**
 * remove complex elements from strings
 * @param mixed $mText string or array of strings
 * @param integer $iFlags all flags of needed functions
 * @return mixed
 */
    public static function StripFromText($mText, $iFlags = self::REMOVE_PHP)
    {
        if (is_string($mText) || is_array($mText)) {
            $aPatterns = array(
                self::REMOVE_PHP     => '/<\?php\s+.*\?>/si',
                self::REMOVE_DROPLET => '/\[\[.*?\]\]/si',
                self::REMOVE_COMMENT => '/<!--\s+.*?-->/si',
                self::REMOVE_SCRIPT  => '/<script[^>]*?\/>|<script[^>]*?>.*?<\/script>/si',
                self::REMOVE_STYLES  =>
                    '/<style[^>]*?\/>|<style[^>]*?>.*?<\/style>|'.
                    '<link[^>]*?(\"text\/css\")?(\"stylesheet\")?[^>]*?\/?>|<link[^>]*?(\"text\/css\")?(\"stylesheet\")?[^>]*?>.*?<\/style>/si',
            );
            $iFlags = intval($iFlags);
            $aSearches = array();
            for ($i = 0; $i < sizeof($aPatterns); $i++) {
                if ((pow(2, $i) & $iFlags) != 0) {
                    $aSearches[] = $aPatterns[pow(2, $i)];
                }
            }
            $mText = preg_replace($aSearches, '', $mText);
        }
        return $mText;
    }

}

// end of class Sanitize

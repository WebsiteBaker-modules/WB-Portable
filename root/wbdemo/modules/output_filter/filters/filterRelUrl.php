<?php
/**
 * Convert full qualified, local URLs into relative URLs
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @param string $sContent
 * @return string
 */
    function doFilterRelUrl($sContent) {
        $aFilterSettings = getOutputFilterSettings();
        if ($aFilterSettings['sys_rel']) {
            $sAppUrl  = rtrim(str_replace('\\', '/', WB_URL), '/').'/';
            $sAppPath = rtrim(str_replace('\\', '/', WB_PATH), '/').'/';
            $sContent = preg_replace_callback(
                '/((?:href|src)\s*=\s*")([^\?\"]*?)/isU',
                function ($aMatches) use ($sAppUrl, $sAppPath) {
                    $sAppRel = preg_replace('/^https?:\/\/[^\/]*(.*)$/is', '$1', $sAppUrl);
                    $aMatches[2] = str_replace('\\', '/', $aMatches[2]);
                    $aMatches[2] = preg_replace('/^'.preg_quote($sAppUrl, '/').'/is', '', $aMatches[2]);
                    $aMatches[2] = preg_replace('/(\.+\/)|(\/+)/', '/', $aMatches[2]);
                    if (!is_readable($sAppPath.$aMatches[2])) {
                    // in case of death link show original link
                        return $aMatches[0];
                    } else {
                        return $aMatches[1].$sAppRel.$aMatches[2];
                    }
                },
                $sContent
            );
            // restore canonical relation links
            $sContent = preg_replace_callback(
                '/<link\s[^>]*?\"canonical\"[^>]*?>/isU',
                function($aMatches) use ($sAppUrl) {
                    return preg_replace(
                        '/(href\s*=\s*\")([^\"]*?)/siU',
                        '\1'.rtrim($sAppUrl, '/').'\2',
                        $aMatches[0]
                    );
                },
                $sContent
            );
        }
        return $sContent;
    }

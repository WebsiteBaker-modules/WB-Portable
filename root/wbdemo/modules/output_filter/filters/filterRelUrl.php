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
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            if (!class_exists('WbAdaptor', false)) {
            // backward compatibility for WB less then 2.8.4
                $sAppUrl  = rtrim(str_replace('\\', '/', WB_URL), '/').'/';
                $sAppPath = rtrim(str_replace('\\', '/', WB_PATH), '/').'/';
            } else {
                $sAppUrl  = WbAdaptor::getInstance()->AppUrl;
                $sAppPath = WbAdaptor::getInstance()->AppPath;
            }
            $sAppRel  = preg_replace('/^https?:\/\/[^\/]*(.*)$/is', '$1', $sAppUrl);
            $sDocRoot = preg_replace('/^(.*?)'.preg_quote($sAppRel, '/').'$/', '$1', $sAppUrl);
            $sContent = preg_replace_callback(
                '/((?:href|src|action)\s*=\s*")([^\?\"]+?)/isU',
                function ($aMatches) use ($sAppUrl, $sAppPath, $sAppRel) {
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
/* Original SP7 Manu Fix */
            // restore canonical relation links
            $sContent = preg_replace_callback(
                '/<link\s[^>]*?\"canonical\"[^>]*?>/isU',
                function($aMatches) use ($sDocRoot) {
                    return preg_replace(
                        '/(href\s*=\s*\")([^\"]*?)/siU',
                        '\1'.rtrim($sDocRoot, '/').'\2',
                        $aMatches[0]
                    );
                },
                $sContent
            );
        }
        return $sContent;
    }

<?php
/**
 * Convert full qualified, local URLs into relative URLs
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @param string $sContent
 * @return string
 */
    function doFilterCanonical($sContent) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            $sAppUrl  = rtrim(str_replace('\\', '/', WB_URL), '/');
/**
 * 
            // restore canonical relation links
            $pattern = '/(<link\s*?.*?(?:rel\s*?=\s*?"canonical"\s*?)?.*?href\s*?=\s*?")([^\"]*?)(\"\s*?.*?(?:rel\s*?=\s*?"canonical"\s*?)?[^>]*?>)/isU'; 
            $replace = '\1'.rtrim($sAppUrl,'/').'\2\3'; 
            $sContent = preg_replace( $pattern, $replace, $sContent );
 */
            // restore canonical relation links
            $sContent = preg_replace_callback(
                '/(<link\s*?.*?(?:rel\s*?=\s*?"canonical"\s*?)?.*?href\s*?=\s*?")'.
                '([^\"]*?)(\"\s*?.*?(?:rel\s*?=\s*?"canonical"\s*?)?[^>]*?>)/siU',
                function ($aMatches) use ($sAppUrl) {
                    $aMatches[2] = str_replace('\\', '/', $aMatches[2]);
                    if (mb_substr($aMatches[2], 0, 1) == '/') {
                        return '$1'.rtrim($sAppUrl, '/').'$2$3';
                    }
                    return $aMatches[0];
                },
                $sContent
            );
        }
        return $sContent;
    }

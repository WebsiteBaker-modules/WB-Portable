<?php
/**
 * moves all css definitions from <body> at bottom of the <head> section
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @param string $content
 * @return string
 */
    function doFilterCssToHead($sContent) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            $aMatches = array();
            $sPattern = '/(\<link[^>]*?"(text\/css|stylesheet)"[^>]*?\/?\>)|'
                      . '(\<style[^>]*?"text\/css"[^>]*?\>.*?\<\/style\>)/i';
            if (preg_match_all($sPattern, $sContent, $aMatches)) {
                $aSafe = $aMatches = array_unique($aMatches[0]);
                array_walk($aMatches, function(&$value, $key) {
                    $value = '/'.preg_quote($value, '/').'/siu';
                });
                if(sizeof($aSafe) > 0) {
                    $sInsert  = "\n".implode("\n", $aSafe)."\n</head>\n<body";
                    $sContent = preg_replace(
                        '/<\/head>.*?<body/si', 
                        $sInsert, 
                        preg_replace($aMatches, '', $sContent), 
                        1
                    );
                }
            }
        }
        return $sContent;
    }


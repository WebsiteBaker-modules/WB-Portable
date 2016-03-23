<?php
/**
 * 
 * @param string $content
 * @return string
 */
    function doFilterLoadOnFly($content) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            if( !preg_match('/<head.*<.*src=\".*\/domReady.js.*>.*<\/head/siU', $content) ) {
                  $scriptLink  = '<script src="'.WB_URL.'/include/jquery/domReady.js" type="text/javascript"></script>'."\n";
                  $scriptLink .= '<script src="'.WB_URL.'/include/jquery/LoadOnFly.js" type="text/javascript"></script>'."\n";
                  $regex = '/(.*)(<\s*?\/\s*?head\s*>.*)/isU';
                  $replace = '$1'.$scriptLink.'$2';
                  $content = preg_replace ($regex, $replace, $content);
            }
        }
        return $content;
    }

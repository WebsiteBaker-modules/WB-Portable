<?php
/**
 * protect email addresses (replace '@' and '.' and obfuscate address
 * @param string $content
 * @return string
 */
    function doFilterJquery($content) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            if( !preg_match('/<head.*<.*src=\".*\/jquery-min.js.*>.*<\/head/siU', $content) ) {
                  $scriptLink  = '<script src="'.WB_URL.'/include/jquery/jquery-min.js" type="text/javascript"></script>'."\n";
                  $scriptLink .= '<script src="'.WB_URL.'/include/jquery/jquery-insert.js" type="text/javascript"></script>'."\n";
                  $scriptLink .= '<script src="'.WB_URL.'/include/jquery/jquery-include.js" type="text/javascript"></script>'."\n";
                  $sJqueryThemeRel =  '/templates/'.TEMPLATE.'/jquery/jquery_theme.js';
                  $scriptLink .=  file_exists(WB_PATH.$sJqueryThemeRel)
                      ? '<script src="'.WB_URL.$sJqueryThemeRel.'" type="text/javascript"></script>'."\n"
                      : '<script src="'.WB_URL.'/include/jquery/jquery_theme.js" type="text/javascript"></script>'."\n";
                  $regex = '/(.*)(<\s*?\/\s*?head\s*>.*)/isU';
                  $replace = '$1'.$scriptLink.'$2';
                  $content = preg_replace ($regex, $replace, $content);
            }
        }
        return $content;
    }

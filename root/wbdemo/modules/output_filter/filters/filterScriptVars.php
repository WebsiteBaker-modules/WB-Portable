<?php
/**
 * 
 * @param string $content
 * @return string
 */
    function doFilterScriptVars($content) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            if( !preg_match('/<head.*<.*src=\".*\/domReady.js.*>.*<\/head/siU', $content) ) {
                  $scriptLink = "<script type=\"text/javascript\">"
                              ."<!--\n"
                              ."var URL = '".WB_URL."';\n"
                              ."var WB_URL = '".WB_URL."';\n"
                              ."var THEME_URL = '".THEME_URL."';\n"
                              ."var TEMPLATE_DIR = '".TEMPLATE_DIR."';\n"
                              ."var TEMPLATE = '".TEMPLATE."';\n"
                              ."var EDITOR = '".WYSIWYG_EDITOR."';\n"
                              ."-->\n"
                              ."</script>\n";
                  $regex = '/(.*)(<\s*?\/\s*?head\s*>.*)/isU';
                  $replace = '$1'.$scriptLink.'$2';
                  $content = preg_replace ($regex, $replace, $content);
            }
        }
        return $content;
    }

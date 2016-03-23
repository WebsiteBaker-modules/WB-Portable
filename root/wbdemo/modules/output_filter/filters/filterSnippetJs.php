<?php
/**
 * 
 * @param string $content
 * @return string
 */
function doFilterSnippetJs($sContent) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            global $database, $wb;
            $sql = 'SELECT DISTINCT `directory` FROM `'.TABLE_PREFIX.'addons` '
                 . 'WHERE `function`=\'snippet\'';
            if (($oSnippetList = $database->query($sql))) { 
                while (($aSnippets = $oSnippetList->fetchRow(MYSQLI_ASSOC))) 
                {
                    $sFrontendJsFile = '/modules/'.$aSnippets['directory'].'/frontend.js';
                    if (!is_readable(WB_PATH.$sFrontendJsFile)) {
                        $sFrontendJsFile = '/modules/'.$aSnippets['directory'].'/js/frontend.js';
                        if (!is_readable(WB_PATH.$sFrontendJsFile)) {
                            continue;
                        }
                    }
                    $sPattern = '/<head.*?<script.*?src\s*?=\s*?\"'.preg_quote(WB_URL.$sFrontendJsFile, '/').'\"[^>]*?>.*?<\/head>/si';
                    if (preg_match($sPattern, $sContent)) {
                        continue;
                    }
                    $sPattern = '/(<head.*<\/script>)(.*?<\/head>)/si';
                    $sReplacement = '$1'."\n".'<script src="'.WB_URL.$sFrontendJsFile.'" type="text/javascript"></script>'.'$2';
                    $sContent = preg_replace($sPattern, $sReplacement, $sContent);
                }
            }
        }
    return $sContent;
}

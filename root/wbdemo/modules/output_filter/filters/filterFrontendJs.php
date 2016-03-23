<?php
/**
 * 
 * @param string $content
 * @return string
 */
function doFilterFrontendJs($sContent) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            global $database, $wb;
            $sql = 'SELECT DISTINCT `module` FROM `'.TABLE_PREFIX.'sections` '
                 . 'WHERE `page_id` = '.(int)$wb->page['page_id'];
            if (($oModuleList = $database->query($sql))) {
                while (($aModules = $oModuleList->fetchRow(MYSQLI_ASSOC))) 
                {
                    $sFrontendJsFile = '/modules/'.$aModules['module'].'/frontend.js';
                    if (!is_readable(WB_PATH.$sFrontendJsFile)) {
                        $sFrontendJsFile = '/modules/'.$aModules['module'].'/js/frontend.js';
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

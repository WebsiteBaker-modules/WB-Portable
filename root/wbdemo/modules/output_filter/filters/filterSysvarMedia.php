<?php
/**
 * Replaces {SYSVAR:MEDIAREL} tags with it's real path
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @param string $sContent
 * @return string
 */
    function doFilterSysvarMedia($sContent) {
        $aFilterSettings = getOutputFilterSettings();
        $key = preg_replace('=^.*?filter([^\.\/\\\\]+)(\.[^\.]+)?$=is', '\1', __FILE__);
        if ($aFilterSettings[$key]) {
            $sMediaUrl = WB_URL.MEDIA_DIRECTORY;
            $sContent = str_replace('{SYSVAR:MEDIA_REL}', $sMediaUrl, $sContent );
        }
        return $sContent;
    }
// end of file

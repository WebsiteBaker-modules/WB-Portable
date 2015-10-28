<?php

/*
 * make use of Thorn's OutputFilter Dashboard (OpF Dashboard)
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @param string &$content : reference to global $content
 * @return void
 */
    function doFilterOpF($content)
    {
        $aFilterSettings = getOutputFilterSettings();
        if ($aFilterSettings['opf']) {
            // Load OutputFilter functions
            $sOpfFile = WB_PATH.'modules/outputfilter_dashboard/functions.php';
            if (is_readable($sOpfFile)) {
                if (!function_exists('opf_apply_filters')) {
                    require($sOpfFile);
                }
                // use 'cache' instead of 'nocache' to enable page-cache.
                // Do not use 'cache' in case you use dynamic contents (e.g. snippets)!
                opf_controller('init', 'nocache');
                $content = opf_controller('page', $content);
            }
        }
        return $content;
    }

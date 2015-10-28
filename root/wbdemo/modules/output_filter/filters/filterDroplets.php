<?php
/**
 * execute droplets
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @param string $content
 * @return string
 */
    function doFilterDroplets($content)
    {
        if(file_exists(WB_PATH .'/modules/droplets/droplets.php')) {
            include_once(WB_PATH .'/modules/droplets/droplets.php');
            if(function_exists('evalDroplets')) {
                $content = evalDroplets($content);
            }
        }
        return $content;
    }

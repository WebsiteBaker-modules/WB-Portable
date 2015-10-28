<?php
/**
 *
 * @category        modules
 * @package         output_filter
 * @copyright       Manuela v.d.Decken <manuela@isteam.de>
 * @author          Manuela v.d.Decken <manuela@isteam.de>
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3-SP4
 * @requirements    PHP 5.3.6 and higher
 *
 */
/* ****************************************************************** */
/**
 * execute the frontend output filter
 * @param  string $sContent actual content
 * @return string modified content
 */
    function executeFrontendOutputFilter($sContent)
    {
        if (!function_exists('OutputFilterApi')) {
            include __DIR__.'/OutputFilterApi.php';
        }
        return OutputFilterApi(
            array(
                'WbLink',
/* ****************************************************************** */
/* *** from here insert ordered requests of individual filters    *** */
/* ***                                                            *** */
                'Droplets',
                'Email',
                'Opf',
/* ***                                                            *** */
/* *** end of individual filters                                  *** */
/* ****************************************************************** */
                'WbLink',
                'RelUrl',
//                'Canonical',
                'CssToHead',
            ),
            $sContent
        );
    }

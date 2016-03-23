<?php
/*
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS HEADER.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * SimpleCommandDispatcher.inc
 *
 * @category     Addons
 * @package      Addons_Dispatcher
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      3.0.1
 * @lastmodified $Date: $
 * @since        File available since 17.12.2015
 * @description  xyz
 */

    require (__DIR__.'/SimpleRegister.php');

    // detect if system running backend or frontend is already set by SimpleRegister
//    $oApp = (isset($GLOBALS['admin']) ? $GLOBALS['admin'] : $GLOBALS['wb']);
    $bIsBackend = ($oApp instanceof admin);
    // set addon depending path / url
    $sAddonPath = $oReg->AppPath.'/modules/'.$sAddonName;
    $sAddonUrl  = $oReg->AppUrl.'/modules/'.$sAddonName;
    // define the theme to use -----------------------------------------------------------
    if (is_readable($sAddonPath.'/themes/default')) {
    // first set fallback to system default theme
        $sAddonThemePath = $sAddonPath.'/themes/default';
        $sAddonThemeUrl  = $sAddonUrl.'/themes/default';
    }
    if (is_readable($sAddonPath.'/themes/'.$oReg->DefaultTheme)) {
    // overload with the selected theme if accessible
        $sAddonThemePath = $sAddonPath.'/themes/'.$oReg->DefaultTheme;
        $sAddonThemeUrl  = $sAddonUrl.'/themes/'.$oReg->DefaultTheme;
    }
    // define the template to use --------------------------------------------------------
    if (is_readable($sAddonPath.'/templates/default')) {
        // first set fallback to system default template
        $sAddonTemplatePath = $sAddonPath.'/templates/default';
        $sAddonTemplateUrl  = $sAddonUrl.'/templates/default';
    }
    if (is_readable($sAddonPath.'/templates/'.$oReg->DefaultTemplate)) {
        // try setting to the template of global settings
        $sAddonTemplatePath = $sAddonPath.'/templates/'.$oReg->DefaultTemplate;
        $sAddonTemplateUrl  = $sAddonUrl.'/templates/'.$oReg->DefaultTemplate;
    }
    if (!$bIsBackend && is_readable($sAddonPath.'/templates/'.$oReg->Template)) {
        // try setting to the template of page depending settings
        $sAddonTemplatePath = $sAddonPath.'/templates/'.$oReg->Template;
        $sAddonTemplateUrl  = $sAddonUrl.'/templates/'.$oReg->Template;
    }
    // load addon depending language file ------------------------------------------------
    if (is_readable($sAddonPath.'/languages/EN.php')) {
        // first load fallback to system default language (EN)
        include $sAddonPath.'/languages/EN.php';
    }
    if (is_readable($sAddonPath.'/languages/'.$oReg->DefaultLanguage.'.php')) {
        // try loading language of global settings
        include $sAddonPath.'/languages/'.$oReg->DefaultLanguage.'.php';
    }
    if (is_readable($sAddonPath.'/languages/'.$oReg->Language.'.php')) {
        // try loading language of user (backend) or page (frontend) defined settings
        include $sAddonPath.'/languages/'.$oReg->Language.'.php';
    }
     // Include the ordering class
    if (!class_exists('order')) {
    include WB_PATH.'/framework/class.order.php';
    }
    // Simple Command Dispatcher ---------------------------------------------------------
    // sanitize command from compatibility file
    $sCommand = (isset($sCommand) ? strtolower($sCommand) : '');
    // sanitize/validate request var 'cmd'
    $sCmd = preg_replace(
        '/[^a-z\/0-1]/siu',
        '',
        (isset($_REQUEST['cmd']) ? strtolower($_REQUEST['cmd']) : '')
    );
    // build valid sCommand string
    if (($sCommand && $sCmd)) {
        if (!preg_match('/^'.$sCommand.'/si', $sCmd)) {
            // concate both arguments if needed
            $sCommand .= '/'.$sCmd;
        } else {
            $sCommand = $sCmd;
        }

$sCmd = '';
    }
    $sCommand = 'cmd'.str_replace( // remove spaces and add prefix 'cmd'
        ' ', '',
        ucfirst( // make first char of every word to uppercase
            str_replace( // change '/' to space
                '/', ' ',
                preg_replace( // change leading 'add/' to 'modify/'
                    '/^add\//s',
                    'modify/',
                    trim(($sCommand ?: $sCmd), '/') // remove leading and trailing slashes
                )
            )
        )
    );
    // execute command -------------------------------------------------------------------
    if (is_readable($sAddonPath.'/cmd/'.$sCommand.'.inc') ) {
        include($sAddonPath.'/cmd/'.$sCommand.'.inc');
    } else {
        throw new Exception('call of invalid command ['.$sCommand.'] for [modules/'.$sAddonName.'] failed!');
    }

// end of file
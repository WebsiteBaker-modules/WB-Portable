<?php
/**
 * $Id: index.php 70 2016-10-19 16:43:24Z dietmar $
 * Website Baker template: allcss
 * This template is one of four basis templates distributed with Website Baker.
 * Feel free to modify or build up on this template.
 *
 * This file contains the overall template markup and the Website Baker
 * template functions to add the contents from the database.
 *
 * LICENSE: GNU General Public License
 *
 * @author     Ryan Djurovich, C. Sommer
 * @copyright  GNU General Public License
 * @license    http://www.gnu.org/licenses/gpl.html
 * @version    2.70
 * @platform   Website Baker 2.7
 *
 * Website Baker is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Website Baker is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_URL') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */

ob_start();  //fetch MainContent
page_content(1);
$page_contentMain = ob_get_clean();
ob_start();  //fetch original header content
page_content(2);
$page_contentTeaser = ob_get_clean();
ob_start();  //fetch original header content
page_content(3);
$page_contentSidebar = ob_get_clean();

$lang    = (defined('LANGUAGE') && LANGUAGE ? LANGUAGE : 'EN');
$charset = (defined('DEFAULT_CHARSET') ? DEFAULT_CHARSET : 'utf-8');
// TEMPLATE CODE STARTS BELOW
?><!DOCTYPE HTML>
<html lang="<?php echo strtolower((defined('LANGUAGE') && LANGUAGE ? LANGUAGE : 'EN')); ?>">
<head>
    <meta charset="<?php echo (defined('DEFAULT_CHARSET') ? DEFAULT_CHARSET : 'utf-8'); ?>" />
    <title><?php page_title('', '[WEBSITE_TITLE]'); ?></title>
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="<?php page_description(); ?>" />
    <meta name="keywords" content="<?php page_keywords(); ?>" />
    <!-- Mobile viewport optimisation -->
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes"/>
    <link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/screen.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/CookieNotice.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo TEMPLATE_DIR; ?>/css/print.css" media="print" />
    <link rel="canonical" href="[wblink<?php echo $page_id;?>]"/>
    <link rel="alternate" type="application/rss+xml" title="Test RSS-Feed" href="<?php echo WB_URL; ?>/modules/news/rss.php?page_id=12" />
<?php
// automatically include optional WB module files (frontend.css, frontend.js)
        register_frontend_modfiles('css');
?>

    <!--[if lt IE 9]>
        <script>
            document.createElement('header');
            document.createElement('nav');
            document.createElement('footer');
        </script>
    <![endif]-->

<?php
// automatically include optional WB module files (frontend.css, frontend.js)
    register_frontend_modfiles('jquery');
    register_frontend_modfiles('js');
?>

</head>
<?php if( is_readable( WB_PATH.'/modules/wbCounter/count.php' )) { include ( WB_PATH.'/modules/wbCounter/count.php' ); } ?>
<body class="allcssRes gradient-sweet-home">
<div id="allcssRes-wrapper" class="main outer-box ">
    <header >
        <div class="banner gradient">
            <a class="h1" href="<?php echo WB_URL; ?>/" target="_top"><?php page_title('', '[WEBSITE_TITLE]'); ?></a>
            <span class="h1">| <?php page_title('', '[PAGE_TITLE]'); ?></span>
        </div>
    <!-- frontend search -->
    <div class="search_box gradient round-top-left round-top-right">
<?php
        // CODE FOR WEBSITE BAKER FRONTEND SEARCH
if (SHOW_SEARCH) { ?>
    <form name="search" action="<?php echo WB_URL; ?>/search/index.php" method="get" >
        <input type="hidden" name="referrer" value="<?php echo defined('REFERRER_ID') ? REFERRER_ID : PAGE_ID; ?>" />
        <input type="text" name="string" class="search_string" />
        <input type="submit" name="wb_search" id="wb_search" value="<?php echo $TEXT['SEARCH']; ?>" class="search_submit" />
    </form>
<?php } ?>
    </div>
    </header>
<?php if(trim($page_contentTeaser)!=''){ ?>
        <div class="teaser">
          <div class="content">
          <?php echo $page_contentTeaser; ?>
          </div><!-- end content -->
        </div><!-- end teaser -->
<?php } ?>

    <input type="checkbox" id="open-menu" />
    <label for="open-menu" class="open-menu-label">
        <span class="title h4"> <?php page_title('', '[PAGE_TITLE]'); ?></span>
        <span class="fa fa-bars" aria-hidden="true"> </span>
    </label>
    <div id="lang" style="height: 2.925em;">
<?php $iMultiLang = 0; if (function_exists('language_menu')) { $sMultiLang = language_menu(); $iMultiLang = intval($sMultiLang!='');} ?>
    </div>
    <div id="left-col">
      <div class="content">
        <!-- main navigation menu -->
        <nav class="outer-box gradient-sweet-home">
            <div class="menu">
                <?php
                echo show_menu2(0, SM2_ROOT+$iMultiLang, SM2_CURR+1, SM2_ALL|SM2_BUFFER|SM2_PRETTY|SM2_NUMCLASS,'<li><span class="menu-default">[ac][menu_title]</a></span>','</li>','<ul>','</ul>');
                ?>
            </div>
        </nav>

<?php if(trim($page_contentSidebar)!=''){ ?>
      <div class="left-content outer-box gradient-sweet-home">
            <?php echo $page_contentSidebar; ?>
      </div>
<?php } ?>
<?php if (defined('FRONTEND_LOGIN') && FRONTEND_LOGIN){ ?>
        <div class="outer-box gradient-sweet-home">
             [[LoginBox]]
        </div>
<?php } ?>
<?php if (function_exists('wbCounter')){ ?>
        <div class="outer-box gradient-sweet-home">
             <?php echo wbCounter();?>
        </div>
<?php } ?>
      </div><!-- end content -->
    </div><!-- end left-col -->

    <div class="main-content">
        <?php echo $page_contentMain; ?>
    </div>

    <footer>
    <div class="footer p">
        <?php page_footer(); ?>
    </div>
    </footer>

</div>

<div class="powered_by">
    Powered by <a href="http://websitebaker.org" target="_blank">WebsiteBaker</a>
</div>
<div id="CookieNotice">
    <div id="CookieNoticeBar">
        <span id="CookieNoticeClose">OK</span>
        <span id="CookieNoticeInfo">This website uses cookies. When you browse on this site, you agree to the use of cookies.</span>
    </div>
</div>
<script charset="utf-8" type="text/javascript" src="<?php echo TEMPLATE_DIR; ?>/jquery_frontend.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo TEMPLATE_DIR; ?>/js/CookieNotice.js"></script>

<?php
// automatically include optional WB module file frontend_body.js)
//        register_frontend_modfiles_body('jquery');
        register_frontend_modfiles_body('js');
?>

</body>
</html>

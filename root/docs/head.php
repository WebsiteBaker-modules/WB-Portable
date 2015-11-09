<!DOCTYPE HTML>
<html lang="<?php echo strtolower($lang); ?>">
<head>
  <meta charset="utf-8" />
    <title><?php echo $title; ?></title>
    <link href="screen.css" rel="stylesheet" type="text/css" />
</head>
<body>
<a id="top"></a>
<div id="framers_framer">    
        <div id="header">
            <div class="logo">
                <a href="http://127.0.0.1:4001/docs/"><img src="1x1.gif" alt="" width="567" height="159" border="0" /></a></div>            
            <div class="print_logo" style="display: none;">
                <img src="1x1.gif" alt="" width="477" height="229" border="0" />
                <p>WebsiteBaker CMS - Portable Edition</p>
            </div>
            <div class="about_pic pic_<?php echo $lang; ?>">&nbsp;</div>            
        </div>
    <!-- /#header -->
    
    <div id="content_framer">
        
        <div id="bars">
            
            <div class="breadcrumb_bar">
            <?php if($title != 'START - WebsiteBaker CMS Portable') { ?>    
                <div>
                    <a href="en.php" target="_self" title="EN"><img class="flag_icon" src="EN.png" alt="EN" /></a>
                        &nbsp;
                    <a href="de.php" target="_self" title="DE"><img class="flag_icon" src="DE.png" alt="DE" /></a>
                        &nbsp;
                    <a href="nl.php" target="_self" title="NL"><img class="flag_icon" src="NL.png" alt="NL" /></a>
                        &nbsp;
                </div>            
                <div class="breadcrumb"><strong><?php echo $crumb; ?>:</strong><span class="arrow">&nbsp;</span><span><?php echo $title; ?></span>
                    <p style="clear:both;"></p>
                </div>
            <?php } ?>    
            </div>
            
        </div>
        
        <!-- /#bars -->        
        <div id="sidebar">        
            <ul class="leftmenu">
                <li><a href="index.php" >Start</a>
                <ul class="leftmenu">
                    <li><a title="English Help Page" href="en.php" >English</a></li>
                    <li><a title="Deutsche Hilfeseite" href="de.php" >Deutsch</a></li>
                    <li><a title="Dutch Help Page" href="nl.php" >Nederland</a></li>
                </ul>
            </li>
            </ul>
            <div>
                <h2>Useful Links</h2>
                <hr />
                <ul class="leftmenu">
                    <li><a href="http://www.websitebaker.org/" target="_blank" >WebsiteBaker CMS</a></li>
                    <li><a href="http://www.websitebaker.org/forum/" target="_blank" >WebsiteBaker Forum</a></li>                        
                    <li><a href="http://portable.websitebaker.org/" target="_blank" >WebsiteBaker Portable</a></li>
                    <!--<li><a href="http://portableapps.com/apps/internet/firefox_portable" target="_blank" >FirefoxPortable</a></li>-->
                    <li><a href='http://www.websitebaker.at/wb-templates/' target='_blank' >Websitebaker.at Templates</a></li>
                    <li><a href='http://getfirebug.com/' target='_blank' >Firebug.com</a></li>
                    <li><a href='http://notepad-plus-plus.org/' target='_blank' >Notepad++ Editor</a></li>
                    <li><a href='http://www.mysqldumper.net/' target='_blank' >MySQLDumper</a></li>
                    <li><a href="http://www.usbwebserver.net/en/" target="_blank" >USBWebserver8</a></li>
                </ul>
            </div>
            <div>
                <h2>Support WebsiteBaker</h2>
                <hr />
<?php echo $TEXT['PORT_DONATE']; ?>
                <br />
                <div id="fb-link">
                    <a title="WebsiteBaker Portable on Facebook" target="_blank" href="http://www.facebook.com/WebsiteBaker.Portable" class="">
                        <img width="120" alt="find us on facebook" title="WebsiteBaker Portable on Facebook" src="find-on-fb.png" />
                    </a>
                </div>
            </div>
            <!--<div id="copyright">
                <h3>Copyright &copy; 2013 <br /><br /><a href="http://www.websitebaker-portable.com/" target="_blank" >WebsiteBaker Portable Project</a></h3>    
                <p>Text and images of this page are licensed under a <a href="http://creativecommons.org/licenses/by-nc-nd/3.0/" target="_blank">Creative Commons Attribution-NonCommercial-No Derivative 3.0</a> Licence. You are free to copy and distribute this work for noncommercial purposes as long as no changes are applied and this copyright notice and a backlink to <a href="http://www.websitebaker.org">WebsiteBaker.org</a> are provided.</p>
            </div>-->
        </div>
        <div id="content">
        <?php // here begins your content 
        ?>
<?php
$lang = 'NL';
$aCurrent = array( 'nl' => 'current', 'de' => 'link', 'en' => 'link' );
$title = 'Welkom bij WebsiteBaker CMS Portable';
$crumb = 'U bent hier';
$TEXT['PORT_DONATE'] = '<form method="post" target="_blank" action="https://www.paypal.com/cgi-bin/webscr">
                            <p style="text-align: center;">
                                <input type="hidden" value="_s-xclick" name="cmd" />
                                <input type="hidden" value="7853384" name="hosted_button_id" />
                                <input type="image" alt="PayPal, de veilige en complete manier van online betalen." name="submit" src="https://www.paypal.com/nl_NL/NL/i/btn/btn_donateCC_LG.gif" />
                                <img width="1" height="1" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" alt="" />
                            </p>
                        </form>';
include ("head.php");

// content begins here
?>
        <h2>Wat is WebsiteBaker Portable</h2>
        <p>Het <a target="_blank" href="http://websitebaker.org/">WebsiteBaker</a> Portable Project combineert het populaire, makkelijk te gebruiken Content Management System met de eenvoudig te configureren en veelzijdig inzetbare <a target="_blank" href="http://www.usbwebserver.net/">USBWebserver8</a>.</p>
        <p><b>Zo hebt u een systeem dat als een live demo van WebsiteBaker of als lokale testomgeving gebruikt kan worden.</b></p>
        <br />
        <hr />
        <br />
        <h2>Taal-instellingen</h2>
        <p>WebsiteBaker ondersteunt een veelheid aan talen. Deze kunt u onder<br />
        <strong><tt>Settings &gt; Default Settings &gt; Language</tt></strong> en onder<br />
        <strong><tt>Preferences &gt; My Settings &gt; Language </tt></strong> <i>(evenals de tijdzone, datum en tijd)</i> aanpassen. </p>
        <br />
        <hr />
        <br />
        <h2>Change Intropage that starts up with the Server</h2>
        <p>I've set up the short documentation in front of the WebsiteBaker.</p>
        <p>If you don't need this intropage you'll only have to change the startdirectory!</p>
        <h3>WB-Portable-xxx/root/index.php</h3>
        <p>Open with a texteditor like notepad, find line 5<br /><br />
        <tt>$start = 'docs';</tt></p>
        <p>change <tt>'docs'</tt> to <tt>'wbdemo'</tt> (Frontend) or <tt>'wbdemo/admin'</tt> (Administration/Backend)</p>
        <br />
        <hr />
        <br />
        <h2>Offici&euml;le ondersteuning voor WebsiteBaker</h2>
        <p>Voor hulp bij de verschillende thema's en de manier waarop WebsiteBaker werkt staan <a href="http://help.websitebaker.org/" target="_blank">de officiële WebsiteBaker hulppagina's</a> ter beschikking. Voor specifieke vragen of problemen kunt u ook <a href="http://forum.websitebaker.org/" target="_blank">het WebsiteBaker Forum</a> doorzoeken.</p>
        <br />
        <hr />
        <br />
        <h2>WebsiteBaker Add-ons (modules and templates)</h2>
        <p>There exists numerous WebsiteBaker templates, designs, modules and add-ons. Check out the <a href="http://addon.websitebaker.org/" target="_blank">official WebsiteBaker Add-ons Repository</a> to find what you need.</p>
        <br />
        <hr />
        <br />
        <fieldset>
        <legend>WebsiteBaker Portable</legend>
        <ul>
        <li><a target="_blank" href="http://127.0.0.1:4001/wbdemo/index.php"><strong>Directe toegang tot de frontend </strong></a>(voor bezoekers)</li>
        <li><a target="_blank" href="http://127.0.0.1:4001/wbdemo/admin/login/index.php"><strong>Directe toegang tot de backend</strong></a>(administratie)<br /><br />
             <b>Loginname:</b> admin<br />
             <b>Password: </b>admin</li>
        </ul>        
        </fieldset>
        <br />
        <hr />
        <br />
        <h2>About USBWebserver8</h2>
        <p>You can display the USBWebserver8 WAMP Infos (e.g. phpinfo) <a href="http://127.0.0.1:4001/docs/usbw-info/" target="_blank">by clicking here!</a><br />
        USBWebserver-handleiding: <a href="http://www.usbwebserver.net/downloads/handleiding.pdf" target="_blank" title="USBWebserver8 handleiding">Click!</a><br />
        Access to database via <b>MySQLDumper</b>: <a href="http://127.0.0.1:4001/mysqldumper" target="_blank">Click!</a><br />
        Access to database via <b>phpMyAdmin</b>: <a href="http://127.0.0.1:4001/phpmyadmin" target="_blank">Click!</a><br /><br /></p>        
        <hr />
        <br />
        <h2>Dank aan</h2>
        <p><b>USBWebserver8</b> ontwikkelaar.<br />Deze WebsiteBaker Portable Editie kon alleen met behulp van het gratis beschikbare USBWebserver8 worden ontwikkeld.</p>
        <p>Christian Sommer (aka doc) voor het idee.<br />
        Afbeeldingen en logo zijn ontworpen door Christian M. Stefan (aka Stefek).</p>

        <p>De <b>WebsiteBaker community</b> zonder welke er niet zulke ideeën zouden zijn geboren.</p>
        <p>Iedereen die het project op de een of andere manier ondersteund heeft, met name Martin Freudenreich (aka mr-fan)</p>
        <p>Veel plezier bij het bakken!<br /><strong>WebsiteBaker Org e.V.</strong></p>
        
    <?php
include ("foot.php");

?>        
<?php
$lang = 'DE';
$title = 'Willkommen zu WebsiteBaker CMS Portable ';
$crumb = 'Sie sind hier';
$TEXT['PORT_DONATE'] = '<form method="post" target="_blank" action="https://www.paypal.com/cgi-bin/webscr">
                            <p style="text-align: center;">
                                <input type="hidden" value="_s-xclick" name="cmd" />
                                <input type="hidden" value="6629896" name="hosted_button_id" />
                                <input type="image" alt="Jetzt einfach, schnell und sicher online spenden." name="submit" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" />
                                <img width="1" height="1" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" alt="" />
                            </p>
                        </form>';

include ("head.php");

// content begins here
?>
<h2>Was ist WebsiteBaker Portable</h2>

        <p>Das <a target="_blank" href="http://websitebaker.org/">WebsiteBaker</a> Portable Projekt verbindet das beliebte, leicht zu bedienende Content Management System mit dem einfach konfigurierbaren und vielseitig einsetzbaren <a target="_blank" href="http://www.usbwebserver.net/en/">USBWebserver8</a>.</p>
        <p><b>Dadurch haben Sie ein System, dass als Live-Demo von WebsiteBaker oder auch für eine lokale Testumgebung genutzt werden&nbsp; kann.</b></p>
        <br />
        <hr />
        <br />
        <h2>Einstellung der Sprache</h2>
        <p>WebsiteBaker beherrscht viele Sprachen, diese können Sie unter<br />
        <strong><tt>Settings &gt; Default Settings &gt; Language</tt></strong> und unter<br />
        <strong><tt>Preferences &gt; My Settings-Language</tt></strong> <i>(ebenso Zeitzone, Datum und Uhrzeit)</i> anpassen.<br />
        <br />
        <hr />
        <br />
        <h2>&Auml;ndern der Startseite bei Serverstart</h2>
        <p>Aktuell startet diese Kurzdokumentation automatisch mit dem Server.</p>
        <p>Wenn Sie immer direkt mit WebsiteBaker starten wollen, ändern Sie einfach den Startordner in folgender Datei:</p>
        <h3>WB-Portable-xxx/root/index.php</h3>
        <p>&Ouml;ffnen Sie die Datei mit einem Texteditor wie Notepad und finden Zeile 5<br /><br />
        <tt>$start = 'docs';</tt></p>
        <p>&Auml;ndern Sie <tt>'docs'</tt> zu <tt>'wbdemo'</tt> (Frontend) oder <tt>'wbdemo/admin'</tt> (Administration/Backend)</p>
        <br />
        <hr />
        <br />
        <h2>Offizieller Support f&uuml;r WebsiteBaker</h2>
        <p style="text-align: left;">F&uuml;r Hilfestellungen zu einzelnen Themen und der Bedienung von WebsiteBaker steht
         <a target="_blank" href="http://wiki.websitebaker.org/">die offizielle Hilfe Seite</a> jederzeit zur Verf&uuml;gung. F&uuml;r Detailfragen zu Besonderheiten oder Problemen können Sie auch<a target="_blank" href="http://forum.websitebaker.org/"> das Forum</a> durchsuchen.</p>
        <br />
        <hr />
        <br />
        <h2>Erweiterungen - Module und Templates</h2>
        <p>F&uuml;r WebsiteBaker gibt es sehr viele Templates, Designs, Module und Add-ons. Besuchen Sie das <a target="_blank" href="http://addons.websitebaker.org/">offizielle Add-ons Repository</a>. Dort finden Sie alle verf&uuml;gbaren Zusatzmodule und -funktionen f&uuml;r Ihr WebsiteBaker.</p>
        <br />
        <hr />
        <br />
        <fieldset>
        <legend>WebsiteBaker Portable</legend>
        <ul>
        <li><a target="_blank" href="http://localhost:4001/wbdemo/"><strong>Direktzugang zum Frontend </strong></a>(Besucherbereich)</li>
        <li><a target="_blank" href="http://localhost:4001/wbdemo/admin/"><strong>Direktzugang zum Backend </strong></a>(Administrationsbereich)<br /><br />
             <b>Loginname: </b>admin<br />
             <b>Passwort: </b>admin</li>
        </ul>
        </fieldset>
        <br />
        <hr />
        <br />
        <h2>&Uuml;ber USBWebserver8</h2>
        <p>Alle Infos zu USBWebserver8 WAMP auf einen Blick (z.B. phpinfo) <a target="_blank" href="http://localhost:4001/docs/usbw-info/">gibt es Hier!</a><br />
        Eine Anleitung in englisch: <a href="http://www.usbwebserver.net/downloads/manual.pdf" target="_blank" title="USBWebserver8 Anleitung">Klick!</a><br />
        Zugriff auf die Datenbank per <b>phpMyAdmin</b>: <a target="_blank" href="http://localhost:4001/phpmyadmin/">Klick!</a><br /><br /></p>
        <hr />
        <br />
        <h2>Dank an:</h2>
        <p>Die Entwickler von <b>USBWebserver8</b>.<br />
        Diese WebsiteBaker Portable Edition konnte nur mit Hilfe des frei verf&uuml;gbaren USBWebserver8 erstellt werden.</p>
        <p>Christan Sommer (aka doc) f&uuml;r die Idee.<br />
        Grafiken &amp; Logodesign von Christian M. Stefan (aka Stefek).</p>
        <p>Die <b>WebsiteBaker Community</b>, ohne die solche Ideen nicht denkbar w&auml;ren!</p>
        <p>Alle, die das Projekt unterstützt haben, speziell Martin Freudenreich (aka mr-fan) für die Umsetzung des Portable Projektes.<br />
        <p>Viel Spaß beim Backen!<br />
        <strong>WebsiteBaker Org e.V.</strong></p>
<?php
include ("foot.php");

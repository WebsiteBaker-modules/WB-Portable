<?php
$lang = 'EN';
$title = 'START - WebsiteBaker CMS Portable';
$crumb = 'You\'re here';
$TEXT['PORT_DONATE'] = '<form action="https://www.paypal.com/cgi-bin/webscr" target="_blank" method="post">
                            <p style="text-align: center;">
                                <input type="hidden" name="cmd" value="_s-xclick" />
                                <input type="hidden" name="hosted_button_id" value="6629956" />
                                <input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to donate online!" />
                                <img width="1" height="1" alt="" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" />
                            </p>
                        </form>';
include ("head.php");

// content begins here
?>

<div id="help">
    <h1>WebsiteBaker Portable Help</h1>

    <fieldset>
    <legend>Please choose your language</legend>
        <table class="separat">
            <tr>
                <td><a target="_self" title="English Help Page" href="http://localhost:4001/docs/en.php"><img height="34" width="32" alt="EN" src="EN.png" /></a></td>
                <td><a target="_self" title="Deutsche Hilfeseite" href="http://localhost:4001/docs/de.php"><img height="34" width="32" alt="DE" src="DE.png" /></a></td>
                <td><a target="_self" title="Dutch Help Page" href="http://localhost:4001/docs/nl.php"><img height="34" width="32" alt="NL" src="NL.png" /></a></td>
            </tr>
        </table>
    </fieldset>
</div>
    <!--<br />
    <h2>Additional Stuff for your Portable Edition</h2>
        <ul>
            <li>Take your Design - Look at the <a target="_blank" href="http://websitebaker.at/wb-templates/">Top 30 Templates from the Templatefinder</a> from the Temlate Search (Website is only German - Templates aren't ;)</li>
    </ul>
        <br />
        <hr />-->
        <br />
        <hr />
        <br />
    <h2 style="text-align: center;">Skip Documentation</h2>
        <p style="float:right; text-align:center;margin:25px 60px -50px 0;">Username and Password:<br />
        <b>admin</b></p>
        <div id="skip">
            <br />
            <p><a title="Website" target="_blank" href="http://localhost:4001/wbdemo/index.php"><img height="86" width="250" align="middle" alt="" src="button-frontend.png" /></a> <a title="Administration" target="_blank" href="http://localhost:4001/wbdemo/admin/login/index.php"><img height="86" width="250" align="middle" alt="" src="button-backend.png" /></a></p>
            <br />
            <hr />
            <h2 >MySQL Clients</h2>
            <div class="links" style="text-align: center; width: 100%; ">
            <ul class="links">
                <li class="links" >
                <a title="adminer" target="_blank" href="http://localhost:4001/adminer/index.php">
                    <img width="148" alt="" src="http://localhost:4001/docs/_adminer.png" />
                </a>
                </li>
                <li class="links">
                <a title="phpMyAdmin" target="_blank" href="http://localhost:4001/phpmyadmin/">
                    <img width="148" alt="" src="http://localhost:4001/docs/_button-pma.png" />
                </a>
                </li>
            </ul>

            <p style="text-align:center;">Username and Password:<br />
            <b>root / usbw</b></p>
            </div>
        </div>
        <hr />
        <div style="text-align: center;">
        <h2>About USBWebserver8.6</h2>
        <p>You can display the USBWebserver8 WAMP Infos (e.g. phpinfo) <a href="http://localhost:4001/docs/usbw-info/" target="_blank">by clicking here!</a></p>
        </div>
    <!--<h2>Change Intropage that starts up with the Server</h2>
        <p>I've set up the short documentation in front of the WebsiteBaker.</p>
        <p>If you don't need this intropage you'll only have to change the startdirectory!</p>
        <h3>WB-Portable-xxx/root/index.php</h3>
        <p><tt>line 5: $start = 'docs';</tt></p>
        <p>change <tt>'docs'</tt> to <tt>'wbdemo'</tt> or <tt>'wbdemo/admin'</tt></p>
        <hr />-->
        <blockquote style="text-align: center;">
            <br />
            <h3>Have fun with <span>WebsiteBaker Portable Edition</span></h3>
            <cite>WebsiteBaker Org e.V.</cite>
        </blockquote>

<?php
include ("foot.php");


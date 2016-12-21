<?php
$lang = 'en';
$title = 'Welcome to WebsiteBaker CMS Portable ';
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
?><h2>What is WebsiteBaker portable about</h2>
        <p>The <a href="http://websitebaker.org/" target="_blank">WebsiteBaker</a> portable project combines the easy to use content management system WebsiteBaker with the out of the box server solution <a href="http://www.usbwebserver.net/en/" target="_blank">USBWebserver8</a>.</p>
        <p><strong>This allows you to demonstrate WebsiteBaker live or test and run it in a local test environment by one click.</strong></p>
        <br />
        <hr />
        <br />
        <h2>Language settings</h2>
        <p>WebsiteBaker supports various languages. The default language can be set via<br />
        <strong><tt>Settings &gt; Default Settings &gt; Language</tt></strong>. Each user can change his basic preferences like language, time zone or display format of date and time via <strong><tt>Preferences &gt; My Settings.</tt></strong></p>
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
        <h2>WebsiteBaker official support</h2>
        <p>
        Please visit the <a href="http://help.websitebaker.org/" target="_blank">official WebsiteBaker Help site</a>
         to learn more about WebsiteBaker. If you have any problems or questions, please do not hesitate to visit the&nbsp;<a href="http://forum.websitebaker.org/" target="_blank"> WebsiteBaker Forum</a>.
         </p>
        <br />
        <hr />
        <br />
        <h2>WebsiteBaker Add-ons (modules and templates)</h2>
        <p>There exists numerous WebsiteBaker templates, designs, modules and add-ons. Check out the <a href="http://addons.websitebaker.org/" target="_blank">official WebsiteBaker Add-ons Repository</a> to find what you need.</p>
        <br />
        <hr />
        <br />
        <fieldset>
            <legend>WebsiteBaker Portable</legend>
            <ul>
            <li><a target="_blank" href="http://localhost:4001/wbdemo/"><strong>Direct access to the frontend </strong></a> (visitors)</li>
            <li><a target="_blank" href="http://localhost:4001/wbdemo/admin/"><strong>Direct access to the backend</strong></a> (admin)<br /><br />
                 <b>Loginname: </b>admin<br />
                 <b>Password: </b>admin</li>
            </ul>
        </fieldset>
        <br />
        <hr />
        <br />
        <h2>About USBWebserver8</h2>
        <p>You can display the USBWebserver8 WAMP Infos (e.g. phpinfo) <a href="http://localhost:4001/docs/usbw-info/" target="_blank">by clicking here!</a><br />
        USBWebserver-manual: <a href="http://www.usbwebserver.net/downloads/manual.pdf" target="_blank" title="USBWebserver8 manual">Click!</a><br />
        Access to database via <b>phpMyAdmin</b>: <a href="http://localhost:4001/phpmyadmin" target="_blank">Click!</a><br /><br /></p>
        <hr />
        <br />
        <h2><span>Thanks to:</span></h2>
        <p>The <b>USBWebserver8</b> developer, <br />who makes WebsiteBaker Portable Edition feasible at all.</p>
        <p>Christian Sommer (aka doc) for the initial idea.<br />
        Graphics and logo design by Christian M. Stefan  (aka Stefek).</p>
        <p>The <b>WebsiteBaker Community</b> for the support and encouragement to make this project happen.</p>
        <p>All the people who supported this project, especially Martin Freudenreich (aka mr-fan) for starting this portable project .</p>
        <p>Have fun baking!<br />
        <strong>WebsiteBaker Org e.V.</strong></p>

    <?php
include ("foot.php");
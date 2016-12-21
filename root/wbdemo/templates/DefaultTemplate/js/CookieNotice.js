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
 *//** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * CookieNotice
 *
 * @category     Template
 * @copyright    Manuela v.d.Decken <manuela@isteam.de>
 * @author       Manuela v.d.Decken <manuela@isteam.de>
 * @license      http://www.gnu.org/licenses/gpl.html   GPL License
 * @version      0.0.1
 * @lastmodified 19.09.2015
 * @since        File available since 04.07.2015
 * @description  switch off the cookie notice for n days
    (by default after 7 days the cookie will be removed again)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

    function CookieNotice(NoticeAreaId) {

        var CookieLifetime = 7; // lifetime of the cookie in days
        var thisObject = this;
        this.Box = document.getElementById(NoticeAreaId);

        this.hideNotice = function(e) {
            e = e || window.event;
            var target = e.target || e.srcElement;
            if (target.id == 'CookieNoticeClose') {
                thisObject.Box.style.display = 'none';
                thisObject.setCookie("CookieNoticeVisible", "none", CookieLifetime);
                if (e.stopPropagation) {
                    e.stopPropagation();
                } else {
                    e.cancelBubble = true;
                }
            }
        };

        this.setCookie = function(cname,cvalue,exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*86400000));
            var expires = "expires=" + d.toGMTString();
             document.cookie = cname+"="+cvalue+"; path=/; "+expires;
        };

        this.getCookie = function(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i=0; i<ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1);
                if (c.indexOf(name) === 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        };

        this.start = function() {
            var value = thisObject.getCookie('CookieNoticeVisible');
            if (value != 'none') {
                thisObject.Box.style.display = 'block';
            }
        };
        this.Box.onclick = thisObject.hideNotice;
        this.start();
    }/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    show the cookie notice if needed
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
    var watchNotice = new CookieNotice('CookieNotice');
//    new CookieNotice('CookieNotice');





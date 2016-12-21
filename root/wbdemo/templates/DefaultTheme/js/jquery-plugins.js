    function include_file(filename, filetype) {
        if(!filetype)
            var filetype = 'js'; //js default filetype
        var th = document.getElementsByTagName('head')[0];
        var s  = document.createElement((filetype == "js") ? 'script' : 'link');
        s.setAttribute('type',(filetype == "js") ? 'text/javascript' : 'text/css');
        if (filetype == "css")
            s.setAttribute('rel','stylesheet');
        s.setAttribute((filetype == "js") ? 'src' : 'href', filename);
        th.appendChild(s);
    }

    function redirect_to_page (url, timer) {
        if (timer < 0 ) {
            self.location.replace (url);
        } else {
            setTimeout('self.location.href="'+url+'"', timer);
        }
    }

function replaceAllBackSlash(targetStr){
      var index=targetStr.indexOf("\\");
      while(index >= 0){
          targetStr=targetStr.replace("\\","");
          index=targetStr.indexOf("\\");
      }
      return targetStr;
  }

    function loadErrorFile() {
        var errorModal = $('#errorModal');
//console.info(errorModal);
        $('#ErrorIFrameBox').load( (THEME_URL+"/LoadErrorlog.php") )
    }

    function delete_error_log(ev){
        var errorModal = $('#errorModal');
        var iframe = $('#ErrorIFrameBox');
        var action = '0';
        var img = $('#deleteImg');
//console.info(iframe);
//console.info(img);
        if( img.attr("src") == THEME_URL+"images/remove_0.png") {
            var action = "show";
            var src = THEME_URL+"images/remove_1.png";
        } else {
            var action = "0";
            var src = THEME_URL+"images/remove_0.png";
        }
       $.ajax({
            url: THEME_URL+"/delete_errorlog.php",
            type: "POST",
            data: 'action='+action,
            dataType: 'json',
            success: function(data) {
                if(data.success == "true") {
                    $('#ErrorIFrameBox').empty();
                    $('#ErrorIFrameBox').load(THEME_URL+"/LoadErrorlog.php")
                    src = THEME_URL+'/images/remove_0.png'
                    img.attr("src", src);
                    img.attr("title", data.message);
                } else {
                    alert(data.message);
                }
            },
            complete: function() {}
        });
    }
/*
*/
    domReady(function(){

//        var matches = document.querySelectorAll(".jcalendar");
        if( document.querySelectorAll(".jcalendar").length > 0 ) {
            var JCalendarCss = WB_URL+"/include/jscalendar/calendar-system.css";
            if (typeof LoadOnFly ==='undefined'){
              $.insert(JCalendarCss);
            } else {
              LoadOnFly('head', JCalendarCss);
            }
        }

        if( document.querySelectorAll(".jsadmin").length > 0 ) {
            var JsAdminCss = WB_URL+"/modules/jsadmin/backend.css";
            if (typeof LoadOnFly ==='undefined'){
              $.insert(JsAdminCss);
            } else {
              LoadOnFly('head', JsAdminCss);
            }
        }

        elm = document.getElementsByTagName('form');
//console.info(elm);
          for (i=0; elm[i]; i++) {
            if ( (elm[i].className.indexOf('autocomplete') == -1) ) {
                elm[i].setAttribute('autocomplete', 'off');
            }
            if ( (elm[i].className.indexOf('accept-charset') == -1) ) {
                elm[i].setAttribute('accept-charset', 'utf-8');
            }
          }
/*
*/
        var errorlog = document.getElementById('delete_php_error-log');
            errorlog.addEventListener('click', delete_error_log);

/**
 *
var frm = document.getElementsByName("preferences_save");
console.info(frm);
   frm.reset();  // Reset

   //Add external link class to external links -
   $('a[href^="http://"]').filter(function() {
      //Compare the anchor tag's host name with location's host name
       return this.hostname && this.hostname !== location.hostname;
     }).addClass("external").attr("target", "_blank");

   //* Add internal link class to external links -
   $('a[href^="http://"]').filter(function() {
      //Compare the anchor tag's host name with location's host name
       return this.hostname && this.hostname == location.hostname;
     }).addClass("internal");
   $('form').attr('autocomplete', 'off');
 */

    });

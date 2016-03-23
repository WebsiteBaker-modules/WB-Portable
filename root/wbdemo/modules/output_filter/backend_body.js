/**
 * 
 */
domReady(function() {


    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }

    function initCheckboxes() {
        addEvent(document.getElementById('select_all'), 'click', setCheckboxes);
    }
    function setCheckboxes() {
        var cb = document.getElementById( 'cb-filters' ).getElementsByTagName('input');
console.info(cb);
        var isChecked = document.getElementById('select_all').checked;
        for (var i = 0; i < cb.length; i++) {
            cb[i].checked = isChecked;
        }
    }

    function selectSingleElement(IdSuffix, el ) {
        document.getElementById(el.id + IdSuffix).checked ='checked'; 
        document.getElementById('select_all').checked =false;
    }

    function deselectAllElements(IdSuffix, el ) {
        for ( i = 0;; i++) {
            if (!(e = document.getElementById('L' + i + IdSuffix))) {
                break;
            }
            e.checked = el.checked;
        }
    }





    var smtpPort = document.getElementById("wbmailer_smtp_port");
    if ( smtpPort ){
        smtpPort.addEventListener("click", function() {
            toggle_wbmailer_secure( smtpPort );
        }, false);
    }

});

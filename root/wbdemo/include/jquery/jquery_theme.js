var JQUERY_THEME = WB_URL+'/include/jquery';

$(document).ready(function() {
        if (typeof LoadOnFly ==="undefined"){
            LoadOnFly( 'head', JQUERY_THEME+'/jquery-ui.css' );
        } else {
            $.insert(JQUERY_THEME+'/jquery-ui.css' );
        }
        $.include( JQUERY_THEME+'/jquery-ui-min.js');
//        LoadOnFly( 'body', JQUERY_THEME+'/jquery-ui-min.js' );
});

function change_os(type) {
    if(type === 'linux') {
        document.getElementById('file_perms_box1').style.display = 'block';
console.info(type);
        document.getElementById('file_perms_box2').style.display = 'block';
        document.getElementById('file_perms_box3').style.display = 'block';
    } else if(type === 'windows') {
        document.getElementById('file_perms_box1').style.display = 'none';
console.info(type);
        document.getElementById('file_perms_box2').style.display = 'none';
        document.getElementById('file_perms_box3').style.display = 'none';
    }
}

function change_wbmailer(type) {
    if(type === 'smtp') {
        document.getElementById('row_wbmailer_smtp_settings').style.display = '';
        document.getElementById('row_wbmailer_smtp_host').style.display = '';
        document.getElementById('row_wbmailer_smtp_auth_mode').style.display = '';
        document.getElementById('row_wbmailer_smtp_username').style.display = '';
        document.getElementById('row_wbmailer_smtp_password').style.display = '';
    } else if(type === 'phpmail') {
        document.getElementById('row_wbmailer_smtp_settings').style.display = 'none';
        document.getElementById('row_wbmailer_smtp_host').style.display = 'none';
        document.getElementById('row_wbmailer_smtp_auth_mode').style.display = 'none';
        document.getElementById('row_wbmailer_smtp_username').style.display = 'none';
        document.getElementById('row_wbmailer_smtp_password').style.display = 'none';
    }
}
/*  */
function toggle_wbmailer_auth() {
        document.getElementById('row_wbmailer_smtp_username').style.display = '';
        document.getElementById('row_wbmailer_smtp_password').style.display = '';
        document.settings.wbmailer_smtp_auth.checked == true;
/*
    if( document.settings.wbmailer_smtp_auth.checked == true ) {
        document.getElementById('row_wbmailer_smtp_username').style.display = '';
        document.getElementById('row_wbmailer_smtp_password').style.display = '';
    } else {
        document.getElementById('row_wbmailer_smtp_username').style.display = 'none';
        document.getElementById('row_wbmailer_smtp_password').style.display = 'none';
    }
*/
}
domReady(function() {

    var system_linux = document.getElementById("operating_system_linux");
    if ( system_linux ){
        system_linux.addEventListener("click", function() {
            change_os( 'linux' );
        }, false);
    }

    var system_windows = document.getElementById("operating_system_windows");
    if ( system_windows ){
        system_windows.addEventListener("click", function() {
            change_os( 'windows' );
        }, false);
    }

    var phpmail = document.getElementById("wbmailer_routine_phpmail");
    if ( phpmail ){
        phpmail.addEventListener("click", function() {
            change_wbmailer( 'phpmail' );
        }, false);
    }

    var smtp = document.getElementById("wbmailer_routine_smtp");
    if ( smtp ){
        smtp.addEventListener("click", function() {
            change_wbmailer( 'smtp' );
        }, false);
    }

    var redirecttimer = document.getElementById("redirect_timer"),
        resRedirectTimer = document.getElementById("RedirectTimer");
    if ( redirecttimer ){
        redirecttimer.addEventListener("input", function() {
            resRedirectTimer.innerHTML = redirecttimer.value;
        }, false);
    }

    var page_level = document.getElementById("page_level_limit"),
        resPageLevel = document.getElementById("PageLevelLimit");
    if ( page_level ){
        page_level.addEventListener("input", function() {
            resPageLevel.innerHTML = page_level.value;
        }, false);
    }

    var netmask4 = document.getElementById("sec_token_netmask4"),
        resNetmask4 = document.getElementById("SecTokenNetmask4");
    if ( netmask4 ){
        netmask4.addEventListener("input", function() {
            resNetmask4.innerHTML = netmask4.value;
        }, false);
    }

    var netmask6 = document.getElementById("sec_token_netmask6"),
        resNetmask6 = document.getElementById("SecTokenNetmask6");
    if ( netmask6 ){
        netmask6.addEventListener("input", function() {
            resNetmask6.innerHTML = netmask6.value;
        }, false);
    }

    var LifeTime = document.getElementById("sec_token_life_time"),
        resLifeTime = document.getElementById("SecTokenLifeTime");
    if ( LifeTime ){
        LifeTime.addEventListener("input", function() {
            if ( LifeTime.value > 120 ){
              resLifeTime.innerHTML = LifeTime.value / 60;
console.info('Line 113: '+LifeTime.value);
            } else {
              resLifeTime.innerHTML = LifeTime.value;
console.info(LifeTime.value);
            }
        }, false);
    }

    var MaxExcerpt = document.getElementById("search_max_excerpt"),
        resMaxExcerpt= document.getElementById("SearchMaxExcerpt");
    if ( MaxExcerpt ){
        MaxExcerpt.addEventListener("input", function() {
            resMaxExcerpt.innerHTML = MaxExcerpt.value;
        }, false);
    }

    var TimeLimit = document.getElementById("search_time_limit"),
        resTimeLimit= document.getElementById("SearchTimeLimit");
    if ( TimeLimit ){
        TimeLimit.addEventListener("input", function() {
            resTimeLimit.innerHTML = TimeLimit.value;
        }, false);
    }

});



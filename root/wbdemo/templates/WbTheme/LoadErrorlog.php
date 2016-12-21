<?php
/**
 * LoadErrorlog.php
 */

    $sAppPath = dirname(dirname(__DIR__));
    if (is_readable($sAppPath.'/config.php')) {require ($sAppPath.'/config.php');}
    // An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE.
    $aRequestVars = $_REQUEST;
    $sErrorlogFile = WB_PATH.'/var/logs/php_error.log';
    $sErrorlogUrl  = WB_URL .'/var/logs/php_error.log';
    $aJsonRespond['url'] = $sErrorlogUrl;
    // initialize json_respond array  (will be sent back)
    $aJsonRespond = array();
    $aJsonRespond['content'] = array();
    $aJsonRespond['message'] = 'Load operation failed';
    $aJsonRespond['success'] = false;

    if (!($aJsonRespond['content'] = file($sErrorlogFile, FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES|FILE_TEXT))){
        exit(json_encode($aJsonRespond));
    }

      // If the script is still running, set success to true
      $aJsonRespond['success'] = 'true';
// and echo the answer as json to the ajax function
echo json_encode( (implode('<br />', $aJsonRespond['content'])));

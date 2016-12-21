<?php

    $sAppPath = dirname(dirname(__DIR__));
    if (is_readable($sAppPath.'/config.php')) {require ($sAppPath.'/config.php');}
    if (!function_exists('rm_full_dir')){require (WB_PATH.'/framework/functions.php');}
    // An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE.
    $aRequestVars = $_REQUEST;
    $sErrorlogFile = WB_PATH.'/var/logs/php_error.log';
    $sErrorlogUrl  = WB_URL .'/var/logs/php_error.log';
    $aJsonRespond['url'] = $sErrorlogUrl;
    // initialize json_respond array  (will be sent back)
    $aJsonRespond = array();
    $aJsonRespond['content'] = '';
    $aJsonRespond['message'] = 'Load operation failed';
    $aJsonRespond['success'] = false;
    if(!isset($aRequestVars['action']) )
    {
        $aJsonRespond['message'] = '"action" was not set';
        exit(json_encode($aJsonRespond));
    } elseif ($aRequestVars['action']=='show') {
          $aJsonRespond['content'] = file_get_contents($sErrorlogFile);
    } else {
        if (is_writeable($sErrorLogFile)) {
          if (!rm_full_dir($sErrorLogFile, true)){
              $aJsonRespond['message'] = "can't delete from folder";
              exit(json_encode($aJsonRespond));
          }
          if (!file_exists($sErrorLogFile)) {
              file_put_contents($sErrorLogFile, 'created: ['.date('c').']'.PHP_EOL, FILE_APPEND);
          }
          $aJsonRespond['message'] = 'New php_error.log successfully created';
          $aJsonRespond['content'] = file_get_contents($sErrorlogFile);
        }
    }
// If the script is still running, set success to true
$aJsonRespond['success'] = 'true';
// and echo the answer as json to the ajax function
echo json_encode($aJsonRespond);

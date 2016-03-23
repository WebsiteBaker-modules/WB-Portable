//:Online Besucher Statistik
//:use [[WBCount]]
$retVal='';
ob_start();
if( is_readable( WB_PATH.'/modules/wbCounterView/view.php' )) { include ( WB_PATH.'/modules/wbCounterView/view.php' ); }
$retVal = ob_get_clean();
return $retVal;